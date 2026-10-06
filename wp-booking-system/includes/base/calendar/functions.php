<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Includes the files needed for the Calendars
 *
 */
function wpbs_include_files_calendar()
{

    // Get calendar dir path
    $dir_path = plugin_dir_path(__FILE__);

    // Include other functions files
    if (file_exists($dir_path . 'functions-ajax.php')) {
        include $dir_path . 'functions-ajax.php';
    }

    // Include main Calendar class
    if (file_exists($dir_path . 'class-calendar.php')) {
        include $dir_path . 'class-calendar.php';
    }

    // Include the db layer classes
    if (file_exists($dir_path . 'class-object-db-calendars.php')) {
        include $dir_path . 'class-object-db-calendars.php';
    }

    if (file_exists($dir_path . 'class-object-meta-db-calendars.php')) {
        include $dir_path . 'class-object-meta-db-calendars.php';
    }

    // Include calendar outputters
    if (file_exists($dir_path . 'class-calendar-outputter.php')) {
        include $dir_path . 'class-calendar-outputter.php';
    }

    if (file_exists($dir_path . 'class-calendar-overview-outputter.php')) {
        include $dir_path . 'class-calendar-overview-outputter.php';
    }

    if (file_exists($dir_path . 'class-calendar-yearly-outputter.php')) {
        include $dir_path . 'class-calendar-yearly-outputter.php';
    }
}
add_action('wpbs_include_files', 'wpbs_include_files_calendar');

/**
 * Register the class that handles database queries for the Calendars
 *
 * @param array $classes
 *
 * @return array
 *
 */
function wpbs_register_database_classes_calendars($classes)
{

    $classes['calendars'] = 'WPBS_Object_DB_Calendars';
    $classes['calendarmeta'] = 'WPBS_Object_Meta_DB_Calendars';

    return $classes;
}
add_filter('wpbs_register_database_classes', 'wpbs_register_database_classes_calendars');

/**
 * Returns an array with WPBS_Calendar objects from the database
 *
 * @param array $args
 * @param bool  $count
 *
 * @return array
 *
 */
function wpbs_get_calendars($args = array(), $count = false)
{

    $calendars = wp_booking_system()->db['calendars']->get_calendars($args, $count);

    /**
     * Add a filter hook just before returning
     *
     * @param array $calendars
     * @param array $args
     * @param bool  $count
     *
     */
    return apply_filters('wpbs_get_calendars', $calendars, $args, $count);
}

/**
 * Gets a calendar from the database
 *
 * @param mixed int|object      - calendar id or object representing the calendar
 *
 * @return WPBS_Calendar|false
 *
 */
function wpbs_get_calendar($calendar)
{

    return wp_booking_system()->db['calendars']->get_object($calendar);
}

/**
 * Inserts a new calendar into the database
 *
 * @param array $data
 *
 * @return mixed int|false
 *
 */
function wpbs_insert_calendar($data)
{

    return wp_booking_system()->db['calendars']->insert($data);
}

/**
 * Updates a calendar from the database
 *
 * @param int     $calendar_id
 * @param array $data
 *
 * @return bool
 *
 */
function wpbs_update_calendar($calendar_id, $data)
{

    return wp_booking_system()->db['calendars']->update($calendar_id, $data);
}

/**
 * Deletes a calendar from the database
 *
 * @param int $calendar_id
 *
 * @return bool
 *
 */
function wpbs_delete_calendar($calendar_id)
{

    return wp_booking_system()->db['calendars']->delete($calendar_id);
}

/**
 * Inserts a new meta entry for the calendar
 *
 * @param int    $calendar_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $unique
 *
 * @return mixed int|false
 *
 */
function wpbs_add_calendar_meta($calendar_id, $meta_key, $meta_value, $unique = false)
{

    return wp_booking_system()->db['calendarmeta']->add($calendar_id, $meta_key, $meta_value, $unique);
}

/**
 * Updates a meta entry for the calendar
 *
 * @param int    $calendar_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $prev_value
 *
 * @return bool
 *
 */
function wpbs_update_calendar_meta($calendar_id, $meta_key, $meta_value, $prev_value = '')
{

    return wp_booking_system()->db['calendarmeta']->update($calendar_id, $meta_key, $meta_value, $prev_value);
}

/**
 * Returns a meta entry for the calendar
 *
 * @param int    $calendar_id
 * @param string $meta_key
 * @param bool   $single
 *
 * @return mixed
 *
 */
function wpbs_get_calendar_meta($calendar_id, $meta_key = '', $single = false)
{

    return wp_booking_system()->db['calendarmeta']->get($calendar_id, $meta_key, $single);
}

