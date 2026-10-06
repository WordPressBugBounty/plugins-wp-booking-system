<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Includes the files needed for the Form Emails
 *
 */
function wpbs_include_files_email()
{

    // Get form dir path
    $dir_path = plugin_dir_path(__FILE__);

    // Include main Email class
    if (file_exists($dir_path . 'class-email.php')) {
        include $dir_path . 'class-email.php';
    }

    // Include main Table List class
    if (file_exists($dir_path . 'class-list-table-emails.php')) {
        include $dir_path . 'class-list-table-emails.php';
    }

    // Include the db layer classes
    if (file_exists($dir_path . 'class-object-db-emails.php')) {
        include $dir_path . 'class-object-db-emails.php';
    }

    if (file_exists($dir_path . 'class-object-meta-db-emails.php')) {
        include $dir_path . 'class-object-meta-db-emails.php';
    }

    // Include the Email Notifications class
    if (file_exists($dir_path . 'class-email-notifications.php')) {
        include $dir_path . 'class-email-notifications.php';
    }

     // Include the Email Notifications class
    if (file_exists($dir_path . 'class-evaluate-email-logic.php')) {
        include $dir_path . 'class-evaluate-email-logic.php';
    }

    // Include email actions
    if (file_exists($dir_path . 'functions-actions-emails.php')) {
        include $dir_path . 'functions-actions-emails.php';
    }
}
add_action('wpbs_include_files', 'wpbs_include_files_email');

/**
 * Register the class that handles database queries for the Form Emails
 *
 * @param array $classes
 *
 * @return array
 *
 */
function wpbs_register_database_classes_emails($classes)
{

    $classes['emails'] = 'WPBS_Object_DB_Emails';
    $classes['emailmeta'] = 'WPBS_Object_Meta_DB_Emails';

    return $classes;
}
add_filter('wpbs_register_database_classes', 'wpbs_register_database_classes_emails');

/**
 * Returns an array with WPBS_Email objects from the database
 *
 * @param array $args
 * @param bool  $count
 *
 * @return array
 *
 */
function wpbs_get_emails($args = array(), $count = false)
{

    $emails = wp_booking_system()->db['emails']->get_emails($args, $count);

    /**
     * Add a filter hook just before returning
     *
     * @param array $emails
     * @param array $args
     * @param bool  $count
     *
     */
    return apply_filters('wpbs_get_emails', $emails, $args, $count);
}

/**
 * Gets a email from the database
 *
 * @param mixed int|object      - email id or object representing the email
 *
 * @return WPBS_Email|false
 *
 */
function wpbs_get_email($email)
{

    return wp_booking_system()->db['emails']->get_object($email);
}

/**
 * Inserts a new email into the database
 *
 * @param array $data
 *
 * @return mixed int|false
 *
 */
function wpbs_insert_email($data)
{

    return wp_booking_system()->db['emails']->insert($data);
}

/**
 * Updates a email from the database
 *
 * @param int     $email_id
 * @param array $data
 *
 * @return bool
 *
 */
function wpbs_update_email($email_id, $data)
{

    return wp_booking_system()->db['emails']->update($email_id, $data);
}

/**
 * Deletes a email from the database
 *
 * @param int $email_id
 *
 * @return bool
 *
 */
function wpbs_delete_email($email_id)
{

    return wp_booking_system()->db['emails']->delete($email_id);
}

/**
 * Inserts a new meta entry for the email
 *
 * @param int    $email_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $unique
 *
 * @return mixed int|false
 *
 */
function wpbs_add_email_meta($email_id, $meta_key, $meta_value, $unique = false)
{

    return wp_booking_system()->db['emailmeta']->add($email_id, $meta_key, $meta_value, $unique);
}

/**
 * Updates a meta entry for the email
 *
 * @param int    $email_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $prev_value
 *
 * @return bool
 *
 */
function wpbs_update_email_meta($email_id, $meta_key, $meta_value, $prev_value = '')
{

    return wp_booking_system()->db['emailmeta']->update($email_id, $meta_key, $meta_value, $prev_value);
}

/**
 * Returns a meta entry for the email
 *
 * @param int    $email_id
 * @param string $meta_key
 * @param bool   $single
 *
 * @return mixed
 *
 */
function wpbs_get_email_meta($email_id, $meta_key = '', $single = false)
{

    return wp_booking_system()->db['emailmeta']->get($email_id, $meta_key, $single);
}

