<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function wpbs_action_ajax_open_booking_details()
{

    // Nonce
    check_ajax_referer('wpbs_open_booking_details', 'wpbs_token');

    if (!isset($_POST['id'])) {
        return false;
    }

    $booking_id = absint($_POST['id']);

    // Get booking
    $booking = wpbs_get_booking($booking_id);

    if (is_null($booking)) {
        return;
    }

    // If booking is unread, make it read
    if (!$booking->get('is_read')) {
        $booking_data = array(
            'is_read' => 1,
        );
        wpbs_update_booking($booking_id, $booking_data);
    }

    // Get modal content
    $booking_display = new WPBS_Booking_Details_Outputter($booking);
    $booking_display->display();

    wp_die();
}
add_action('wp_ajax_wpbs_open_booking_details', 'wpbs_action_ajax_open_booking_details');

function wpbs_action_ajax_booking_email_customer()
{
    // Nonce
    check_ajax_referer('wpbs_booking_email_customer', 'wpbs_token');

    if (!isset($_POST['id'])) {
        return false;
    }

    $booking_id = absint($_POST['id']);

    // Get booking
    $booking = wpbs_get_booking($booking_id);

    if (is_null($booking)) {
        return;
    }
    parse_str($_POST['form_data'], $_POST['form_data']);

    $email_form_data = $_POST['form_data'];

    $language = wpbs_get_booking_meta($booking_id, 'submitted_language', true);

    switch_to_locale(wpbs_language_to_locale($language));

    // Parse some form tags
    $email_tags = new WPBS_Email_Tags(wpbs_get_form($booking->get('form_id')), wpbs_get_calendar($booking->get('calendar_id')), $booking_id, $booking->get('fields'), $language, strtotime($booking->get('start_date')), strtotime($booking->get('end_date')));

    $email_form_data['booking_email_customer_message'] = $email_tags->parse(do_shortcode(nl2br($email_form_data['booking_email_customer_message'])));
    $email_form_data['booking_email_customer_subject'] = $email_tags->parse($email_form_data['booking_email_customer_subject']);

    // Send the email
    $mailer = new WPBS_Booking_Mailer($booking, $email_form_data);
    $mailer->prepare('customer');
    $mailer->send();

    echo __('Email successfully sent.', 'wp-booking-system');

    wpbs_delete_temporary_files($booking->get('id'));

    wp_die();
}
add_action('wp_ajax_wpbs_booking_email_customer', 'wpbs_action_ajax_booking_email_customer');

/**
 * Edit Booking Details
 *
 */
