<?php
$booking_options = wpbs_get_calendar_meta($calendar_id, 'manual_booking_options', true);
$settings_configured = wpbs_is_add_booking_settings_configured($calendar_id);
$settings = get_option('wpbs_settings', array());
?>

<div id="wpbs-add-booking-modal-overlay">
    <div id="wpbs-add-booking-modal-inner">
        <div class="wpbs-plugin-wrapper">
            <div class="wpbs-box">
                <div class="wpbs-box-header">
                    <a href="#" id="wpbs-add-booking-modal-close"><i class="wpbs-icon-close"></i></a>
                    <h1>
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                            <path fill="currentColor" d="M336 292v24c0 6.6-5.4 12-12 12h-76v76c0 6.6-5.4 12-12 12h-24c-6.6 0-12-5.4-12-12v-76h-76c-6.6 0-12-5.4-12-12v-24c0-6.6 5.4-12 12-12h76v-76c0-6.6 5.4-12 12-12h24c6.6 0 12 5.4 12 12v76h76c6.6 0 12 5.4 12 12zm112-180v352c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V112c0-26.5 21.5-48 48-48h48V12c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v52h128V12c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v52h48c26.5 0 48 21.5 48 48zm-48 346V160H48v298c0 3.3 2.7 6 6 6h340c3.3 0 6-2.7 6-6z"></path>
                        </svg>
                        <?php echo __('Add Booking', 'wp-booking-system') ?>
                    </h1>
                    <button id="wpbs-add-booking-edit-options" class="wpbs-secondary-button <?php echo !$settings_configured ? 'wpbs-hidden' : ''; ?>"><?php echo __('Edit Calendar Options', 'wp-booking-system') ?></button>

                </div>

                <div class="wpbs-box-content">

                    <?php include WPBS_PLUGIN_DIR . 'includes/base/admin/upgrade-to-premium-cta.php'; ?>

                </div>
            </div>
        </div>
    </div>
</div>