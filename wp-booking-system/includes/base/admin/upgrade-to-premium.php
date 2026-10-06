<?php

// Exit if accessed directly
if (! defined('ABSPATH')) exit;

$tabs = $this->get_tabs();
$active_tab = (! empty($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general');
$settings = get_option('wpbs_settings', array());

?>

<div class="wrap wpbs-wrap wpbs-wrap-settings">

    <?php wpbs_get_wp_admin_header(); ?>

    <div class="wpbs-plugin-sticky-header">
        <span class="wpbs-page-heading"><?php echo __('Upgrade to Premium', 'wp-booking-system'); ?></span>
        <div class="wpbs-header-buttons"></div>
    </div>

    <div class="wpbs-notifications">
        <h1 class="wp-heading-inline"></h1>
    </div>

    <div class="wpbs-plugin-wrapper wpbs-wrap-upgrade-to-premium">

        <h3>Take your booking system to the next level</h3>

        <div class="wpbs-premium-main">
            <ul>
                <li>
                    <strong><span class="dashicons dashicons-yes" aria-hidden="true"></span> Create unlimited calendars and forms</strong>
                    <p>Manage multiple properties, rentals or resources with as many booking calendars and forms as you need.</p>
                </li>

                <li>
                    <strong><span class="dashicons dashicons-yes" aria-hidden="true"></span> Add pricing to your bookings</strong>
                    <p>Set prices by day or season, charge based on the number of guests, add extra services, taxes, deposits and more.</p>
                </li>

                <li>
                    <strong><span class="dashicons dashicons-yes" aria-hidden="true"></span> Accept online and offline payments</strong>
                    <p>Let customers pay online using supported payment gateways, or offer offline payment methods such as bank transfer or payment on arrival.</p>
                </li>

                <li>
                    <strong><span class="dashicons dashicons-yes" aria-hidden="true"></span> Sync availability with other platforms</strong>
                    <p>Use iCalendar to keep availability synchronized with platforms such as Airbnb, Booking.com, Google Calendar and other compatible services.</p>
                </li>

                <li>
                    <strong><span class="dashicons dashicons-yes" aria-hidden="true"></span> Create advanced booking restrictions</strong>
                    <p>Control minimum and maximum stays, fixed booking lengths, arrival and departure days, advance booking periods, turnaround times and seasonal rules.</p>
                </li>

                <li>
                    <strong><span class="dashicons dashicons-yes" aria-hidden="true"></span> Manage all your bookings in one place</strong>
                    <p>Use the Booking Manager to view and manage bookings from all your calendars from one convenient dashboard.</p>
                </li>

                <li>
                    <strong><span class="dashicons dashicons-yes" aria-hidden="true"></span> Automate customer notifications</strong>
                    <p>Create and customize emails for new bookings, confirmations, booking updates, reminders and other important events.</p>
                </li>

                <li>
                    <strong><span class="dashicons dashicons-yes" aria-hidden="true"></span> See reports and booking statistics</strong>
                    <p>Get a clearer picture of your business with reports covering bookings, revenue and other useful performance data.</p>
                </li>

                <li>
                    <strong><span class="dashicons dashicons-yes" aria-hidden="true"></span> Control calendar access with User Management</strong>
                    <p>Choose which WordPress users can access your calendars and assign specific calendars to individual users when needed.</p>
                </li>

                <li>
                    <strong><span class="dashicons dashicons-yes" aria-hidden="true"></span> Display an overview calendar</strong>
                    <p>Combine availability from multiple calendars into one clear overview, making it easier to show and manage multiple rentals or resources.</p>
                </li>

                <li>
                    <strong><span class="dashicons dashicons-yes" aria-hidden="true"></span> Help customers find available rentals</strong>
                    <p>Let visitors search across multiple calendars and quickly find properties or resources available for their selected dates.</p>
                </li>

                <li>
                    <strong><span class="dashicons dashicons-yes" aria-hidden="true"></span> Unlock even more tools for your booking business</strong>
                    <p>Add discounts and coupons, invoices, contracts, SMS notifications, multiple currencies, WooCommerce Checkout, inventory management and more.</p>
                </li>
            </ul>

            <h3 class="wpbs-clear">
                Want to see Premium in action?
                <a href="https://www.wpbookingsystem.com/demo/" target="_blank" rel="noopener">Explore the live demos</a>.
            </h3>
        </div>

        <aside class="wpbs-premium-sidebar" aria-label="Premium upgrade options">
            <div class="cta">
                <h3>Ready to get more from WP Booking System?</h3>
                <p class="prices">Unlock advanced booking tools, pricing, integrations and more. Premium plans start at $79 per year.</p>
                <a class="wpbs-button" href="https://www.wpbookingsystem.com/pricing/?utm_source=wp-booking-system-free" target="_blank" rel="noopener">Explore Premium</a>
            </div>

            <div class="cta-2">
                <h4>Save 20% on your upgrade</h4>
                <p class="discount">
                    <button type="button" class="wpbs-select-on-click" aria-label="Copy discount code FREE-VERSION-UPGRADE">FREE-VERSION-UPGRADE</button>
                </p>
                <p class="wpbs-premium-copy-hint">Click the code to copy it.</p>
            </div>
        </aside>

    </div>
</div>