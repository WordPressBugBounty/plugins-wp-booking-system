<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;


/**
 * Get important notifications
 * 
 * @return array
 * 
 */
function wpbs_get_dashboard_notifications()
{

    if (apply_filters('wpbs_disable_dashboard_notification_count', false) == true) {
        return [];
    }

    global $wpdb;

    /**
     * Only cache automatically generated notifications.
     */
    $notifications = get_transient('wpbs_dashboard_notifications_cache');

    if (!is_array($notifications)) {

        $notifications = [];

        $today_ymd = current_time('Ymd');

        /**
         * Get due part payments
         */
        $bookings = $wpdb->get_results(
            $wpdb->prepare(
                "
                SELECT 
                    bookings.id,
                    bookings.status,
                    sent_date.meta_value AS final_payment_email_sent_date
                FROM {$wpdb->prefix}wpbs_bookings AS bookings
                INNER JOIN {$wpdb->prefix}wpbs_booking_meta AS sent_meta
                    ON sent_meta.booking_id = bookings.id
                    AND sent_meta.meta_key = %s
                    AND sent_meta.meta_value = %s
                LEFT JOIN {$wpdb->prefix}wpbs_booking_meta AS sent_date
                    ON sent_date.booking_id = bookings.id
                    AND sent_date.meta_key = %s
                WHERE bookings.status != %s
                ",
                'final_payment_email_sent',
                '1',
                'final_payment_email_sent_date',
                'trash'
            )
        );

      

        /**
         * Cache only automatically generated notifications.
         */
        set_transient(
            'wpbs_dashboard_notifications_cache',
            $notifications,
            DAY_IN_SECONDS
        );
    }

    /**
     * Always fetch manually saved notifications fresh.
     */
    $notifications = array_merge(
        $notifications,
        wpbs_get_notifications([
            'group'  => 'notification',
            'status' => 'active',
        ])
    );

    /**
     * Sort cached + fresh notifications together.
     */
    usort($notifications, function ($a, $b) {
        return strcmp($b->get('date_created'), $a->get('date_created'));
    });

    return $notifications;
}

/**
 * Clear notifications cache after each payment update
 * 
 */
add_filter('wpbs_update_payment', function () {
    delete_transient('wpbs_dashboard_notifications_cache');
});

/**
 * Get the link to the booking modal
 * 
 */
function wpbs_dashboard_get_booking_href($notification, $booking)
{
    $notification_type = wpbs_get_notification_event_type($notification);

    $query_args = array(
        'page' => 'wpbs-calendars',
        'subpage' => 'edit-calendar',
        'calendar_id' => $booking->get('calendar_id'),
        'booking_id' => $booking->get('id')
    );

    if (strtolower($notification_type) == 'email') {
        $query_args['modal-tab'] = 'email-logs';
    }

    if (strtolower($notification_type) == 'payment') {
        $query_args['modal-tab'] = 'payment-details';
    }

    if (strtolower($notification_type) == 'sms') {
        $query_args['modal-tab'] = 'sms';
    }

    return add_query_arg($query_args, admin_url('admin.php'));
}
