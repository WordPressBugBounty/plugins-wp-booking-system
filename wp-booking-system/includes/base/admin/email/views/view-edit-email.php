<?php
$email_fields = wpbs_form_get_email_fields($form_data);
$email_meta = wpbs_get_email_meta($email_id);
?>

<form method="POST" action="" autocomplete="off">

    <!-- Plugin Sticky Header -->
    <div class="wpbs-plugin-sticky-header wpbs-plugin-sticky-header-in-tab">

        <!-- Page Title -->
        <span class="wpbs-page-heading">
            <?php echo __('Edit Email', 'wp-booking-system'); ?>

            <!-- Form Title -->
            <div id="titlediv" class="wpbs-page-heading-editable">
                <div id="titlewrap">
                    <div class="wpbs-form-field">
                        <div class="wpbs-input">
                            <input type="text" name="notification_email_name" size="30" value="<?php echo esc_attr($email->get('name')) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <span class="wpbs-heading-tag"><strong><?php printf(__('Email ID: %d', 'wp-booking-system'), $email_id); ?></strong></span>

        </span>

        <!-- Buttons -->
        <div class="wpbs-header-buttons">

            <!-- Back Button -->
            <a href="<?php echo add_query_arg(array('page' => 'wpbs-forms', 'subpage' => 'edit-form', 'form_id' => $form_id, 'tab' => 'email-notifications'), admin_url('admin.php')); ?>" class="wpbs-secondary-button"><?php echo __('Back to all emails', 'wp-booking-system') ?></a>
            <a href="#" class="wpbs-primary-button wpbs-custom-save-button"><?php echo __('Save Changes', 'wp-booking-system'); ?></a>

        </div>

    </div>

    <div class="wpbs-dynamic-tags-wrapper">

        <!-- Dynamic Tags -->
        <div class="wpbs-dynamic-tags">
            <h2 class="title"><?php echo __('Dynamic Tags', 'wp-booking-system'); ?></h2>
            <p><?php echo __('You can use these dynamic tags in any of the fields. They will be replaced with the values submitted in the form.', 'wp-booking-system'); ?></p>
            <?php wpbs_output_email_tags($form_data); ?>
        </div>

        <div class="wpbs-tab-inner">

            <h2><?php _e('Email Content', 'wp-booking-system') ?></h2>

            <!-- Send To Type -->
            <?php $send_to_type = (!empty($email_meta['notification_send_to_type'][0])) ? esc_attr($email_meta['notification_send_to_type'][0]) : 'field'; ?>
            <div class="wpbs-form-field">
                <label class="wpbs-label" for=""><?php echo __('Send To', 'wp-booking-system'); ?></label>

                <div class="wpbs-input wpbs-input-inline-inputs">
                    <div class="wpbs-input-radio email-notification-send-to">
                        <input type="radio" <?php echo checked('field', $send_to_type, true) ?> name="notification_send_to_type" value="field" id="send-to-form-field">
                        <label for="send-to-form-field"><?php echo __('Select form field', 'wp-booking-system'); ?></label>
                    </div>
                    <div class="wpbs-input-radio email-notification-send-to">
                        <input type="radio" <?php echo checked('email', $send_to_type, true) ?> name="notification_send_to_type" value="email" id="send-to-email-address">
                        <label for="send-to-email-address"><?php echo __('Enter email address', 'wp-booking-system'); ?></label>
                    </div>
                </div>
            </div>

            <!-- Send To -->
            <div class="wpbs-form-field">

                <div class="wpbs-input wpbs-email-sent-to-field" <?php echo $send_to_type == 'email' ? 'style="display:none;"' : ''; ?>>
                    <label class="wpbs-label" for="notification_send_to_field"><?php echo __('Recipient Email Field', 'wp-booking-system'); ?></label>
                    <?php $send_to = (!empty($email_meta['notification_send_to_field'][0])) ? esc_attr($email_meta['notification_send_to_field'][0]) : ''; ?>
                    <?php if ($email_fields): ?>
                        <select name="notification_send_to_field" id="notification_send_to_field">
                            <?php foreach ($email_fields as $email_field): ?>
                                <?php $label = isset($email_field['values']['default']['label']) && !empty($email_field['values']['default']['label']) ? $email_field['values']['default']['label'] : 'no-label'; ?>
                                <option <?php echo ($send_to == '{' . $email_field['id'] . ':' . $label . '}') ? 'selected' : ''; ?> value="<?php echo '{' . $email_field['id'] . ':' . $label . '}'; ?>"><?php echo '{' . $email_field['id'] . ':' . $label . '}'; ?></option>
                            <?php endforeach ?>
                        </select>
                    <?php else: ?>
                        <div class="wpbs-page-notice wpbs-page-notice-email-domain notice-error">
                            <p><?php echo __('You must add an Email field to your form in order to send emails to your customers.', 'wp-booking-system'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="wpbs-input wpbs-email-sent-to-email" <?php echo $send_to_type == 'field' ? 'style="display:none;"' : ''; ?>>
                    <label class="wpbs-label" for="notification_send_to_email"><?php echo __('Recipient Email Address', 'wp-booking-system'); ?></label>
                    <input name="notification_send_to_email" type="text" id="notification_send_to_email" value="<?php echo (!empty($email_meta['notification_send_to_email'][0])) ? esc_attr($email_meta['notification_send_to_email'][0]) : ''; ?>">
                </div>


                <a href="#" class="wpbs-link-button wpbs-form-translations-toggle"><?php echo __('Options', 'wp-booking-system'); ?> <i class="wpbs-icon-angle-down"></i></a>

                <div class="wpbs-form-translations wpbs-form-fields-indent">
                    <!-- CC -->
                    <div class="wpbs-form-field">
                        <label class="wpbs-label" for="notification_send_to_cc"><?php echo __('CC', 'wp-booking-system'); ?></label>
                        <div class="wpbs-input">
                            <input name="notification_send_to_cc" type="text" id="notification_send_to_cc" value="<?php echo (!empty($email_meta['notification_send_to_cc'][0])) ? esc_attr($email_meta['notification_send_to_cc'][0]) : ''; ?>">
                        </div>
                    </div>

                    <!-- CC -->
                    <div class="wpbs-form-field">
                        <label class="wpbs-label" for="notification_send_to_bcc"><?php echo __('BCC', 'wp-booking-system'); ?></label>
                        <div class="wpbs-input">
                            <input name="notification_send_to_bcc" type="text" id="notification_send_to_bcc" value="<?php echo (!empty($email_meta['notification_send_to_bcc'][0])) ? esc_attr($email_meta['notification_send_to_bcc'][0]) : ''; ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- From Name -->
            <div class="wpbs-form-field">
                <label class="wpbs-label" for="notification_from_name"><?php echo __('From Name', 'wp-booking-system'); ?></label>

                <div class="wpbs-input">
                    <input name="notification_from_name" type="text" id="notification_from_name" value="<?php echo (!empty($email_meta['notification_from_name'][0])) ? esc_attr($email_meta['notification_from_name'][0]) : (isset($settings['default_from_name']) ? esc_attr($settings['default_from_name']) : ''); ?>">
                </div>
            </div>

            <!-- From Email -->
            <?php $from_email = (!empty($email_meta['notification_from_email'][0])) ? esc_attr($email_meta['notification_from_email'][0]) : (isset($settings['default_from_email']) ? esc_attr($settings['default_from_email']) : '') ?>
            <div class="wpbs-form-field">
                <label class="wpbs-label" for="notification_from_email"><?php echo __('From Email', 'wp-booking-system'); ?></label>

                <div class="wpbs-input">
                    <input name="notification_from_email" type="text" id="notification_from_email" value="<?php echo $from_email; ?>">
                </div>
            </div>

            <?php wpbs_same_email_domain_notice($from_email); ?>

            <!-- Reply To -->
            <div class="wpbs-form-field">
                <label class="wpbs-label" for="notification_reply_to"><?php echo __('Reply To', 'wp-booking-system'); ?></label>

                <div class="wpbs-input">
                    <input name="notification_reply_to" type="text" id="notification_reply_to" value="<?php echo (!empty($email_meta['notification_reply_to'][0])) ? esc_attr($email_meta['notification_reply_to'][0]) : (isset($settings['default_reply_to']) ? esc_attr($settings['default_reply_to']) : ''); ?>">
                </div>
            </div>

            <!-- Subject -->
            <div class="wpbs-form-field">
                <label class="wpbs-label" for="notification_subject"><?php echo __('Subject', 'wp-booking-system'); ?></label>

                <div class="wpbs-input">
                    <input name="notification_subject" type="text" id="notification_subject" value="<?php echo (!empty($email_meta['notification_subject'][0])) ? esc_attr($email_meta['notification_subject'][0]) : ''; ?>">
                </div>

                <?php if (wpbs_translations_active()): ?><a href="#" class="wpbs-link-button wpbs-form-translations-toggle"><?php echo __('Translations', 'wp-booking-system'); ?> <i class="wpbs-icon-angle-down"></i></a><?php endif ?>

                <?php if (wpbs_translations_active()): ?>
                    <!-- Required Field Translations -->
                    <div class="wpbs-form-translations wpbs-form-fields-indent">
                        <?php foreach ($active_languages as $language): ?>
                            <div class="wpbs-form-field wpbs-small-margin">
                                <div class="wpbs-input">
                                    <div class="wpbs-input-wrapper">
                                        <span class="wpbs-input-symbol wpbs-input-symbol-left wpbs-flag-symbol">
                                            <span><img src="<?php echo WPBS_PLUGIN_DIR_URL; ?>/assets/img/flags/<?php echo $language; ?>.png" /> <?php echo strtoupper($language); ?></span>
                                        </span>
                                        <input name="notification_subject_translation_<?php echo $language; ?>" type="text" id="notification_subject_translation_<?php echo $language; ?>" value="<?php echo (!empty($email_meta['notification_subject_translation_' . $language][0])) ? esc_attr($email_meta['notification_subject_translation_' . $language][0]) : ''; ?>">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif ?>
            </div>

            <!-- Message -->
            <div class="wpbs-form-field">
                <label class="wpbs-label" for="notification_message"><?php echo __('Message', 'wp-booking-system'); ?></label>

                <div class="wpbs-input">
                    <?php wp_editor((!empty($email_meta['notification_message'][0])) ? $email_meta['notification_message'][0] : '', 'notification_message', array('teeny' => true, 'textarea_rows' => 10, 'media_buttons' => false)) ?>
                </div>

                <?php if (wpbs_translations_active()): ?><a href="#" class="wpbs-link-button wpbs-form-translations-toggle"><?php echo __('Translations', 'wp-booking-system'); ?> <i class="wpbs-icon-angle-down"></i></a><?php endif ?>

                <?php if (wpbs_translations_active()): ?>
                    <div class="wpbs-form-translations wpbs-form-fields-indent">
                        <?php foreach ($active_languages as $language): ?>
                            <div class="wpbs-form-field wpbs-absolute-label">
                                <label class="wpbs-label"><img src="<?php echo WPBS_PLUGIN_DIR_URL; ?>/assets/img/flags/<?php echo $language; ?>.png" /> <?php echo $languages[$language]; ?></label>
                                <div class="wpbs-input">
                                    <?php wp_editor((!empty($email_meta['notification_message_translation_' . $language][0])) ? $email_meta['notification_message_translation_' . $language][0] : '', 'notification_message_translation_' . $language, array('teeny' => true, 'textarea_rows' => 10, 'media_buttons' => false)) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <hr>
            <h2><?php _e('Attachments', 'wp-booking-system') ?></h2>

            <!-- Enable iCalendar Attachment -->
            <div class="wpbs-form-field">
                <label class="wpbs-label" for="notification_ical_file">
                    <?php echo __('iCalendar Event', 'wp-booking-system'); ?>
                </label>

                <div class="wpbs-input">
                    <label for="notification_ical_file" class="wpbs-checkbox-switch">
                        <input type="hidden" name="notification_ical_file" value="0">
                        <input data-target="#wpbs-email-ical-file" name="notification_ical_file" type="checkbox" id="notification_ical_file" class="wpbs-settings-toggle wpbs-settings-wrap-toggle" <?php echo (!empty($email_meta['notification_ical_file'][0])) ? 'checked' : ''; ?>>
                        <div class="wpbs-checkbox-slider"></div>
                    </label>
                </div>
                <div class="wpbs-label-description"><?php echo __("Attach an iCalendar event file to the email.", 'wp-booking-system'); ?></div>

            </div>

            <!-- iCalendar Attachment Fields -->
            <div id="wpbs-email-ical-file" class="wpbs-email-ical-file wpbs-form-fields-indent wpbs-settings-wrapper <?php echo (!empty($email_meta['notification_ical_file'][0])) ? 'wpbs-settings-wrapper-show' : ''; ?>">

               <?php include WPBS_PLUGIN_DIR . 'includes/base/admin/upgrade-to-premium-cta.php'; ?>

            </div>

            <?php do_action('wpbs_edit_email_notification_attachments', $email_id, $email_meta) ?>

        </div>
    </div>

    <br />
    <div class="wpbs-tab-inner">
        <hr>
        <h2><?php _e('Email Options', 'wp-booking-system') ?></h2>

        <!-- When to send -->
        <div class="wpbs-form-field">

            <div class="wpbs-input">
                <label class="wpbs-label" for="notification_when_to_send"><?php echo __('When to Send', 'wp-booking-system'); ?></label>
                <select name="notification_when_to_send" id="notification_when_to_send">
                        <option value="booking_created" <?php echo selected($email_meta['notification_when_to_send'][0], 'booking_created'); ?>><?php _e('When the booking is created', 'wp-booking-system') ?></option>
                        <option value="booking_accepted" <?php echo selected($email_meta['notification_when_to_send'][0], 'booking_accepted'); ?>><?php _e('When the booking is accepted', 'wp-booking-system') ?></option>
                    
                </select>
            </div>

        </div>


        <!-- Enable Manage Booking Link -->
        <div class="wpbs-form-field">
            <label class="wpbs-label" for="notification_manage_booking_link">
                <?php echo __('Include "Manage Booking" Link', 'wp-booking-system'); ?>
            </label>

            <div class="wpbs-input">
                <label for="notification_manage_booking_link" class="wpbs-checkbox-switch">
                    <input type="hidden" name="notification_manage_booking_link" value="0">
                    <input name="notification_manage_booking_link" type="checkbox" id="notification_manage_booking_link" class="wpbs-settings-toggle" <?php echo (!empty($email_meta['notification_manage_booking_link'][0])) ? 'checked' : ''; ?>>
                    <div class="wpbs-checkbox-slider"></div>
                </label>
            </div>
            <div class="wpbs-label-description"><?php echo __("Add a link to the Booking Modal in you site. Only enable this option for emails sent to the website administrators or people who have access to the website's WP Admin Dashboard.", 'wp-booking-system'); ?></div>

        </div>


        <!-- Enable Conditional Logic -->
        <div class="wpbs-form-field">
            <label class="wpbs-label" for="notification_enable_conditional_logic">
                <?php echo __('Enable Conditional Logic', 'wp-booking-system'); ?>
            </label>

            <div class="wpbs-input">
                <label for="notification_enable_conditional_logic" class="wpbs-checkbox-switch">
                    <input type="hidden" name="notification_enable_conditional_logic" value="0">
                    <input data-target="#wpbs-email-conditional-logic" name="notification_enable_conditional_logic" type="checkbox" id="notification_enable_conditional_logic" class="wpbs-settings-toggle wpbs-settings-wrap-toggle" <?php echo (!empty($email_meta['notification_enable_conditional_logic'][0])) ? 'checked' : ''; ?>>
                    <div class="wpbs-checkbox-slider"></div>
                </label>
            </div>

        </div>

        <div id="wpbs-email-conditional-logic" class="wpbs-email-conditional-logic wpbs-logic-form-fields wpbs-form-fields-indent wpbs-settings-wrapper <?php echo (!empty($email_meta['notification_enable_conditional_logic'][0])) ? 'wpbs-settings-wrapper-show' : ''; ?>">

            <?php include WPBS_PLUGIN_DIR . 'includes/base/admin/upgrade-to-premium-cta.php'; ?>

        </div>

    </div>

    <!-- Hidden fields -->
    <input type="hidden" name="form_id" value="<?php echo $form_id; ?>" />
    <input type="hidden" name="email_id" value="<?php echo $email_id; ?>" />
    <input type="hidden" name="_wp_http_referer" value="<?php echo esc_attr(wp_unslash($_SERVER['REQUEST_URI'])); ?>">

    <!-- Nonce -->
    <?php wp_nonce_field('wpbs_edit_email_notification', 'wpbs_token', false); ?>
    <input type="hidden" name="wpbs_action" value="edit_email_notification" />

    <!-- Save Page Button -->
    <div class="wpbs-box-footer-wrapper">
        <div class="wpbs-box-footer">
            <input type="submit" class="wpbs-primary-button wpbs-save-form" value="<?php echo __('Save Email Notification', 'wp-booking-system'); ?>" />
        </div>
    </div>

</form>