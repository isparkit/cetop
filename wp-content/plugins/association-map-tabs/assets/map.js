document.addEventListener("DOMContentLoaded", function () {

  if (!window.AMT_MAP_DATA || !document.getElementById("amt-map")) return;



  let mapInstance = null;

  let markers = [];



  /**

   * Activate a specific tab and highlight corresponding map marker

   */

  function activateTab(id) {

    // Update tab buttons

    document.querySelectorAll(".amt-tab-btn").forEach((btn) => {

      btn.classList.toggle("active", btn.dataset.id == id);

    });



    // Update content sections

    document.querySelectorAll(".amt-content").forEach((box) => {

      box.classList.toggle("active", box.dataset.id == id);

    });



    // Highlight marker on map if it exists

    highlightMarker(id);

  }



  /**

   * Highlight a marker on the map

   */

  function highlightMarker(id) {

    markers.forEach((markerData) => {

      if (markerData.marker) {

        if (markerData.id == id) {

          markerData.marker.setZIndex(google.maps.Marker.MAX_ZINDEX + 1);

          markerData.marker.setIcon(createCustomMarker(true));

          // Pan to marker smoothly

          if (mapInstance) {

            mapInstance.panTo(markerData.marker.getPosition());

          }

        } else {

          markerData.marker.setZIndex(0);

          markerData.marker.setIcon(createCustomMarker(false));

        }

      }

    });

  }



  /**

   * Create custom SVG marker icon

   */

  function createCustomMarker(isActive = false) {



  const fillColor = isActive ? "#0d5a28" : "#1a7f37";

  const strokeColor = "#ffffff";



  const svg = `

  <svg xmlns="http://www.w3.org/2000/svg" width="75" height="96" viewBox="0 0 75 96" fill="none">

<path d="M31.7254 24.6989C33.3887 23.6004 35.3444 23.014 37.3449 23.014C40.0275 23.014 42.6003 24.0674 44.4972 25.9422C46.3941 27.8171 47.4598 30.3601 47.4598 33.0116C47.4598 34.9889 46.8666 36.9218 45.7551 38.5659C44.6437 40.21 43.064 41.4914 41.2157 42.2481C39.3674 43.0048 37.3337 43.2028 35.3716 42.817C33.4095 42.4313 31.6072 41.4791 30.1926 40.0809C28.778 38.6827 27.8146 36.9013 27.4243 34.962C27.0341 33.0227 27.2344 31.0125 27.9999 29.1857C28.7655 27.3589 30.062 25.7975 31.7254 24.6989Z" fill="#EEEEF1"/>

<g filter="url(#filter0_d_1114_4105)">

<path d="M26.8906 4.83557C31.4782 3.17167 36.405 2.6279 41.251 3.24963C46.0969 3.87138 50.719 5.64058 54.7246 8.40686C58.7303 11.1732 62.0016 14.8545 64.2588 19.1383C66.5159 23.422 67.6926 28.1816 67.6895 33.0114C67.6662 38.9511 65.859 44.751 62.4971 49.6744L37.3447 82.9996L14.2148 52.4069C11.0518 48.7255 8.83492 44.3433 7.75293 39.6334C6.67094 34.9235 6.7565 30.0247 8.00098 25.3541C9.24551 20.6835 11.6125 16.3785 14.9014 12.8063C18.1902 9.23409 22.3032 6.49948 26.8906 4.83557ZM37.3447 23.0143C35.3442 23.0143 33.388 23.6003 31.7246 24.6989C30.0613 25.7974 28.7646 27.3594 27.999 29.1862C27.2336 31.0128 27.0336 33.0234 27.4238 34.9625C27.8142 36.9017 28.7779 38.6826 30.1924 40.0807C31.607 41.4788 33.409 42.4313 35.3711 42.817C37.3331 43.2028 39.3666 43.0043 41.2148 42.2477C43.0631 41.491 44.6434 40.2101 45.7549 38.566C46.8663 36.922 47.459 34.9886 47.459 33.0114C47.4589 30.36 46.3939 27.8168 44.4971 25.942C42.6002 24.0672 40.0273 23.0144 37.3447 23.0143Z" fill="white"/>

<path d="M26.8906 4.83557L25.8677 2.01534L25.8677 2.01535L26.8906 4.83557ZM41.251 3.24963L41.6328 0.274026L41.6327 0.274024L41.251 3.24963ZM54.7246 8.40686L56.4294 5.93832L56.4294 5.93831L54.7246 8.40686ZM64.2588 19.1383L66.9129 17.7398L66.9129 17.7398L64.2588 19.1383ZM67.6895 33.0114L70.6894 33.0231L70.6895 33.0133L67.6895 33.0114ZM62.4971 49.6744L64.8916 51.4817L64.9345 51.4249L64.9746 51.3662L62.4971 49.6744ZM37.3447 82.9996L34.9517 84.8089L37.3468 87.9768L39.7393 84.8069L37.3447 82.9996ZM14.2148 52.4069L16.6079 50.5976L16.5514 50.5229L16.4903 50.4518L14.2148 52.4069ZM7.75293 39.6334L4.82909 40.3051L4.82909 40.3051L7.75293 39.6334ZM8.00098 25.3541L5.10212 24.5817L5.10212 24.5817L8.00098 25.3541ZM14.9014 12.8063L12.6943 10.7743L12.6943 10.7743L14.9014 12.8063ZM37.3447 23.0143L37.3448 20.0143H37.3447V23.0143ZM31.7246 24.6989L30.0714 22.1955L30.0713 22.1955L31.7246 24.6989ZM27.999 29.1862L25.2322 28.0266L25.2321 28.0268L27.999 29.1862ZM27.4238 34.9625L24.4828 35.5544L24.4828 35.5545L27.4238 34.9625ZM30.1924 40.0807L28.0835 42.2144L28.0835 42.2144L30.1924 40.0807ZM35.3711 42.817L34.7923 45.7607L34.7924 45.7607L35.3711 42.817ZM41.2148 42.2477L42.3514 45.024L42.3515 45.024L41.2148 42.2477ZM45.7549 38.566L48.2402 40.2462L48.2402 40.2462L45.7549 38.566ZM47.459 33.0114H50.459V33.0113L47.459 33.0114ZM44.4971 25.942L46.606 23.8083L46.606 23.8083L44.4971 25.942ZM26.8906 4.83557L27.9135 7.6558C32.0511 6.15509 36.4963 5.66421 40.8692 6.22524L41.251 3.24963L41.6327 0.274024C36.3136 -0.408408 30.9052 0.18826 25.8677 2.01534L26.8906 4.83557ZM41.251 3.24963L40.8692 6.22524C45.2417 6.78625 49.4099 8.38238 53.0198 10.8754L54.7246 8.40686L56.4294 5.93831C52.0281 2.89879 46.952 0.95651 41.6328 0.274026L41.251 3.24963ZM54.7246 8.40686L53.0198 10.8754C56.6299 13.3686 59.5745 16.6837 61.6047 20.5368L64.2588 19.1383L66.9129 17.7398C64.4288 13.0253 60.8307 8.97786 56.4294 5.93832L54.7246 8.40686ZM64.2588 19.1383L61.6047 20.5368C63.6348 24.3897 64.6922 28.6687 64.6895 33.0094L67.6895 33.0114L70.6895 33.0133C70.6929 27.6944 69.397 22.4543 66.9129 17.7398L64.2588 19.1383ZM67.6895 33.0114L64.6895 32.9996C64.6686 38.3374 63.0447 43.5526 60.0196 47.9827L62.4971 49.6744L64.9746 51.3662C68.6733 45.9495 70.6638 39.5648 70.6894 33.0231L67.6895 33.0114ZM62.4971 49.6744L60.1025 47.8672L34.9502 81.1924L37.3447 82.9996L39.7393 84.8069L64.8916 51.4817L62.4971 49.6744ZM37.3447 82.9996L39.7378 81.1904L16.6079 50.5976L14.2148 52.4069L11.8218 54.2161L34.9517 84.8089L37.3447 82.9996ZM14.2148 52.4069L16.4903 50.4518C13.6428 47.1376 11.6494 43.1954 10.6768 38.9617L7.75293 39.6334L4.82909 40.3051C6.02047 45.4912 8.46079 50.3133 11.9394 54.3619L14.2148 52.4069ZM7.75293 39.6334L10.6768 38.9618C9.70427 34.7284 9.78106 30.3254 10.8998 26.1265L8.00098 25.3541L5.10212 24.5817C3.73193 29.7241 3.63762 35.1185 4.82909 40.3051L7.75293 39.6334ZM8.00098 25.3541L10.8998 26.1266C12.0187 21.9276 14.1474 18.0544 17.1084 14.8383L14.9014 12.8063L12.6943 10.7743C9.0776 14.7026 6.47235 19.4394 5.10212 24.5817L8.00098 25.3541ZM14.9014 12.8063L17.1084 14.8383C20.0698 11.6217 23.7761 9.1565 27.9135 7.65579L26.8906 4.83557L25.8677 2.01535C20.8303 3.84246 16.3106 6.84648 12.6943 10.7743L14.9014 12.8063ZM37.3447 23.0143V20.0143C34.7591 20.0143 32.2275 20.7715 30.0714 22.1955L31.7246 24.6989L33.3779 27.2022C34.5485 26.4291 35.9293 26.0143 37.3447 26.0143V23.0143ZM31.7246 24.6989L30.0713 22.1955C27.9151 23.6196 26.229 25.648 25.2322 28.0266L27.999 29.1862L30.7659 30.3457C31.3001 29.0709 32.2075 27.9751 33.3779 27.2022L31.7246 24.6989ZM27.999 29.1862L25.2321 28.0268C24.2351 30.4061 23.9741 33.0267 24.4828 35.5544L27.4238 34.9625L30.3649 34.3706C30.0931 33.0201 30.2321 31.6195 30.7659 30.3455L27.999 29.1862ZM27.4238 34.9625L24.4828 35.5545C24.9917 38.0828 26.2473 40.3995 28.0835 42.2144L30.1924 40.0807L32.3013 37.947C31.3085 36.9658 30.6366 35.7205 30.3648 34.3705L27.4238 34.9625ZM30.1924 40.0807L28.0835 42.2144C29.9196 44.0291 32.2547 45.2617 34.7923 45.7607L35.3711 42.817L35.9498 39.8734C34.5634 39.6008 33.2943 38.9285 32.3013 37.947L30.1924 40.0807ZM35.3711 42.817L34.7924 45.7607C37.33 46.2596 39.9599 46.0031 42.3514 45.024L41.2148 42.2477L40.0783 39.4713C38.7734 40.0055 37.3363 40.1459 35.9498 39.8734L35.3711 42.817ZM41.2148 42.2477L42.3515 45.024C44.7431 44.0449 46.7945 42.3848 48.2402 40.2462L45.7549 38.566L43.2695 36.8859C42.4924 38.0355 41.3831 38.9371 40.0782 39.4713L41.2148 42.2477ZM45.7549 38.566L48.2402 40.2462C49.6862 38.1074 50.459 35.5891 50.459 33.0114H47.459H44.459C44.459 34.3881 44.0465 35.7366 43.2695 36.8859L45.7549 38.566ZM47.459 33.0114L50.459 33.0113C50.4589 29.5535 49.0697 26.2434 46.606 23.8083L44.4971 25.942L42.3882 28.0757C43.7182 29.3902 44.4589 31.1664 44.459 33.0114L47.459 33.0114ZM44.4971 25.942L46.606 23.8083C44.1436 21.3746 40.8113 20.0144 37.3448 20.0143L37.3447 23.0143L37.3447 26.0143C39.2432 26.0143 41.0568 26.7598 42.3882 28.0757L44.4971 25.942Z" fill="#00963E"/>

</g>

<defs>

<filter id="filter0_d_1114_4105" x="0" y="0" width="74.6895" height="95.9768" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">

<feFlood flood-opacity="0" result="BackgroundImageFix"/>

<feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>

<feOffset dy="4"/>

<feGaussianBlur stdDeviation="2"/>

<feComposite in2="hardAlpha" operator="out"/>

<feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/>

<feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_1114_4105"/>

<feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_1114_4105" result="shape"/>

</filter>

</defs>

</svg>

  `;



  return {

    url: "data:image/svg+xml;charset=UTF-8," + encodeURIComponent(svg),

    scaledSize: new google.maps.Size(48, 56),

    anchor: new google.maps.Point(24, 56)

  };

}





  /**

   * Initialize Google Map with markers

   */

  function initMap() {

    const first = AMT_MAP_DATA[0];



    mapInstance = new google.maps.Map(document.getElementById("amt-map"), {

      zoom: 4,

      center: { lat: +first.lat, lng: +first.lng },

      gestureHandling: "cooperative",

      mapTypeId: google.maps.MapTypeId.ROADMAP,

      styles: [

        {

          featureType: "poi",

          elementType: "labels",

          stylers: [{ visibility: "off" }],

        },

        {

          featureType: "administrative",

          elementType: "geometry.stroke",

          stylers: [{ color: "#e0e0e0" }, { weight: 1 }],

        },

        {

          featureType: "water",

          elementType: "geometry.fill",

          stylers: [{ color: "#f0f8ff" }],

        },

      ],

    });



    AMT_MAP_DATA.forEach((item, index) => {

      if (!item.lat || !item.lng) return;



      const marker = new google.maps.Marker({

        position: { lat: +item.lat, lng: +item.lng },

        map: mapInstance,

        title: item.title,

        animation: google.maps.Animation.DROP,

        icon: createCustomMarker(false),

        zIndex: index,

      });



      markers.push({ id: item.id, marker: marker });



      // Click handler for marker

      marker.addListener("click", () => {

        activateTab(item.id);

        // Smooth scroll to tabs

        document

          .querySelector(".amt-tabs-sidebar")

          .scrollIntoView({ behavior: "smooth", block: "nearest" });

      });



      // Hover effect

      marker.addListener("mouseover", () => {

        marker.setZIndex(google.maps.Marker.MAX_ZINDEX);

      });



      marker.addListener("mouseout", () => {

        const activeId = document.querySelector(".amt-tab-btn.active")?.dataset

          .id;

        if (activeId != item.id) {

          marker.setZIndex(0);

        }

      });

    });



    // Activate first tab by default

    if (AMT_MAP_DATA.length > 0) {

      activateTab(AMT_MAP_DATA[0].id);

    }

  }



  // Add click event listeners to tab buttons

  document.querySelectorAll(".amt-tab-btn").forEach((btn) => {

    btn.addEventListener("click", () => {

      activateTab(btn.dataset.id);

    });



    // Keyboard navigation

    btn.addEventListener("keydown", (e) => {

      if (e.key === "Enter" || e.key === " ") {

        e.preventDefault();

        activateTab(btn.dataset.id);

      }

    });

  });



  // Wait for Google Maps script to load

  const interval = setInterval(() => {

    if (window.google && window.google.maps) {

      clearInterval(interval);

      initMap();

    }

  }, 300);

});