/**
 * Returns the translated meta entry for the calendar
 *
 * @param int    $calendar_id
 * @param string $meta_key
 * @param string $language_code
 *
 * @return mixed
 *
 */
function wpbs_get_translated_calendar_meta($calendar_id, $meta_key, $language_code)
{
    $settings           = get_option('wpbs_settings', array());
    $active_languages = (!empty($settings['active_languages']) ? $settings['active_languages'] : array());

    if (in_array($language_code, $active_languages)) {

        $translated_meta = wpbs_get_calendar_meta($calendar_id, $meta_key . '_translation_' . $language_code, true);

        if (!empty($translated_meta)) {
            return $translated_meta;
        }
    }

    return wpbs_get_calendar_meta($calendar_id, $meta_key, true);
}

/**
 * Removes a meta entry for the calendar
 *
 * @param int    $calendar_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $delete_all
 *
 * @return bool
 *
 */
function wpbs_delete_calendar_meta($calendar_id, $meta_key, $meta_value = '', $delete_all = '')
{

    return wp_booking_system()->db['calendarmeta']->delete($calendar_id, $meta_key, $meta_value, $delete_all);
}

/**
 * Returns the default arguments for the calendar outputter
 *
 * @return array
 *
 */
function wpbs_get_calendar_output_default_args()
{

    $args = array(
        'show_title' => 1,
        'months_to_show' => 1,
        'start_weekday' => 1,
        'show_legend' => 1,
        'legend_position' => 'side',
        'show_button_navigation' => 1,
        'show_selector_navigation' => 1,
        'show_week_numbers' => 0,
        'current_year' => date('Y'),
        'current_month' => date('n'),
        'jump_months' => 0,
        'highlight_today' => 0,
        'history' => 1,
        'show_tooltip' => 1,
        'show_prices' => 0,
        'language' => wpbs_get_site_language(),
        'min_width' => '200',
        'max_width' => '380',
        'start_date' => 0,
        'end_date' => 0,
        'changeover_start' => 0,
        'changeover_end' => 0,
        'form_position' => 'bottom',
        'manual_booking' => '',
        'current_date' => mktime(0, 0, 0, wp_date('m'), wp_date('j'), wp_date('Y')) * 1000
    );

    /**
     * Filter the args before returning
     *
     * @param array $args
     *
     */
    $args = apply_filters('wpbs_get_calendar_output_default_args', $args);

    return $args;
}

/**
 * Returns the default arguments for the calendar overview outputter
 *
 * @return array
 *
 */
function wpbs_get_calendar_overview_output_default_args()
{

    $args = array(
        'show_legend' => 1,
        'legend_position' => 'top',
        'show_day_abbreviation' => 0,
        'current_year' => date('Y'),
        'current_month' => date('n'),
        'history' => 1,
        'show_tooltip' => 1,
        'language' => wpbs_get_site_language(),
    );

    /**
     * Filter the args before returning
     *
     * @param array $args
     *
     */
    $args = apply_filters('wpbs_get_calendar_overview_output_default_args', $args);

    return $args;
}


/**
 * Gets all the bookings, loops through the interval and returns WPBS_Event objects
 *
 * @param int $calendar_id
 *
 * @return array
 *
 */
function wpbs_get_bookings_as_events($calendar_id, $events, $year, $month)
{

    $booking_events = array();
    $month = str_pad($month, 2, '0', STR_PAD_LEFT);

    $bookings = wpbs_get_bookings(array('calendar_id' => $calendar_id, 'orderby' => 'id', 'order' => 'asc', 'status' => array('pending', 'accepted'), 'custom_query' =>
    ' AND ' . $year . $month . ' BETWEEN EXTRACT(YEAR_MONTH FROM start_date) and EXTRACT(YEAR_MONTH FROM end_date)'));

    foreach ($bookings as $booking) {
        $events_begin = new DateTime($booking->get('start_date'));

        $events_end = new DateTime($booking->get('end_date'));
        $events_end->modify('+1 day');

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($events_begin, $interval, $events_end);

        foreach ($period as $event_date) {

            $event_data = array(
                'id' => null,
                'calendar_id' => $calendar_id,
                'booking_id' => $booking->get('id'),
                'date_year' => $event_date->format('Y'),
                'date_month' => $event_date->format('m'),
                'date_day' => $event_date->format('d'),
            );
            $booking_events[] = wpbs_get_event((object) $event_data);
        }
    }

    return $booking_events;
}

/**
 * Remove Timezone from Date strings.
 *
 * @param string $date
 *
 * @return string
 *
 */
