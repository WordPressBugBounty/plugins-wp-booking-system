<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;


/**
 * Adds a new tab to the Settings page of the plugin
 *
 * @param array $tabs
 *
 * @return $tabs
 *
 */
function wpbs_submenu_page_settings_tabs_tools($tabs)
{

	$tabs['tools'] = __('Tools', 'wp-booking-system');

	return $tabs;
}
add_filter('wpbs_submenu_page_settings_tabs', 'wpbs_submenu_page_settings_tabs_tools', 100);


/**
 * Adds the HTML for the Tools tab
 *
 */
function wpbs_submenu_page_settings_tab_tools()
{

	include 'views/view-tools.php';
}
add_action('wpbs_submenu_page_settings_tab_tools', 'wpbs_submenu_page_settings_tab_tools');

/**
 * Returns an array with the sub tabs for the Tools page
 *
 * @return array
 *
 */
function wpbs_get_tools_tabs()
{
	$tabs = array(
		'data-deletion' => __('Data Deletion', 'wp-booking-system'),
		'cron' => __('Emails & Cron', 'wp-booking-system'),
		'calendar' => __('Calendar Tools', 'wp-booking-system'),
	);

	/**
	 * Filter the tabs before returning
	 *
	 * @param array $tabs
	 *
	 */
	return apply_filters('wpbs_submenu_page_settings_tools_tabs', $tabs);
}

/**
 * Action that uninstalls the plugin
 *
 */
function wpbs_action_uninstall_plugin()
{

	// Verify for nonce
	if (empty($_GET['wpbs_token']) || !wp_verify_nonce($_GET['wpbs_token'], 'wpbs_uninstall_plugin'))
		return;

	/**
	 * Drop db tables
	 *
	 */
	global $wpdb;

	$registered_tables = wp_booking_system()->db;

	foreach ($registered_tables as $table)
		$wpdb->query("DROP TABLE IF EXISTS {$table->table_name}");

	/**
	 * Remove options
	 *
	 */
	delete_option('wpbs_version');
	delete_option('wpbs_first_activation');
	delete_option('wpbs_upgrade_5_0_0');
	delete_option('wpbs_upgrade_5_0_0_skipped');
	delete_option('wpbs_serial_key');
	delete_option('wpbs_registered_website_id');
	delete_option('wpbs_settings');


	/**
	 * Deactivate the plugin and redirect to Plugins
	 *
	 */
	deactivate_plugins(WPBS_BASENAME);

	wp_redirect(admin_url('plugins.php'));
	exit;
}
add_action('wpbs_action_uninstall_plugin', 'wpbs_action_uninstall_plugin');

/**
 * Action that wipes all booking data
 *
 */
function wpbs_action_wipe_booking_data()
{

	// Verify for nonce
	if (empty($_GET['wpbs_token']) || !wp_verify_nonce($_GET['wpbs_token'], 'wpbs_wipe_booking_data'))
		return;

	/**
	 * Truncate db tables
	 *
	 */
	global $wpdb;

	$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}wpbs_bookings");
	$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}wpbs_booking_meta");
	$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}wpbs_payments");
	$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}wpbs_notifications");

	// Remove existing cron jobs
	$crons = _get_cron_array();

	foreach ($crons as $timestamp => $cron) {

		if (isset($cron['wpbs_sms_reminder_notification'])) {
			unset($crons[$timestamp]['wpbs_sms_reminder_notification']);
		}
		if (isset($cron['wpbs_sms_follow_up_notification'])) {
			unset($crons[$timestamp]['wpbs_sms_follow_up_notification']);
		}
		if (isset($cron['wpbs_sms_payment_notification'])) {
			unset($crons[$timestamp]['wpbs_sms_payment_notification']);
		}
		if (isset($cron['wpbs_refund_security_deposit_cron'])) {
			unset($crons[$timestamp]['wpbs_refund_security_deposit_cron']);
		}
		if (isset($cron['wpbs_send_scheduled_email_notification'])) {
			unset($crons[$timestamp]['wpbs_send_scheduled_email_notification']);
		}
	}

	foreach ($crons as $timestamp => $cron) {
		if (!$cron) {
			unset($crons[$timestamp]);
		}
	}

	_set_cron_array($crons);

	wp_redirect(add_query_arg(array('page' => 'wpbs-settings', 'tab' => 'tools', 'wpbs_message' => 'booking_wipe_successful'), admin_url('admin.php')));
	exit;
}
add_action('wpbs_action_wipe_booking_data', 'wpbs_action_wipe_booking_data');