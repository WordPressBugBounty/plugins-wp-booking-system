<?php

// Exit if accessed directly
if (! defined('ABSPATH')) exit;


/**
 * Validates and handles the adding of the new email notification in the database
 *
 */
function wpbs_action_add_email_notification()
{

    // Verify for nonce
    if (empty($_POST['wpbs_token']) || ! wp_verify_nonce($_POST['wpbs_token'], 'wpbs_add_email_notification'))
        return;

    // Verify for email notification name
    if (empty($_POST['notification_email_name'])) {

        wpbs_admin_notices()->register_notice('email_notification_name_missing', '<p>' . __('Please add a name for your new email notification.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('email_notification_name_missing');

        return;
    }

    // Verify for email notification name
    if (empty($_POST['email_notification_form_id'])) {
        return;
    }

    $form_id = absint($_POST['email_notification_form_id']);
    $form = wpbs_get_form($form_id);

    // Prepare email notification data to be inserted
    $email_notification_data = array(
        'name'             => sanitize_text_field(stripslashes($_POST['notification_email_name'])),
        'form_id'       => $form_id,
        'is_enabled'     => 1,
        'date_created'  => current_time('Y-m-d H:i:s'),
        'date_modified' => current_time('Y-m-d H:i:s'),
        'status'        => 'active'
    );

    // Insert email notification into the database
    $email_notification_id = wpbs_insert_email($email_notification_data);

    // If the email notification could not be inserted show a message to the user
    if (! $email_notification_id) {

        wpbs_admin_notices()->register_notice('email_notification_insert_false', '<p>' . __('Something went wrong. Could not create the email notification. Please try again.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('email_notification_insert_false');

        return;
    }

    $email_fields = wpbs_form_get_email_fields($form->get('fields'));


    // Set some defaults
    wpbs_update_email_meta($email_notification_id, 'notification_send_to_type', 'field');
    wpbs_update_email_meta($email_notification_id, 'notification_send_to_email', get_option('admin_email'));
    if ($email_fields) {
        $label = isset($email_fields[0]['values']['default']['label']) && !empty($email_fields[0]['values']['default']['label']) ? $email_fields[0]['values']['default']['label'] : 'no-label';
        wpbs_update_email_meta($email_notification_id, 'notification_send_to_field', '{' . $email_fields[0]['id'] . ':' . $label . '}');
        wpbs_update_email_meta($email_notification_id, 'notification_send_to', '{' . $email_fields[0]['id'] . ':' . $label . '}');
    } else {
        wpbs_update_email_meta($email_notification_id, 'notification_send_to', get_option('admin_email'));
    }


    if ($_POST['email_notification_template'] == 'user_notification') {
        // User Notification Template
        wpbs_update_email_meta($email_notification_id, 'notification_send_to_type', 'field');
        wpbs_update_email_meta($email_notification_id, 'notification_subject', __('Thank you for your booking!', 'wp-booking-system'));
        wpbs_update_email_meta($email_notification_id, 'notification_message', sprintf(__("Hi there, <br><br>Thank you for your booking! We're excited to welcome you. <br><br>Here are your booking details:<br><br>{All Fields}<br><br>Looking forward to your stay!<br><br>Best regards,<br>%s.", 'wp-booking-system'), get_bloginfo('name')));
        wpbs_update_email_meta($email_notification_id, 'notification_ical_file', 'on');
        wpbs_update_email_meta($email_notification_id, 'notification_ical_summary', sprintf(__('Stay at %s', 'wp-booking-system'), get_bloginfo('name')));
        wpbs_update_email_meta($email_notification_id, 'notification_when_to_send', 'booking_created');
    } else {
        // No template
        wpbs_update_email_meta($email_notification_id, 'notification_when_to_send', 'booking_created');
    }

    // Redirect to the edit page of the email notification with a success message
    wp_redirect(add_query_arg(array('page' => 'wpbs-forms', 'subpage' => 'edit-form', 'form_id' => $form_id, 'tab' => 'email-notifications', 'email_notification_id' => $email_notification_id, 'wpbs_message' => 'email_notification_insert_success'), admin_url('admin.php')));
    exit;
}
add_action('wpbs_action_add_email_notification', 'wpbs_action_add_email_notification', 50);


/**
 * Validates and handles the editing of an existing email notification
 *
 */
function wpbs_action_edit_email_notification()
{
    // Verify for nonce
    if (empty($_POST['wpbs_token']) || !wp_verify_nonce($_POST['wpbs_token'], 'wpbs_edit_email_notification')) {
        return;
    }

    $_POST = stripslashes_deep($_POST);

    if (empty($_POST['form_id'])) {

        wpbs_admin_notices()->register_notice('email_notification_update_failed', '<p>' . __('Something went wrong. Could not update the email_notification.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('email_notification_update_failed');

        return;
    }

    if (empty($_POST['email_id'])) {

        wpbs_admin_notices()->register_notice('email_notification_update_failed', '<p>' . __('Something went wrong. Could not update the email_notification.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('email_notification_update_failed');

        return;
    }

    $settings = get_option('wpbs_settings', array());
    $active_languages = (!empty($settings['active_languages']) ? $settings['active_languages'] : array());

    /**
     * Prepare variables
     *
     */
    $form_id = absint($_POST['form_id']);
    $email_id = absint($_POST['email_id']);
    $email_name = sanitize_text_field($_POST['notification_email_name']);


    /**
     * Handle email notification object data
     *
     */

    // Get email
    $email = wpbs_get_email($email_id);

    // Update email
    $update_data = array(
        'name' => (!empty($email_name) ? $email_name : $email->get('name')),
        'date_modified' => current_time('Y-m-d H:i:s'),
    );



    wpbs_update_email($email_id, $update_data);

    /**
     * Handle email meta
     *
     */


    // Email Notification Meta Fields
    $meta_fields = array(
        'notification_send_to_type' => array('translations' => false, 'sanitization' => 'sanitize_text_field'),
        'notification_send_to_field' => array('translations' => false, 'sanitization' => 'sanitize_text_field'),
        'notification_send_to_email' => array('translations' => false, 'sanitization' => 'sanitize_text_field'),
        'notification_send_to_cc' => array('translations' => false, 'sanitization' => 'sanitize_text_field'),
        'notification_send_to_bcc' => array('translations' => false, 'sanitization' => 'sanitize_text_field'),
        'notification_from_name' => array('translations' => false, 'sanitization' => 'sanitize_text_field'),
        'notification_from_email' => array('translations' => false, 'sanitization' => 'sanitize_text_field'),
        'notification_reply_to' => array('translations' => false, 'sanitization' => 'sanitize_text_field'),
        'notification_subject' => array('translations' => true, 'sanitization' => 'sanitize_text_field'),
        'notification_message' => array('translations' => true, 'sanitization' => 'wp_kses_post'),
        'notification_ical_file' => array('translations' => false, 'sanitization' => 'sanitize_text_field', 'checkbox' => true),
        'notification_ical_summary' => array('translations' => true, 'sanitization' => 'sanitize_text_field'),
        'notification_ical_description' => array('translations' => true, 'sanitization' => 'sanitize_text_field'),
        'notification_when_to_send' => array('translations' => false, 'sanitization' => 'sanitize_text_field'),
        'notification_day_offset' => array('translations' => false, 'sanitization' => 'sanitize_text_field'),
        'notification_delivery_hour' => array('translations' => false, 'sanitization' => 'sanitize_text_field'),
        'notification_manage_booking_link' => array('translations' => false, 'sanitization' => 'sanitize_text_field', 'checkbox' => true),
        'notification_enable_conditional_logic' => array('translations' => false, 'sanitization' => 'sanitize_text_field', 'checkbox' => true),
    );

    $meta_fields = apply_filters('wpbs_edit_emails_meta_fields', $meta_fields);

    // Store the "send to" email address in a single field.
    if (isset($_POST['notification_send_to_type']) && $_POST['notification_send_to_type'] == 'field') {
        if (isset($_POST['notification_send_to_field'])) {
            wpbs_update_email_meta($email_id, 'notification_send_to', sanitize_text_field($_POST['notification_send_to_field']));
        }
    } elseif (isset($_POST['notification_send_to_type']) && $_POST['notification_send_to_type'] == 'email') {
        if (isset($_POST['notification_send_to_email'])) {
            wpbs_update_email_meta($email_id, 'notification_send_to', sanitize_text_field($_POST['notification_send_to_email']));
        }
    }

    // Update meta fields
    foreach ($meta_fields as $meta_field => $options) {

        if (!isset($_POST[$meta_field])) {
            continue;
        }

        if (!wpbs_is_empty($_POST[$meta_field])) {
            wpbs_update_email_meta($email_id, $meta_field, $options['sanitization']($_POST[$meta_field]));
        } else {
            wpbs_delete_email_meta($email_id, $meta_field);
        }

        if ($options['translations'] == true) {
            foreach ($active_languages as $code) {

                if (!empty($_POST[$meta_field . '_translation_' . $code])) {
                    wpbs_update_email_meta($email_id, $meta_field . '_translation_' . $code, $options['sanitization']($_POST[$meta_field . '_translation_' . $code]));
                } else {
                    wpbs_delete_email_meta($email_id, $meta_field . '_translation_' . $code);
                }
            }
        }
    }



    /**
     * Action hook to save extra email data
     *
     * @param array $_POST
     *
     */
    do_action('wpbs_save_email_data', $_POST);

    /**
     * Success redirect
     *
     */
    if (isset($_POST['_wp_http_referer'])) {
        wp_redirect(add_query_arg(array('wpbs_message' => 'email_notification_edit_success'), $_POST['_wp_http_referer']));
        exit;
    }

    wp_redirect(add_query_arg(array('page' => 'wpbs-forms', 'subpage' => 'edit-form', 'form_id' => $form_id, 'tab' => 'email-notifications', 'email_id' => $email_id, 'wpbs_message' => 'email_notification_edit_success'), admin_url('admin.php')));
    exit;
}
add_action('wpbs_action_edit_email_notification', 'wpbs_action_edit_email_notification', 50);

/**
 * Handles the trash email action, which changes the status of the email from active to trash
 *
 */
function wpbs_action_trash_email()
{

    // Verify for nonce
    if (empty($_GET['wpbs_token']) || ! wp_verify_nonce($_GET['wpbs_token'], 'wpbs_trash_email'))
        return;

    if (empty($_GET['email_notification_id']))
        return;

    if (empty($_GET['form_id']))
        return;

    $email_notification_id = absint($_GET['email_notification_id']);

    $email_data = array(
        'status' => 'trash'
    );

    $updated = wpbs_update_email($email_notification_id, $email_data);

    if (! $updated)
        return;

    // Redirect to the current page
    wp_redirect(add_query_arg(array('page' => 'wpbs-forms', 'subpage' => 'edit-form', 'form_id' => $_GET['form_id'], 'tab' => 'email-notifications', 'wpbs_message' => 'email_trash_success'), admin_url('admin.php')));
    exit;
}
add_action('wpbs_action_trash_email', 'wpbs_action_trash_email', 50);


/**
 * Handles the restore email action, which changes the status of the email from trash to active
 *
 */
function wpbs_action_restore_email()
{

    // Verify for nonce
    if (empty($_GET['wpbs_token']) || ! wp_verify_nonce($_GET['wpbs_token'], 'wpbs_restore_email'))
        return;

    if (empty($_GET['email_notification_id']))
        return;

    $email_notification_id = absint($_GET['email_notification_id']);

    $email_data = array(
        'status' => 'active'
    );

    $updated = wpbs_update_email($email_notification_id, $email_data);

    if (! $updated)
        return;

    // Redirect to the current page
    wp_redirect(add_query_arg(array('page' => 'wpbs-forms', 'subpage' => 'edit-form', 'form_id' => $_GET['form_id'], 'tab' => 'email-notifications', 'email_status' => 'trash', 'wpbs_message' => 'email_notification_restore_success'), admin_url('admin.php')));
    exit;
}
add_action('wpbs_action_restore_email', 'wpbs_action_restore_email', 50);


/**
 * Handles the delete email action, which removes all email data, legend items and events data
 * associated with the email
 *
 */
function wpbs_action_delete_email()
{

    // Verify for nonce
    if (empty($_GET['wpbs_token']) || ! wp_verify_nonce($_GET['wpbs_token'], 'wpbs_delete_email'))
        return;

    if (empty($_GET['email_notification_id']))
        return;

    $email_notification_id = absint($_GET['email_notification_id']);

    /**
     * Delete the email
     *
     */
    $deleted = wpbs_delete_email($email_notification_id);

    if (! $deleted)
        return;

    /**
     * Delete email meta
     *
     */
    $email_meta = wpbs_get_email_meta($email_notification_id);

    if (! empty($email_meta)) {

        foreach ($email_meta as $key => $value) {

            wpbs_delete_email_meta($email_notification_id, $key);
        }
    }

    // Redirect to the current page
    wp_redirect(add_query_arg(array('page' => 'wpbs-forms', 'subpage' => 'edit-form', 'form_id' => $_GET['form_id'], 'tab' => 'email-notifications', 'email_status' => 'trash', 'wpbs_message' => 'email_notification_delete_success'), admin_url('admin.php')));
    exit;
}
add_action('wpbs_action_delete_email', 'wpbs_action_delete_email', 50);


/**
 * Handles the duplication of a email
 *
 */
function wpbs_action_duplicate_email()
{

    // Verify for nonce
    if (empty($_GET['wpbs_token']) || !wp_verify_nonce($_GET['wpbs_token'], 'wpbs_duplicate_email')) {
        return;
    }

    if (empty($_GET['email_notification_id'])) {
        return;
    }

    $email_notification_id = absint($_GET['email_notification_id']);

    $email = wpbs_get_email($email_notification_id);

    // Prepare email notification data to be inserted
    $email_notification_data = array(
        'name' => __('Duplicate of', 'wp-booking-system') . ' ' . $email->get('name'),
        'form_id'       =>  $email->get('form_id'),
        'is_enabled'     =>  $email->get('is_enabled'),
        'date_created'  => current_time('Y-m-d H:i:s'),
        'date_modified' => current_time('Y-m-d H:i:s'),
        'status' => $email->get('status'),
    );

    // Insert email notification into the database
    $new_email_notification_id = wpbs_insert_email($email_notification_data);

    $meta_fields = wpbs_get_email_meta($email_notification_id);

    foreach ($meta_fields as $meta_key => $meta_value) {
        if (is_serialized($meta_value[0], false)) {
            $meta_value[0] = unserialize($meta_value[0]);
        }
        wpbs_add_email_meta($new_email_notification_id, $meta_key, $meta_value[0]);
    }


    // Redirect to the current page
    wp_redirect(add_query_arg(array('page' => 'wpbs-forms', 'subpage' => 'edit-form', 'form_id' => $_GET['form_id'], 'tab' => 'email-notifications', 'wpbs_message' => 'email_duplicate_success'), admin_url('admin.php')));
    exit;
}
add_action('wpbs_action_duplicate_email', 'wpbs_action_duplicate_email', 50);


/**
 * AJAX Callback function for enabling/disabling emails
 * 
 */
function wpbs_action_ajax_email_status_toggle()
{

    if (!isset($_POST['email_id'])) {
        return false;
    }

    $email_id = absint($_POST['email_id']);

    wpbs_update_email($email_id, [
        'is_enabled' => ($_POST['value'] == 'true' ? 1 : 0),
        'date_modified' => current_time('Y-m-d H:i:s')
    ]);

    wp_die();
}
add_action('wp_ajax_wpbs_email_status_toggle', 'wpbs_action_ajax_email_status_toggle');
