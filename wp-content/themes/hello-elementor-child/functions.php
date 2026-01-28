<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if (!function_exists('chld_thm_cfg_locale_css')):
    function chld_thm_cfg_locale_css($uri)
    {
        if (empty($uri) && is_rtl() && file_exists(get_template_directory() . '/rtl.css'))
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter('locale_stylesheet_uri', 'chld_thm_cfg_locale_css');

if (!function_exists('child_theme_configurator_css')):
    function child_theme_configurator_css()
    {
        wp_enqueue_style('chld_thm_cfg_child', trailingslashit(get_stylesheet_directory_uri()) . 'style.css', array('hello-elementor', 'hello-elementor-theme-style', 'hello-elementor-header-footer'));
    }
endif;
add_action('wp_enqueue_scripts', 'child_theme_configurator_css', 10);

// END ENQUEUE PARENT ACTION





/**
 * Events Calendar Shortcode
 * Add this code to your theme's functions.php file
 * Usage: [events_calendar] or [events_calendar limit="5"]
 */

function custom_events_calendar_shortcode($atts)
{
    // Shortcode attributes
    $atts = shortcode_atts(array(
        'limit' => -1, // -1 means show all events
    ), $atts);

    // Get upcoming events
    $args = array(
        'post_type' => 'tribe_events',
        'posts_per_page' => intval($atts['limit']),
        'orderby' => 'event_date',
        'order' => 'ASC',
        'meta_query' => array(
            array(
                'key' => '_EventStartDate',
                'value' => date('Y-m-d H:i:s'),
                'compare' => '>=',
                'type' => 'DATETIME'
            )
        )
    );

    $events = new WP_Query($args);

    // Start output buffering
    ob_start();
?>

   


    <div class="events-container">  
        <div class="events-list">
            <?php
            if ($events->have_posts()) :
                while ($events->have_posts()) : $events->the_post();
                    $event_id = get_the_ID();
                    $start_date = tribe_get_start_date($event_id, false, 'F j');
                    $end_date = tribe_get_end_date($event_id, false, 'j, Y');
                    $venue = tribe_get_venue($event_id);
                    $city = tribe_get_city($event_id);
                    $country = tribe_get_country($event_id);
                    $event_link = tribe_get_event_link($event_id);

                    // Create location string
                    $location = array_filter(array($city, $country));
                    $location_string = implode(', ', $location);

                    // Get calendar links using The Events Calendar functions
                    $google_link = tribe_get_gcal_link($event_id);
                    $ical_link = tribe_get_single_ical_link($event_id);

                    // Get event details for Outlook link
                    $event_title = get_the_title();
                    $event_description = get_the_excerpt();
                    $start_datetime = tribe_get_start_date($event_id, false, 'Y-m-d\TH:i:s');
                    $end_datetime = tribe_get_end_date($event_id, false, 'Y-m-d\TH:i:s');

                    // Create Outlook 365 link
                    $outlook_link = 'https://outlook.office.com/calendar/0/deeplink/compose?path=/calendar/action/compose&rru=addevent';
                    $outlook_link .= '&subject=' . urlencode($event_title);
                    $outlook_link .= '&body=' . urlencode($event_description);
                    $outlook_link .= '&startdt=' . urlencode($start_datetime);
                    $outlook_link .= '&enddt=' . urlencode($end_datetime);
                    if ($location_string) {
                        $outlook_link .= '&location=' . urlencode($location_string);
                    }

                    // Create Outlook Live link
                    $outlook_live_link = 'https://outlook.live.com/calendar/0/deeplink/compose?path=/calendar/action/compose&rru=addevent';
                    $outlook_live_link .= '&subject=' . urlencode($event_title);
                    $outlook_live_link .= '&body=' . urlencode($event_description);
                    $outlook_live_link .= '&startdt=' . urlencode($start_datetime);
                    $outlook_live_link .= '&enddt=' . urlencode($end_datetime);
                    if ($location_string) {
                        $outlook_live_link .= '&location=' . urlencode($location_string);
                    }
            ?>

                    <div class="event-item">
                        <div class="event-info">
                            <div class="event-date">
                                <?php echo $start_date; ?> - <?php echo $end_date; ?>
                            </div>
                            <h2 class="event-title"><?php the_title(); ?></h2>
                            <div class="event-location"><?php echo esc_html($location_string); ?></div>
                        </div>

                        <div class="event-actions">
                            <div class="calendar-dropdown ">
                                <a class="calendar-btn event-btn" type="button">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add to calendar
                                </a>

                                <ul class="calendar-menu">
                                    <li>
                                        <a href="<?php echo esc_url($google_link); ?>" target="_blank" rel="noopener noreferrer">
                                            Google Calendar
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo esc_url($ical_link); ?>">
                                            Apple Calendar (iCal)
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo esc_url($outlook_link); ?>" target="_blank" rel="noopener noreferrer">
                                            Outlook 365
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo esc_url($outlook_live_link); ?>" target="_blank" rel="noopener noreferrer">
                                            Outlook Live
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <a href="<?php echo esc_url($event_link); ?>" class="event-btn">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                visit event page
                            </a>
                        </div>
                    </div>

                <?php
                endwhile;
                wp_reset_postdata();
            else :
                ?>
                <p style="color: white; text-align: center; font-size: 18px;">No upcoming events found.</p>
            <?php endif; ?>
        </div>

        <div class="upcoming-events-btn">
            <a href="<?php echo tribe_get_events_link(); ?>">upcoming events</a>
        </div>
    </div>

    <script>
        document.addEventListener('click', function(e) {
            document.querySelectorAll('.calendar-dropdown').forEach(dropdown => {
                const button = dropdown.querySelector('.calendar-btn');

                if (button && button.contains(e.target)) {
                    dropdown.classList.toggle('open');
                } else {
                    dropdown.classList.remove('open');
                }
            });
        });
    </script>

<?php
    // Return the buffered content
    return ob_get_clean();
}

// Register the shortcode
add_shortcode('events_calendar', 'custom_events_calendar_shortcode');


// Event Detail Page – Add to Calendar
function cetop_event_actions_shortcode() {

    if ( ! function_exists( 'tribe_is_event' ) || ! tribe_is_event() ) {
        return '';
    }

    $event_id = get_the_ID();
    if ( ! $event_id ) {
        return '';
    }

    // TEC links
    $google_cal = function_exists( 'tribe_get_gcal_link' )
        ? tribe_get_gcal_link( $event_id )
        : '';

    $ical_link = function_exists( 'tribe_get_single_ical_link' )
        ? tribe_get_single_ical_link( $event_id )
        : '';

    // Event data for Outlook
    $title       = get_the_title( $event_id );
    $description = wp_strip_all_tags( get_the_excerpt( $event_id ) );
    $start       = tribe_get_start_date( $event_id, false, 'Y-m-d\TH:i:s' );
    $end         = tribe_get_end_date( $event_id, false, 'Y-m-d\TH:i:s' );

    $location_parts = array_filter( array(
        tribe_get_venue( $event_id ),
        tribe_get_city( $event_id ),
        tribe_get_country( $event_id ),
    ) );
    $location = implode( ', ', $location_parts );

    // Outlook Web links
    $outlook_365 = 'https://outlook.office.com/calendar/0/deeplink/compose?path=/calendar/action/compose&rru=addevent'
        . '&subject=' . rawurlencode( $title )
        . '&body=' . rawurlencode( $description )
        . '&startdt=' . rawurlencode( $start )
        . '&enddt=' . rawurlencode( $end )
        . '&location=' . rawurlencode( $location );

    $outlook_live = 'https://outlook.live.com/calendar/0/deeplink/compose?path=/calendar/action/compose&rru=addevent'
        . '&subject=' . rawurlencode( $title )
        . '&body=' . rawurlencode( $description )
        . '&startdt=' . rawurlencode( $start )
        . '&enddt=' . rawurlencode( $end )
        . '&location=' . rawurlencode( $location );

    ob_start();
    ?>
    <div class="event-actions">
        <div class="calendar-dropdown">
            <a class="calendar-btn event-btn" role="button" style="cursor: pointer;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4v16m8-8H4"></path>
                </svg>
                Add to calendar
            </a>

            <ul class="calendar-menu">
                <?php if ( $google_cal ) : ?>
                    <li>
                        <a href="<?php echo esc_url( $google_cal ); ?>" target="_blank" rel="noopener noreferrer">
                            Google Calendar
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ( $ical_link ) : ?>
                    <li>
                        <a href="<?php echo esc_url( $ical_link ); ?>">
                            Apple Calendar (iCal)
                        </a>
                    </li>
                <?php endif; ?>

                <li>
                    <a href="<?php echo esc_url( $outlook_365 ); ?>" target="_blank" rel="noopener noreferrer">
                        Outlook 365
                    </a>
                </li>

                <li>
                    <a href="<?php echo esc_url( $outlook_live ); ?>" target="_blank" rel="noopener noreferrer">
                        Outlook Live
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <?php

    return ob_get_clean();
}
add_shortcode( 'event_actions', 'cetop_event_actions_shortcode' );

add_action('wp_footer', 'script_for_calendar_dropdown');
function script_for_calendar_dropdown() {
    ?>
    <script>
        document.addEventListener('click', function (e) {
            document.querySelectorAll('.calendar-dropdown').forEach(function (dropdown) {
                var button = dropdown.querySelector('.calendar-btn');

                if (button && button.contains(e.target)) {
                    dropdown.classList.toggle('open');
                } else {
                    dropdown.classList.remove('open');
                }
            });
        });
    </script>
    <?php
}