function wpbs_remove_timezone_from_date_string($date)
{
    return trim(explode('T', $date)[0], 'Z');
}

/**
 * Check if a date range in a calendar is bookable.
 *
 * @since 5.7
 *
 * @param int $calendar_id
 * @param string $start_date Ymd
 * @param string $end_date Ymd
 *
 * @return bool
 *
 */
function wpbs_check_if_date_range_is_bookable($calendar_id, $start_date, $end_date, $calendar_events = false)
{

    $calendar_id = absint($calendar_id);
    $start_date = (string) $start_date;
    $end_date = (string) $end_date;

    $start_date_object = DateTime::createFromFormat('!Ymd', $start_date);
    $end_date_object = DateTime::createFromFormat('!Ymd', $end_date);

    if (
        !$calendar_id ||
        !$start_date_object ||
        !$end_date_object ||
        $start_date_object->format('Ymd') !== $start_date ||
        $end_date_object->format('Ymd') !== $end_date ||
        $start_date_object > $end_date_object
    ) {
        return false;
    }

    $non_bookable_legend_items = array();
    $legend_items = wpbs_get_legend_items(array('calendar_id' => $calendar_id));

    $changeover_start = $changeover_end = -1;

    foreach ($legend_items as $legend_item) {
        if (!$legend_item->get('is_bookable')) {
            $non_bookable_legend_items[] = $legend_item->get('id');
        }

        if ($legend_item->get('auto_pending') == 'changeover_start') {
            $changeover_start = $legend_item->get('id');
        }

        if ($legend_item->get('auto_pending') == 'changeover_end') {
            $changeover_end = $legend_item->get('id');
        }
    }

    $changeover_start_found = $changeover_end_found = 0;

    // Loop through events
    if ($calendar_events === false) {
        global $wpdb;

        $custom_query = $wpdb->prepare(
            ' AND CONCAT(date_year,LPAD(date_month, 2, "0"),LPAD(date_day, 2, "0")) >= %d
          AND CONCAT(date_year,LPAD(date_month, 2, "0"),LPAD(date_day, 2, "0")) <= %d',
            (int) $start_date,
            (int) $end_date
        );

        $calendar_events = wpbs_get_events(array(
            'calendar_id' => $calendar_id,
            'custom_query' => $custom_query,
        ));
    }

    // Set custom search dates
    $search_start_date = DateTime::createFromFormat('Ymd', $start_date);
    $search_start_date->modify('-2 days');

    // Format end date
    $search_end_date = DateTime::createFromFormat('Ymd', $end_date);
    $search_end_date->modify('2 days');

    $events = $calendar_events;

    $sorted_events = array();
    foreach ($events as $event) {
        $sorted_events[$event->get('date_year') . str_pad($event->get('date_month'), 2, '0', STR_PAD_LEFT) . str_pad($event->get('date_day'), 2, '0', STR_PAD_LEFT)] = $event->get('legend_item_id');
    }

    ksort($sorted_events);

    $interval = DateInterval::createFromDateString('1 day');
    $start = DateTime::createFromFormat('Ymd', $start_date);
    $end = DateTime::createFromFormat('Ymd', $end_date);
    $end->modify('+1 day');
    $period = new DatePeriod($start, $interval, $end);

    $selection = [];

    foreach ($period as $date) {
        $selection[$date->format('Ymd')] = false;
    }

    foreach ($sorted_events as $event_date => $event_legend_item_id) {
        // If event date is outside search range, continue;
        if ($event_date < $start_date || $event_date > $end_date) {
            continue;
        }

        // Check if the event found is not bookable
        if (in_array($event_legend_item_id, $non_bookable_legend_items)) {
            return false;
        }

        // Check for changeovers. The rule is that if a start changeover exists in an array, we shouln't have an end changeover

        // We found a starting changeover date
        if ($event_legend_item_id == $changeover_start) {
            $changeover_start_found++;
        }

        if ($event_legend_item_id == $changeover_end) {
            $changeover_end_found++;
        }

        // Now if we find an ending changeover date and a starting changeover date was previously found, we mark the date as not available.
        if ($event_legend_item_id == $changeover_end && $changeover_start_found !== 0) {
            return false;
        }

        // If more than 2 starting changeovers are found, selection is invalid
        if ($changeover_start_found !== 0 && $changeover_start_found > 1) {
            return false;
        }

        // If more than 2 ending changeovers are found, selection is invalid
        if ($changeover_end_found !== 0 && $changeover_end_found > 1) {
            return false;
        }

        $selection[$event_date] = $event_legend_item_id;
    }

    foreach ($selection as $i => $legend_id) {

        if ($legend_id === false) {
            continue;
        }

        // Check if the selection starts with a starting changeover
        if ($i == $start_date && $legend_id == $changeover_start) {
            return false;
        }

        // Check if the selection ends with an ending changeover.
        if ($i == $end_date && $legend_id == $changeover_end) {
            return false;
        }

        // Check if we have any changeover in the middle of our selection range
        if ($i != $start_date && $i != $end_date && ($legend_id == $changeover_start || $legend_id == $changeover_end)) {
            return false;
        }
    }

    return true;
}

