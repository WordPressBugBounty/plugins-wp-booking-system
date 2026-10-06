<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;


/**
 * Dashboard card: Upcoming Bookings
 *
 */
class WPBS_Admin_Dashboard_Card_Upcoming_Bookings extends WPBS_Admin_Dashboard_Card
{

    /**
     * Initialize the card.
     *
     */
    protected function init()
    {

        $this->slug    = 'wpbs_card_upcoming_bookings';
        $this->name    = __('Upcoming Bookings', 'wp-booking-system');
        $this->context = 'primary';
    }


    /**
     * Output the card's content.
     *
     */
    public function output()
    {

        $bookings = $this->get_data();


        if ($bookings) :
?>

            <div class="wpbs-card-table-overflow">
                <table class="wpbs-card-table-full-width wpbs-card-table-overflow">

                    <thead>
                        <tr>
                            <th class="wpbs-column wpbs-column-booking-id"><?php echo __('Booking', 'wp-booking-system'); ?></th>
                            <th class="wpbs-column wpbs-column-calendar-name"><?php echo __('Calendar', 'wp-booking-system'); ?></th>
                            <th class="wpbs-column"><?php echo __('Start Date', 'wp-booking-system'); ?></th>
                            <th class="wpbs-column"><?php echo __('End Date', 'wp-booking-system'); ?></th>
                            <th class="wpbs-column"><?php echo __('Stay Length', 'wp-booking-system'); ?></th>
                           
                            <th class="wpbs-column wpbs-column-notes"><!-- --></th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($bookings as $booking) : ?>

                            <tr>
                                <td class="wpbs-column-booking-id">
                                    <?php if ($booking['id'] == 0): ?>
                                        <span class="wpbs-list-table-id wpbs-list-table-id-icalendar">ICAL</span>
                                    <?php else: ?>
                                        <a data-id="<?php echo $booking['id']; ?>" class="wpbs-open-booking-details" target="_blank" href="<?php echo add_query_arg(array('page' => 'wpbs-calendars', 'subpage' => 'edit-calendar', 'calendar_id' => $booking['calendar_id'], 'booking_id' => $booking['id']), admin_url('admin.php')); ?>"><span class="wpbs-list-table-id <?php echo $booking['status'] == 'accepted' ? 'wpbs-booking-color-' . $booking['id'] % 10 : ''; ?>">#<?php echo $booking['id']; ?></span></a>
                                    <?php endif; ?>
                                </td>
                                <td class="wpbs-column-calendar-name">
                                    <a target="_blank" href="<?php echo add_query_arg(array('page' => 'wpbs-calendars', 'subpage' => 'edit-calendar', 'calendar_id' => $booking['calendar_id']), admin_url('admin.php')); ?>"><?php echo $booking['calendar_name']; ?></a>
                                </td>
                                <td>
                                    <?php if (wp_date('Ymd') == date('Ymd', strtotime($booking['start_date']))) : ?>
                                        <strong><?php echo __('Today', 'wp-booking-system') ?></strong>
                                    <?php elseif (wp_date('Ymd') == date('Ymd', strtotime($booking['start_date']) - DAY_IN_SECONDS)) : ?>
                                        <strong><?php echo __('Tomorrow', 'wp-booking-system') ?></strong>
                                    <?php else : ?>
                                        <?php echo wpbs_date_i18n(get_option('date_format'), strtotime($booking['start_date'])) ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo wpbs_date_i18n(get_option('date_format'), strtotime($booking['end_date'])) ?>
                                </td>
                                <td>
                                    <?php echo $booking['stay_length']; ?>
                                </td>
                             
                                <td>
                                    <?php echo $booking['notes']; ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>
            </div>

        <?php else : ?>
            <div class="wpbs-dashboard-no-entries">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                    <path d="M160 0c13.3 0 24 10.7 24 24V64H328V24c0-13.3 10.7-24 24-24s24 10.7 24 24V64h40c35.3 0 64 28.7 64 64v16 48V448c0 35.3-28.7 64-64 64H96c-35.3 0-64-28.7-64-64V192 144 128c0-35.3 28.7-64 64-64h40V24c0-13.3 10.7-24 24-24zM432 192H80V448c0 8.8 7.2 16 16 16H416c8.8 0 16-7.2 16-16V192zm-95 89l-47 47 47 47c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0l-47-47-47 47c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l47-47-47-47c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47 47-47c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
                </svg>
                <p><?php echo __('No bookings coming up in the next month.', 'wp-booking-system') ?></p>
            </div>
<?php endif;
    }

