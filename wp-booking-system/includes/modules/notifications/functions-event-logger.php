<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Log emails
 * 
 */
add_action('wpbs_mailer_end', function($mailer){

    wpbs_insert_notification([
        'notification_group' => 'event',
        'booking_id' => $mailer->booking->get('id'),
        'object_id' => $mailer->email_id,
        'notification_type' => 'email_sent',
        'notification_status' => 'active',
        'date_created' => date('Y-m-d H:i:s', current_time('timestamp'))
    ]);
});


/**
 * Log email error
 * 
 */
add_action('wpbs_mailer_email_failed_to_send', function($mailer){

    wpbs_insert_notification([
        'notification_group' => 'notification',
        'booking_id' => $mailer->booking->get('id'),
        'object_id' => $mailer->email_id,
        'notification_type' => 'email_failed',
        'notification_status' => 'active',
        'date_created' => date('Y-m-d H:i:s', current_time('timestamp'))
    ]);
    
});