/**
 * AJAX wrapper for the check dates function
 * 
 */
function wpbs_ajax_check_if_date_range_is_bookable()
{
    $calendar_id = isset($_POST['calendar_id']) ? absint($_POST['calendar_id']) : 0;
    $start_date = isset($_POST['start_date']) ? sanitize_text_field(wp_unslash($_POST['start_date'])) : '';


    $end_date = isset($_POST['end_date']) ? sanitize_text_field(wp_unslash($_POST['end_date'])) : '';

    echo wpbs_check_if_date_range_is_bookable($calendar_id, $start_date, $end_date) ? '1' : '0';

    wp_die();
}

add_action('wp_ajax_nopriv_wpbs_check_if_date_range_is_bookable', 'wpbs_ajax_check_if_date_range_is_bookable');
add_action('wp_ajax_wpbs_check_if_date_range_is_bookable', 'wpbs_ajax_check_if_date_range_is_bookable');


/**
 * Change the current starting month or starting year of the calendar with an url parameter
 *
 * @param array $calendar_args
 *
 * @return array
 *
 */
function wpbs_calendar_shortcode_dynamic_args($calendar_args, $calendar_id)
{
    // Allow dynamic changing of month
    if (isset($_GET['wpbs-start-month']) && !empty($_GET['wpbs-start-month'])) {
        $calendar_args['current_month'] = absint($_GET['wpbs-start-month']);
    }

    // Allow dynamic changing of month
    if (isset($_GET['wpbs-start-year']) && !empty($_GET['wpbs-start-year'])) {
        $calendar_args['current_year'] = absint($_GET['wpbs-start-year']);
    }

    // Allow dynamic setting of selection
    if (
        isset($_GET['wpbs-selection-start']) && !empty($_GET['wpbs-selection-start']) &&
        isset($_GET['wpbs-selection-end']) && !empty($_GET['wpbs-selection-end'])
    ) {

        $start_date = DateTime::createFromFormat('Y-m-d', $_GET['wpbs-selection-start']);
        $end_date = DateTime::createFromFormat('Y-m-d', $_GET['wpbs-selection-end']);

        // Silently fail if an invalid date was passed
        if (!empty(DateTime::getLastErrors()['error_count'])) {
            return $calendar_args;
        }

        // ..or if the date is in the past
        if ($start_date < (new DateTime())->setTime(0, 0)) {
            return $calendar_args;
        }

        // ..or if the starting date is grater than the ending date
        if ($start_date > $end_date) {
            return $calendar_args;
        }

        // Set time time to 0
        $start_date->setTime(0, 0, 0);
        $end_date->setTime(0, 0, 0);

        if (wpbs_check_if_date_range_is_bookable($calendar_id, $start_date->format('Ymd'), $end_date->format('Ymd')) == false) {
            return $calendar_args;
        }

        $calendar_args['start_date'] = $start_date->getTimestamp() * 1000;
        $calendar_args['end_date'] = $end_date->getTimestamp() * 1000;
    }

    return $calendar_args;
}
add_filter('wpbs_calendar_shortcode_args', 'wpbs_calendar_shortcode_dynamic_args', 10, 2);

/**
 * Change the current starting month or starting year of the calendar with an url parameter
 *
 * @param array $calendar_args
 *
 * @return array
 *
 */
function wpbs_overview_calendar_shortcode_dynamic_args($calendar_args)
{
    // Allow dynamic changing of month
    if (isset($_GET['wpbs-start-month']) && !empty($_GET['wpbs-start-month'])) {
        $calendar_args['current_month'] = absint($_GET['wpbs-start-month']);
    }

    // Allow dynamic changing of month
    if (isset($_GET['wpbs-start-year']) && !empty($_GET['wpbs-start-year'])) {
        $calendar_args['current_year'] = absint($_GET['wpbs-start-year']);
    }

    return $calendar_args;
}
add_filter('wpbs_overview_calendar_shortcode_args', 'wpbs_overview_calendar_shortcode_dynamic_args', 10, 1);