/**
 * Returns the translated meta entry for the email
 *
 * @param int    $email_id
 * @param string $meta_key
 * @param string $language_code
 *
 * @return mixed
 *
 */
function wpbs_get_translated_email_meta($email_id, $meta_key, $language_code)
{
    $settings           = get_option('wpbs_settings', array());
    $active_languages = (! empty($settings['active_languages']) ? $settings['active_languages'] : array());

    if (in_array($language_code, $active_languages)) {

        $translated_meta = wpbs_get_email_meta($email_id, $meta_key . '_translation_' . $language_code, true);

        if (!empty($translated_meta)) {
            return $translated_meta;
        }
    }

    return wpbs_get_email_meta($email_id, $meta_key, true);
}

/**
 * Removes a meta entry for the email
 *
 * @param int    $email_id
 * @param string $meta_key
 * @param string $meta_value
 * @param bool   $delete_all
 *
 * @return bool
 *
 */
function wpbs_delete_email_meta($email_id, $meta_key, $meta_value = '', $delete_all = '')
{

    return wp_booking_system()->db['emailmeta']->delete($email_id, $meta_key, $meta_value, $delete_all);
}

/**
 * Send Scheduled Email
 * 
 * @param int $booking_id
 * @param int $email_id
 * 
 */
function wpbs_send_scheduled_email_notification($booking_id, $email_id)
{

    $booking = wpbs_get_booking($booking_id);

    if (is_null($booking)) {
        return false;
    }

    if ($booking->get('status') == 'trash') {
        return false;
    }

    if ($booking->get('status') == 'cancelled') {
        return false;
    }

    $email = wpbs_get_email($email_id);

    if (is_null($email)) {
        return false;
    }

    if ($email->get('status') == 'trash') {
        return false;
    }

    if (!$email->get('is_enabled')) {
        return false;
    }

    // // If Booking is still Pending, reschedule email.
    if ($booking->get('status') == 'pending') {

        // Reschedule the event to be sent after an hour
        if (wpbs_get_email_delivery_date($booking, $email) < current_time('timestamp')) {
            return false;
        }

        $delivery_date = current_time('timestamp') + HOUR_IN_SECONDS;
        wp_schedule_single_event($delivery_date, 'wpbs_send_scheduled_email_notification', array($booking_id, $email_id));

        return false;
    }

    $emails = new WPBS_Email_Notifications($booking_id);
    $emails->send_email($email_id);

}
add_action('wpbs_send_scheduled_email_notification', 'wpbs_send_scheduled_email_notification', 10, 2);

/**
 * Get scheduled email delivery date
 * 
 * @param WPBS_Booking $booking
 * @param WPBS_Email $email
 * 
 * @return int
 * 
 */
function wpbs_get_email_delivery_date($booking, $email)
{

    $when_to_send = wpbs_get_email_meta($email->get('id'), 'notification_when_to_send', true);
    $day_offset = absint(wpbs_get_email_meta($email->get('id'), 'notification_day_offset', true));

    // Date Created
    $date = new DateTime($booking->get('date_created'));
    $date->setTime(0, 0, 0);
    $booking_created_date = $date->getTimestamp();

    // Start Date
    $date = new DateTime($booking->get('start_date'));
    $date->setTime(0, 0, 0);
    $booking_start_date = $date->getTimestamp();
    
    // End Date
    $date = new DateTime($booking->get('end_date'));
    $date->setTime(0, 0, 0);
    $booking_end_date = $date->getTimestamp();

    // When do we send it.
    switch ($when_to_send) {
        case 'after_created':
            $delivery_date = $booking_created_date + intval($day_offset * DAY_IN_SECONDS);
            break;
        case 'before_start':
            $delivery_date = $booking_start_date - intval($day_offset * DAY_IN_SECONDS);
            break;
        case 'after_start':
            $delivery_date = $booking_start_date + intval($day_offset * DAY_IN_SECONDS);
            break;
        case 'before_end':
            $delivery_date = $booking_end_date - intval($day_offset * DAY_IN_SECONDS);
            break;
        case 'after_end':
            $delivery_date = $booking_end_date + intval($day_offset * DAY_IN_SECONDS);
            break;
        default:
            $delivery_date = $booking_start_date;
    }

    if (function_exists('wpbs_scheduled_email_delivery_hour')) {
        $delivery_date += wpbs_scheduled_email_delivery_hour($email->get('id'));
    }

    return $delivery_date;
}
