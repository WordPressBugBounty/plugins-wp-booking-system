<?php

// Exit if accessed directly
if (! defined('ABSPATH')) exit;
?>

<?php 
$active_languages = (!empty($settings['active_languages']) ? $settings['active_languages'] : array());
?>

<div class="wpbs-tab-inner">

    <?php

    /**
     * Hook to add extra fields at the top of the Booking Tab
     *
     * @param array $settings
     *
     */
    do_action('wpbs_submenu_page_settings_tab_booking_top', $settings);

    ?>

    <h2>
        <?php echo __('Booking Cancellation', 'wp-booking-system'); ?>
        <small class="wpbs-heading-description"><?php echo __('Allow customers to request the cancellation of their bookings.', 'wp-booking-system'); ?></small>
    </h2>

    <!-- Enable Cancellation -->
    <div class="wpbs-form-field">
        <label class="wpbs-label" for="booking_cancellation_enable">
            <?php echo __('Enable Booking Cancellation by Customer', 'wp-booking-system'); ?>
        </label>

        <div class="wpbs-input">
            <label for="booking_cancellation_enable" class="wpbs-checkbox-switch">
                <input type="hidden" name="wpbs_settings[booking_cancellation_enable]" value="0">
                <input data-target="#wpbs-booking-cancellation-wrapper" name="wpbs_settings[booking_cancellation_enable]" type="checkbox" id="booking_cancellation_enable" class="wpbs-settings-toggle wpbs-settings-wrap-toggle" <?php echo (!empty($settings['booking_cancellation_enable'])) ? 'checked' : ''; ?>>
                <div class="wpbs-checkbox-slider"></div>
            </label>
        </div>
    </div>

    <div id="wpbs-booking-cancellation-wrapper" class="wpbs-booking-cancellation-wrapper wpbs-settings-wrapper <?php echo (!empty($settings['booking_cancellation_enable'])) ? 'wpbs-settings-wrapper-show' : ''; ?>">

         <?php include WPBS_PLUGIN_DIR . 'includes/base/admin/upgrade-to-premium-cta.php'; ?>
         
    </div>

    <?php

    /**
     * Hook to add extra fields at the bottom of the Form Tab
     *
     * @param array $settings
     *
     */
    do_action('wpbs_submenu_page_settings_tab_bookingbottom', $settings);

    ?>
</div>

<!-- Save Page Button -->
<div class="wpbs-box-footer-wrapper">
    <div class="wpbs-box-footer">
        <input type="submit" class="wpbs-primary-button" value="<?php echo __('Save Settings', 'wp-booking-system'); ?>" />
    </div>
</div>