function wpbs_action_ajax_wpbs_edit_booking_details()
{

    // Verify nonce
    if (empty($_POST['token']) || !wp_verify_nonce($_POST['token'], 'wpbs_edit_booking')) {
        return;
    }

    parse_str($_POST['form_data'], $data);

    if (!isset($data['booking_id'])) {
        return false;
    }

    $booking_id = absint($data['booking_id']);

    // Get booking
    $booking = wpbs_get_booking($booking_id);

    if (is_null($booking)) {
        return;
    }

    $editable_crons = wpbs_get_editable_crons();

    // Update Booking Details (Form Fields)
    if ($_POST['type'] == 'booking_details') {

        $fields = $booking->get('fields');


        foreach ($fields as &$field) {
            if (isset($data['wpbs-edit-booking-field-' . $field['id']]) && !empty(isset($data['wpbs-edit-booking-field-' . $field['id']]))) {
                $field['user_value'] = esc_attr($data['wpbs-edit-booking-field-' . $field['id']]);
            }
        }

        $booking_data = array('fields' => $fields);

        wpbs_update_booking($booking_id, $booking_data);
    }

    // Update Booking Data (Dates)
    if ($_POST['type'] == 'booking_data') {

        $start_date = DateTime::createFromFormat('Y-m-d', $data['wpbs-edit-booking-field-start_date']);
        $end_date = DateTime::createFromFormat('Y-m-d', $data['wpbs-edit-booking-field-end_date']);

        // Check if dates are in valid order
        if (!$start_date || !$end_date || $start_date > $end_date) {
            $response = array(
                'start_date' => wpbs_date_i18n(get_option('date_format'), strtotime($booking->get('start_date'))),
                'end_date' => wpbs_date_i18n(get_option('date_format'), strtotime($booking->get('end_date'))),
            );
        } else {
            $booking_data = array(
                'start_date' => date('Y-m-d 00:00:00', $start_date->getTimestamp()),
                'end_date' => date('Y-m-d 00:00:00', $end_date->getTimestamp()),
            );

            wpbs_update_booking($booking_id, $booking_data);

            $response = array(
                'start_date' => wpbs_date_i18n(get_option('date_format'), strtotime($booking_data['start_date'])),
                'end_date' => wpbs_date_i18n(get_option('date_format'), strtotime($booking_data['end_date'])),
            );
        }

        foreach ($editable_crons as $field_name => $cron_name) {

            if (isset($data['wpbs-edit-booking-field-' . $field_name])) {

                foreach ($data['wpbs-edit-booking-field-' . $field_name] as $object_id => $value) {


                    $new_cron_date = DateTime::createFromFormat('Y-m-d H:i:s', $value . ' 00:00:00');
                    $new_cron_date->modify((wpbs_scheduled_email_delivery_hour($object_id) >= 0 ? '+ ' : '') . wpbs_scheduled_email_delivery_hour($object_id) . ' seconds');

                    $crons = _get_cron_array();
                    foreach ($crons as $timestamp => $cron) {
                        if (isset($cron[$cron_name])) {
                            foreach ($cron[$cron_name] as $job_id => $job) {
                                if ($job['args'][0] == $booking_id && $job['args'][1] == $object_id) {

                                    if ($timestamp != $new_cron_date->getTimestamp()) {
                                        unset($crons[$timestamp][$cron_name][$job_id]);
                                        $new_job_id = md5(serialize($job['args']));
                                        $crons[$new_cron_date->getTimestamp()][$cron_name][$new_job_id] = $job;

                                        // Clean cron
                                        foreach ($crons as $timestamp => $cron) {
                                            if (isset($cron[$cron_name]) && empty($cron[$cron_name])) {
                                                unset($crons[$timestamp][$cron_name]);
                                            }
                                            if (empty($crons[$timestamp])) {
                                                unset($crons[$timestamp]);
                                            }
                                        }
                                        $response[$field_name . $object_id] = wpbs_date_i18n(get_option('date_format')  . ', ' . get_option('time_format'), $new_cron_date->getTimestamp() + get_option('gmt_offset') * HOUR_IN_SECONDS);

                                        _set_cron_array($crons);
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        echo json_encode($response);
    }


    do_action('wpbs_booking_edited', $booking_id);

    wp_die();
}
add_action('wp_ajax_wpbs_edit_booking_details', 'wpbs_action_ajax_wpbs_edit_booking_details');

/**
 * Remove scheduled event
 * 
 */
function wpbs_action_ajax_wpbs_remove_scheduled_event()
{

    // Nonce
    check_ajax_referer('wpbs_remove_scheduled_event', 'wpbs_token');

    if (!isset($_POST['cron'])) {
        wp_die();
    }

    $cron_name = sanitize_text_field($_POST['cron']);

    if (!isset($_POST['booking_id'])) {
        wp_die();
    }

    $booking_id = absint($_POST['booking_id']);
    $object_id = absint($_POST['object_id']);

    $booking = wpbs_get_booking($booking_id);

    if (!wpbs_current_user_can_access_calendar($booking->get('calendar_id'))) {
        wp_die();
    }


    $crons = _get_cron_array();
    foreach ($crons as $timestamp => $cron) {
        if (isset($cron[$cron_name])) {
            foreach ($cron[$cron_name] as $job_id => $job) {
                if ($job['args'][0] == $booking_id && $job['args'][1] == $object_id) {
                    unset($crons[$timestamp][$cron_name][$job_id]);

                    // Clean cron
                    foreach ($crons as $timestamp => $cron) {
                        if (isset($cron[$cron_name]) && empty($cron[$cron_name])) {
                            unset($crons[$timestamp][$cron_name]);
                        }
                        if (empty($crons[$timestamp])) {
                            unset($crons[$timestamp]);
                        }
                    }

                    _set_cron_array($crons);
                }
            }
        }
    }

    wp_die();
}
add_action('wp_ajax_wpbs_remove_scheduled_event', 'wpbs_action_ajax_wpbs_remove_scheduled_event');

/**
 * Add Booking Notes
 *
 */
function wpbs_action_ajax_booking_add_note()
{
    // Nonce
    check_ajax_referer('wpbs_booking_notes', 'wpbs_token');

    if (!isset($_POST['booking_id'])) {
        return false;
    }

    $booking_id = absint($_POST['booking_id']);

    $note = sanitize_textarea_field($_POST['note']);

    if (empty($note)) {
        return false;
    }

    $booking_notes = wpbs_get_booking_meta($booking_id, 'booking_notes', true);

    if (empty($booking_notes)) {
        $booking_notes = array();
    }

    $timestamp = current_time('timestamp');

    $booking_notes[] = array(
        'timestamp' => $timestamp,
        'note' => $note,
    );

    wpbs_update_booking_meta($booking_id, 'booking_notes', $booking_notes);

    echo '
    <div class="wpbs-booking-details-modal-note">
        <div class="wpbs-booking-details-modal-note-body">
        <p>' . nl2br($note) . '</p>
        </div>
        <div class="wpbs-booking-details-modal-note-footer">
            <p>' . __('Added on', 'wp-booking-system') . ': ' . date(get_option('date_format') . ' ' . get_option('time_format'), $timestamp) . ' </p>
            <a href="#" data-booking-note="' . array_key_last($booking_notes) . '" data-booking-id="' . $booking_id . '" class="wpbs-booking-details-modal-note-remove"><i class="wpbs-icon-trash"></i></a>
        </div>
    </div>
    ';

    wp_die();
}
add_action('wp_ajax_wpbs_booking_add_note', 'wpbs_action_ajax_booking_add_note');

/**
 * Delete Booking Notes
 *
 */
function wpbs_action_ajax_booking_delete_note()
{
    // Nonce
    check_ajax_referer('wpbs_booking_notes', 'wpbs_token');

    if (!isset($_POST['booking_id'])) {
        return false;
    }

    $booking_id = absint($_POST['booking_id']);

    $note_id = absint($_POST['note_id']);

    $booking_notes = wpbs_get_booking_meta($booking_id, 'booking_notes', true);

    unset($booking_notes[$note_id]);

    wpbs_update_booking_meta($booking_id, 'booking_notes', $booking_notes);

    wp_die();
}
add_action('wp_ajax_wpbs_booking_delete_note', 'wpbs_action_ajax_booking_delete_note');

/**
 * Fix html entities in line items
 */
function wpbs_format_html_string($string)
{
    // Remove quantity from labels
    $string = preg_replace('/<span class="wpbs-line-item-quantity\b[^>]*>(.*?)<\/span>/i', '', $string);
    $string = strip_tags($string);
    $string = str_replace('&times;', 'x', $string);
    return $string;
}

/**
 * Save "Hide past bookings" option
 *
 */
function wpbs_action_ajax_booking_remember_hide_past_option()
{
    // Nonce
    check_ajax_referer('wpbs_remember_hide_past_option', 'wpbs_token');

    update_user_meta(get_current_user_id(), 'wpbs_remember_hide_past_bookings_option', ($_POST['remember'] == 'true' ? true : false));

    wp_die();
}
add_action('wp_ajax_wpbs_booking_remember_hide_past_option', 'wpbs_action_ajax_booking_remember_hide_past_option');

/**
 * Save "Order" option
 *
 */
function wpbs_action_ajax_booking_remember_order_option()
{
    // Nonce
    check_ajax_referer('wpbs_remember_order_option', 'wpbs_token');

    update_user_meta(get_current_user_id(), 'wpbs_remember_order_bookings_option', sanitize_text_field($_POST['value']));

    wp_die();
}
add_action('wp_ajax_wpbs_booking_remember_order_option', 'wpbs_action_ajax_booking_remember_order_option');

/**
 * Save "Order By" option
 *
 */
function wpbs_action_ajax_booking_remember_orderby_option()
{
    // Nonce
    check_ajax_referer('wpbs_remember_orderby_option', 'wpbs_token');

    update_user_meta(get_current_user_id(), 'wpbs_remember_orderby_bookings_option', sanitize_text_field($_POST['value']));

    wp_die();
}
add_action('wp_ajax_wpbs_booking_remember_orderby_option', 'wpbs_action_ajax_booking_remember_orderby_option');