    /**
     * Get data
     * 
     */
    private function get_data()
    {

        $calendars = wpbs_get_calendars();
        $calendar_arr = [];
        $settings = get_option('wpbs_settings', array());

        foreach ($calendars as $calendar) {
            $calendar_arr[$calendar->get('id')] = $calendar;
           
        }


        $i = 0;
        $limit = apply_filters('wpbs_dashboard_card_items_limit', 50);

        $bookings = wpbs_get_bookings(array('status' => ['accepted', 'pending'], 'custom_query' => 'AND start_date >= "' . wp_date('Y-m-d') . ' 00:00:00"', 'orderby' => 'start_date', 'order' => 'asc', 'number' => $limit));

        $data = [];


        foreach ($bookings as $booking) {
            $calendar = $calendar_arr[$booking->get('calendar_id')];
            if (!$calendar) {
                continue;
            }


            $data[strtotime($booking->get('start_date')) + $i] = [
                'id' => $booking->get('id'),
                'status' => $booking->get('status'),
                'calendar_id' => $booking->get('calendar_id'),
                'calendar_name' => $calendar->get_name(),
                'start_date' => $booking->get('start_date'),
                'end_date' => $booking->get('end_date'),
                'stay_length' => $this->get_stay_length($booking->get('start_date'), $booking->get('end_date'), wpbs_get_booking_meta($booking->get('id'), 'selection_style', true)),
                'notes' => $this->get_notes($booking)
            ];

            $i++;
        }

        ksort($data);

        $data = array_slice($data, 0, $limit);

        return $data;
    }

    /**
     * Get the stay length
     * 
     */
    private function get_stay_length($start_date, $end_date, $selection_style)
    {

        $difference = (strtotime($end_date) - strtotime($start_date)) / DAY_IN_SECONDS;

        $abbr = '';
        $abbr .= ($difference + 1) . ' ' . (($difference + 1) == 1 ? __('day', 'wp-booking-system') : __('days', 'wp-booking-system'));
        $abbr .= ' / ';
        $abbr .= $difference . ' ' . ($difference == 1 ? __('night', 'wp-booking-system') : __('nights', 'wp-booking-system'));

        $output = '<abbr title="' . $abbr . '">';
        if ($difference == 0) {
            $output .= '1 ' . __('day', 'wp-booking-system');
        } elseif ($selection_style  == 'normal') {
            $output .= ($difference + 1) . ' ' . (($difference + 1) == 1 ? __('day', 'wp-booking-system') : __('days', 'wp-booking-system'));
        } else {
            $output .= $difference . ' ' . ($difference == 1 ? __('night', 'wp-booking-system') : __('nights', 'wp-booking-system'));
        }
        $output .= '</abbr>';

        return $output;
    }

    /**
     * Get booking notes
     * 
     */
    private function get_notes($booking)
    {
        if (!wpbs_get_booking_meta($booking->get('id'), 'booking_notes', true)) {
            return '';
        }

        $notes =  '';
        foreach (wpbs_get_booking_meta($booking->get('id'), 'booking_notes', true) as $note) {
            $notes .= $note['note'] . PHP_EOL . '----------' . PHP_EOL;
        }
        $notes = trim($notes, '----------' . PHP_EOL);
        return '<abbr title="' .  esc_attr($notes) . '"><span class="dashicons dashicons-flag"></span></abbr>';
    }
}
