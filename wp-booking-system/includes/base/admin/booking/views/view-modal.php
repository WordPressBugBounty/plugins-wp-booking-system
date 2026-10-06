    <div id="wpbs-booking-details-modal-inner" class="">
        <div class="wpbs-plugin-wrapper">
            <div class="wpbs-box">

                <div class="wpbs-box-header ">
                    <a href="#" id="wpbs-booking-details-modal-close"><i class="wpbs-icon-close"></i></a>
                    <h1>
                        <i class="wpbs-icon-calendar"></i> <?php echo __('Booking #', 'wp-booking-system'); ?><?php echo $this->booking->get('id'); ?>
                        <?php do_action('wpbs_booking_modal_after_title', $this->booking); ?>
                    </h1>

                    <div class="wpbs-booking-buttons">
                        
                        <a href="javascript:window.print();" class="wpbs-secondary-button wpbs-booking-print"><i class="wpbs-icon-print"></i> <?php echo __('Print', 'wp-booking-system') ?></a>
                    </div>

                </div>


                <!-- Tab Navigation -->
                <div class="wpbs-tabs-nav">
                    <nav class="wpbs-tabs-menu">
                        <ul>
                            <?php foreach ($this->tabs as $tab_slug => $tab_name) : ?>
                                <?php $active_class = $tab_slug == $this->get_active_tab() ? 'wpbs-nav-tab-active' : ''; ?>
                                <li><a href="#" data-tab="<?php echo $tab_slug; ?>" class="wpbs-nav-tab <?php echo $active_class; ?>"><?php echo $tab_name; ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </nav>
                </div>


                <!-- Content -->
                <div class="wpbs-box-content">

                    <!-- Tabs Navigation -->
                    <div class="wpbs-tabs-wrapper">

                        <?php foreach ($this->tabs as $tab_slug => $tab_name) : ?>

                            <?php $active_class = $tab_slug == $this->get_active_tab() ? 'wpbs-active' : ''; ?>

                            <div class="wpbs-tab wpbs-tab-<?php echo $tab_slug; ?> <?php echo $active_class; ?>" data-tab="<?php echo $tab_slug; ?>">
                                <?php
                                switch ($tab_slug) {
                                    case 'manage-booking':
                                        include 'view-modal-manage-booking.php';
                                        break;

                                    case 'booking-details':
                                        include 'view-modal-booking-details.php';
                                        break;

                                    case 'email-customer':
                                        include 'view-modal-email-customer.php';
                                        break;

                                    case 'email-logs':
                                        include 'view-modal-email-logs.php';
                                        break;

                                    case 'notes':
                                        include 'view-modal-notes.php';
                                        break;

                                    default:
                                        /**
                                         * Action to dynamically add content for each tab
                                         *
                                         */
                                        do_action('wpbs_booking_modal_tab_' . $tab_slug, $this->booking, $this->calendar);
                                }
                                ?>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
        </div>
    </div>