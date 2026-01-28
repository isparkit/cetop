<?php



/**

 * Plugin Name: Association Map with Tabs

 * Description: Google Map markers synced with tabs using CPT + ACF.

 * Version: 1.1

 */



if (!defined('ABSPATH')) exit;



/* --------------------------------------------------

 * 1. Register CPT

 * -------------------------------------------------- */

function amt_register_cpt()

{

    register_post_type('associations', [

        'label'        => 'Associations',

        'public'       => true,

        'menu_icon'    => 'dashicons-location-alt',

        'supports'     => ['title', 'editor', 'thumbnail'],

        'show_in_rest' => true,

    ]);
}

add_action('init', 'amt_register_cpt');





/* --------------------------------------------------

 * 2. Settings: Google Map API Key

 * -------------------------------------------------- */

function amt_register_settings()

{

    register_setting('amt_settings_group', 'amt_google_map_api_key');
}

add_action('admin_init', 'amt_register_settings');



function amt_settings_menu()

{

    add_options_page(

        'Association Map Settings',

        'Association Map',

        'manage_options',

        'amt-settings',

        'amt_settings_page_html'

    );
}

add_action('admin_menu', 'amt_settings_menu');



function amt_settings_page_html()

{ ?>

    <div class="wrap">
        <h1>Association Map Settings</h1>
        <form method="post" action="options.php">

            <?php settings_fields('amt_settings_group'); ?>

            <table class="form-table">

                <tr>

                    <th>Google Maps API Key</th>

                    <td>

                        <input type="password"

                            name="amt_google_map_api_key"

                            value="<?php echo esc_attr(get_option('amt_google_map_api_key')); ?>"

                            style="width:400px;">

                    </td>

                </tr>

            </table>

            <?php submit_button(); ?>

        </form>
    </div>
    <div class="copy-shortcode">
        <code>[association_map]</code>
        <button type="button">Copy</button>
    </div>



<?php }





/* --------------------------------------------------

 * 3. Enqueue Assets

 * -------------------------------------------------- */

function amt_enqueue_assets()

{



    // sirf jab shortcode use ho tab bhi chal sakta hai,

    // par abhi simple rakhte hain

    wp_enqueue_style(

        'amt-style',

        plugin_dir_url(__FILE__) . 'assets/style.css'

    );



    wp_enqueue_script(

        'amt-map',

        plugin_dir_url(__FILE__) . 'assets/map.js',

        [],

        '1.1',

        true

    );



    $api_key = get_option('amt_google_map_api_key');



    if ($api_key) {

        wp_enqueue_script(

            'google-map',

            'https://maps.googleapis.com/maps/api/js?key=' . esc_attr($api_key),

            [],

            null,

            true

        );
    }
}

add_action('wp_enqueue_scripts', 'amt_enqueue_assets');





/* --------------------------------------------------

 * 4. Shortcode [association_map]

 * -------------------------------------------------- */

function amt_map_shortcode()

{



    $posts = get_posts([

        'post_type'   => 'associations',

        'numberposts' => -1,

        'post_status' => 'publish'

    ]);



    if (!$posts) {

        return '<p>No associations found.</p>';
    }



    ob_start(); ?>



    <!-- MAP -->

    <div id="amt-map" style="height:500px;"></div>



    <!-- TABS -->

    <div class="amt-container">

        <div class="amt-tabs-sidebar">

            <ul>

                <?php foreach ($posts as $p): ?>
                    <?php $country = get_field('country', $p->ID); ?>


                    <li class="amt-tab-btn" data-id="<?php echo esc_attr($p->ID); ?>">
                        <?php echo esc_html($country); ?> : <?php echo esc_html(get_the_title($p->ID)); ?>
                        
                    </li>



                <?php endforeach; ?>

            </ul>

        </div>



        <!-- CONTENT -->

        <div class="amt-contents">

            <?php foreach ($posts as $p):

                // ACF DATA

                $country = get_field('country', $p->ID);

                $address = get_field('address', $p->ID);

                $phone   = get_field('phone', $p->ID);

                $email   = get_field('email', $p->ID);

                $website = get_field('website', $p->ID);

                $featured_img = get_the_post_thumbnail_url($p->ID);

            ?>



                <div class="amt-content" data-id="<?php echo esc_attr($p->ID); ?>">

                    <div class="company-header">

                        <?php if ($featured_img): ?>

                            <div class="amt-featured-img">

                                <img src="<?php echo esc_url($featured_img); ?>" alt="">

                            </div>

                        <?php endif; ?>



                        <h3><?php echo esc_html(get_the_title($p->ID)); ?></h3>

                        <ul class="amt-meta">

                            <?php if ($country): ?>

                                <li><strong>Country:</strong> <?php echo esc_html($country); ?></li>

                            <?php endif; ?>



                            <?php if ($address): ?>

                                <li><strong>Address:</strong> <?php echo esc_html($address); ?></li>

                            <?php endif; ?>



                            <?php if ($phone): ?>

                                <li><strong>Phone:</strong> <?php echo esc_html($phone); ?></li>

                            <?php endif; ?>



                            <?php if ($email): ?>

                                <li><strong>Email:</strong>

                                    <a href="mailto:<?php echo esc_attr($email); ?>">

                                        <?php echo esc_html($email); ?>

                                    </a>

                                </li>

                            <?php endif; ?>



                            <?php if ($website): ?>

                                <li><strong>Website:</strong>

                                    <a href="<?php echo esc_url($website); ?>" target="_blank">

                                        <?php echo esc_html($website); ?>

                                    </a>

                                </li>

                            <?php endif; ?>

                        </ul>

                    </div>

                    <div class="amt-desc">

                        <span class="amt-country"><?php echo esc_html($country); ?></span>

                        <h2><?php echo esc_html(get_the_title($p->ID)); ?></h2>

                        <?php echo apply_filters('the_content', $p->post_content); ?>

                    </div>







                </div>

            <?php endforeach; ?>

        </div>

    </div>

    <?php

    /* -------------------------

   *  Map Data for JS

   * ------------------------- */

    $map_data = [];



    foreach ($posts as $p) {

        $lat = get_field('latitude',  $p->ID);

        $lng = get_field('longitude', $p->ID);



        if (!$lat || !$lng) continue;



        $map_data[] = [

            'id'    => $p->ID,

            'lat'   => $lat,

            'lng'   => $lng,

            'title' => get_the_title($p->ID)

        ];
    }

    ?>



    <script>
        window.AMT_MAP_DATA = <?php echo wp_json_encode($map_data); ?>;
    </script>



<?php

    return ob_get_clean();
}

add_shortcode('association_map', 'amt_map_shortcode');
