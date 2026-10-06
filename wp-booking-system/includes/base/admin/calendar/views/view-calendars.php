<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

?>

<div class="wrap wpbs-wrap wpbs-wrap-calendars">

	<?php wpbs_get_wp_admin_header(); ?>

	<!-- Plugin Sticky Header -->
	<div class="wpbs-plugin-sticky-header">

		<!-- Page Title -->
		<span class="wpbs-page-heading"><?php echo __('Calendars', 'wp-booking-system'); ?></span>

		<!-- Buttons -->
		<div class="wpbs-header-buttons">
			<a href="<?php echo add_query_arg(array('subpage' => 'add-calendar'), $this->admin_url); ?>" class="wpbs-primary-button"><?php echo __('Add New Calendar', 'wp-booking-system'); ?></a>
		</div>

	</div>

	<!-- WP Notifications -->
	<div class="wpbs-notifications">
		<h1 class="wp-heading-inline"></h1>
	</div>



	<!-- Body -->
	<div class="wpbs-plugin-wrapper">
		<div class="wpbs-box wpbs-transparent">

			<!-- Calendars List Table -->
			<form method="get">


				<div class="wpbs-box-header wpbs-box-toolbar">
					<?php $table = new WPBS_WP_List_Table_Calendars(); ?>
					<?php $table->views(); ?>
					<?php $table->search_box(__('Search Calendars', 'wp-booking-system'), 'wpbs-search-calendars'); ?>
				</div>

				<div class="wpbs-box-content wpbs-no-padding">
					<div class="wpbs-box-table-wrapper">

						<input type="hidden" name="page" value="wpbs-calendars" />
						<input type="hidden" name="paged" value="1">

						<?php
						$table->display();
						?>

					</div>
				</div>

			</form>

		</div>
		
		<?php include WPBS_PLUGIN_DIR . 'includes/base/admin/upgrade-to-premium-cta.php'; ?>

	</div>



</div>