<?php

use Kigkonsult\Icalcreator\Vcalendar;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class WPBS_Email_Notifications
{

    /**
     * The WPBS_Form
     *
     * @access public
     * @var    WPBS_Form
     *
     */
    public $form = null;

    /**
     * The WPBS_Calendar
     *
     * @access public
     * @var    WPBS_Calendar
     *
     */
    public $calendar = null;

    /**
     * The booking id
     *
     * @access public
     * @var    int
     *
     */
    public $booking_id = null;

    /**
     * The booking
     *
     * @access public
     * @var    WPBS_Booking
     *
     */
    public $booking = null;

    /**
     * The form fields
     *
     * @access public
     * @var    array
     *
     */
    public $form_fields = null;

    /**
     * Booking Start Date
     *
     * @access public
     * @var    string
     *
     */
    public $booking_start_date;

    /**
     * Booking End Date
     *
     * @access public
     * @var    string
     *
     */
    public $booking_end_date;

    /**
     * Language
     *
     * @access public
     * @var    string
     *
     */
    public $language;

    /**
     * Email Tags
     *
     * @access public
     * @var    WPBS_Email_Tags
     *
     */
    public $email_tags;

    /**
     * Whether to send past emails instantly or not
     * 
     * @access public
     * @var bool
     * 
     */
    public $send_past_emails = true;

    /**
     * The email notifications
     * 
     * @access public
     * @var array
     * 
     */
    public $emails;

    /**
     * Constructor
     *
     * @param WPBS_Form $form
     * @param array     $args
     *
     */
    public function __construct($booking_id)
    {

        /**
         * Set the booking id
         *
         */
        $this->booking_id = $booking_id;

        /**
         * Set the booking object
         *
         */
        $this->booking = wpbs_get_booking($booking_id);

        /**
         * Set the form
         *
         */
        $this->form = wpbs_get_form($this->booking->get('form_id'));

        // If the form no longer exists, exit.
        if (!$this->form) {
            return false;
        }

        /**
         * Set the calendar
         *
         */
        $this->calendar = wpbs_get_calendar($this->booking->get('calendar_id'));

        /**
         * Set the form fields
         *
         */
        $this->form_fields = $this->booking->get('fields');

        /**
         * Set the language
         *
         */
        $this->language = wpbs_get_booking_meta($this->booking->get('id'), 'submitted_language', true);

        /**
         * Set the booking dates
         *
         */
        $this->booking_start_date = strtotime($this->booking->get('start_date'));
        $this->booking_end_date = strtotime($this->booking->get('end_date'));

        /**
         * Load the Email Tags class
         * 
         */
        $this->email_tags = new WPBS_Email_Tags($this->form, $this->calendar, $this->booking_id, $this->form_fields, $this->language, $this->booking_start_date, $this->booking_end_date);

        /**
         * Get all the emails
         * 
         */
        $this->emails = wpbs_get_emails(['form_id' => $this->form->get('id'), 'status' => 'active', 'is_enabled' => [1]]);
    }

    

    /**
     * Send all the 'booking accepted' emails
     * 
     */
    public function send_booking_accepted_emails()
    {

        $emails_to_send = ['booking_accepted'];

        foreach ($this->emails as $email) {
            if (!in_array($this->get_field('when_to_send', $email->get('id')), $emails_to_send)) {
                continue;
            }

            $this->send_email($email->get('id'));
        }
    }

    /**
     * Send all the 'booking cancelled' emails
     * 
     */
    public function send_booking_cancelled_emails()
    {

        $emails_to_send = ['booking_cancelled'];

        foreach ($this->emails as $email) {
            if (!in_array($this->get_field('when_to_send', $email->get('id')), $emails_to_send)) {
                continue;
            }

            $this->send_email($email->get('id'));
        }
    }

    /**
     * Send all the 'cancellation request created' emails
     * 
     */
    public function send_cancellation_request_created_emails()
    {

        $emails_to_send = ['cancellation_request_created'];

        foreach ($this->emails as $email) {
            if (!in_array($this->get_field('when_to_send', $email->get('id')), $emails_to_send)) {
                continue;
            }

            $this->send_email($email->get('id'));
        }
    }

    /**
     * Send all the 'cancellation request approved' emails
     * 
     */
    public function send_cancellation_request_approved_emails()
    {

        $emails_to_send = ['cancellation_request_approved'];

        foreach ($this->emails as $email) {
            if (!in_array($this->get_field('when_to_send', $email->get('id')), $emails_to_send)) {
                continue;
            }

            $this->send_email($email->get('id'));
        }
    }


    /**
     * Send all the 'cancellation request declined' emails
     * 
     */
    public function send_cancellation_request_declined_emails()
    {

        $emails_to_send = ['cancellation_request_declined'];

        foreach ($this->emails as $email) {
            if (!in_array($this->get_field('when_to_send', $email->get('id')), $emails_to_send)) {
                continue;
            }

            $this->send_email($email->get('id'));
        }
    }

    /**
     * Send all the 'automatic refund processes' emails
     * 
     */
    public function send_automatic_refund_processed_emails()
    {

        $emails_to_send = ['automatic_refund_processed'];

        foreach ($this->emails as $email) {
            if (!in_array($this->get_field('when_to_send', $email->get('id')), $emails_to_send)) {
                continue;
            }

            $this->send_email($email->get('id'));
        }
    }

    /**
     * Send all the 'booking created' emails
     * 
     */
    public function send_booking_created_emails()
    {

        $emails_to_send = ['booking_created'];

        foreach ($this->emails as $email) {
            if (!in_array($this->get_field('when_to_send', $email->get('id')), $emails_to_send)) {
                continue;
            }

            $this->send_email($email->get('id'));
        }
    }

    /**
     * Send all the 'final payment' emails
     * 
     */
    public function send_final_payment_emails()
    {

        $emails_to_send = ['final_payment_made'];

        foreach ($this->emails as $email) {
            if (!in_array($this->get_field('when_to_send', $email->get('id')), $emails_to_send)) {
                continue;
            }

            $this->send_email($email->get('id'));
        }
    }

    /**
     * Send all the 'final payment' emails
     * 
     */
    public function send_payment_failed_emails()
    {

        $emails_to_send = ['payment_failed'];

        foreach ($this->emails as $email) {
            if (!in_array($this->get_field('when_to_send', $email->get('id')), $emails_to_send)) {
                continue;
            }

            $this->send_email($email->get('id'));
        }
    }

    /**
     * Schedule all the emails
     * 
     */
    public function schedule_emails()
    {

        // If the form no longer exists, exit.
        if (!$this->form) {
            return false;
        }

        // Check if a email was submitted
        $has_email = false;
        foreach ($this->form_fields as $field) {
            if ($field['type'] == 'email' && isset($field['user_value']) && $field['user_value']) {
                $has_email = true;
                break;
            }
        }

        if ($has_email == false) {
            return false;
        }

        // Which emails to schedule
        $emails_to_send = ['after_created', 'before_start', 'before_end', 'after_start', 'after_end'];

        foreach ($this->emails as $email) {
            $when_to_send = $this->get_field('when_to_send', $email->get('id'));

            if (!in_array($when_to_send, $emails_to_send)) {
                continue;
            }

            $delivery_date = wpbs_get_email_delivery_date($this->booking, $email);

            // Do not schedule emails in the past
            if (!$this->send_past_emails && $delivery_date < current_time('timestamp')) {
                continue;
            }


            // Schedule the email
            $schedule = wp_schedule_single_event($delivery_date, 'wpbs_send_scheduled_email_notification', array($this->booking_id, $email->get('id')), true);

            if (is_wp_error($schedule)) {

                wpbs_insert_notification([
                    'notification_group' => 'notification',
                    'booking_id' => $this->booking_id,
                    'object_id' => $email->get('id'),
                    'notification_type' => 'scheduling_email_failed',
                    'notification_status' => 'active',
                    'details' => $schedule->get_error_message(),
                    'date_created' => date('Y-m-d H:i:s', current_time('timestamp'))
                ]);
            }
        }
    }

    /**
     * Send an email
     * 
     * @param int $email_id
     * 
     */
    public function send_email($email_id)
    {
        switch_to_locale(wpbs_language_to_locale($this->language));

        $email = wpbs_get_email($email_id);

        if ($this->booking->get('status') == 'trash') {
            return false;
        }

        if ($email->get('status') == 'trash') {
            return false;
        }

        if (!$email->get('is_enabled')) {
            return false;
        }


        do_action('wpbs_email_notifications_before', $email, $this->booking, $this->language);

        $mailer = new WPBS_Mailer($this->booking);

        // Set Fields
        $mailer->type = $email->get('name');
        $mailer->email_id = $email_id;

        $mailer->send_to = $this->email_tags->parse($this->get_field('send_to', $email_id));
        $mailer->send_to_cc = $this->email_tags->parse($this->get_field('send_to_cc', $email_id));
        $mailer->send_to_bcc = $this->email_tags->parse($this->get_field('send_to_bcc', $email_id));
        $mailer->from_name = $this->email_tags->parse($this->get_field('from_name', $email_id));
        $mailer->from_email = $this->email_tags->parse($this->get_field('from_email', $email_id));
        $mailer->reply_to = $this->email_tags->parse($this->get_field('reply_to', $email_id));
        $mailer->subject = $this->email_tags->parse($this->get_field('subject', $email_id));
        $mailer->message = $this->email_tags->parse(do_shortcode(nl2br($this->get_field('message', $email_id))));

        /**
         * Include the "Manage Booking" Link
         * 
         */
        if (wpbs_get_email_meta($email_id, 'notification_manage_booking_link', true) == 'on') {
            $manage_booking_url = apply_filters(
                'wpbs_email_manage_booking_url',
                add_query_arg(
                    array(
                        'page' => 'wpbs-calendars',
                        'subpage' => 'edit-calendar',
                        'calendar_id' => $this->calendar->get('id'),
                        'booking_id' => $this->booking_id
                    ),
                    admin_url('admin.php')
                ),
                $this->calendar->get('id'),
                $this->booking_id
            );

            $manage_booking_label = apply_filters('wpbs_email_manage_booking_label', __('Manage Booking in WP Admin', 'wp-booking-system'));

            $mailer->message = '<a style="display:block; text-align:center;" href="' . $manage_booking_url . '">' . $manage_booking_label . '</a>' . $mailer->message;
        }


        // Send it!
        $mailer->send();

        // Delete attachments
        wpbs_delete_temporary_files($this->booking_id);
    }

    /**
     * Helper function to get the translated value of a field
     *
     * @param string $field
     * @param int $email_id
     *
     * @return string
     *
     */
    protected function get_field($field, $email_id)
    {
        return wpbs_get_translated_email_meta($email_id, 'notification_' . $field, $this->language);
    }

   
}
