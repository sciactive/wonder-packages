<?php
/**
 * Save a work schedule for an employee.
 *
 * @package Components\calendar
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_calendar/managecalendar') )
	punt_user(null, pines_url('com_calendar', 'editcalendar'));

if (isset($_REQUEST['employee'])) {	
	$employee = com_hrm_employee::factory((int) $_REQUEST['employee']);
	if (!isset($employee->guid)) {
		pines_error('The specified employee for this schedule does not exist.');
		pines_redirect(pines_url('com_calendar', 'editcalendar'));
		return;
	}
	
	if (empty($_REQUEST['dates'])) {
		pines_notice('No dates were specified to schedule work for.');
		pines_redirect(pines_url('com_calendar', 'editcalendar', array('employee' => $_REQUEST['employee'])));
		return;
	}
	$dates = explode(',', $_REQUEST['dates']);
	$all_day = ($_REQUEST['all_day'] == 'ON');
	$start_hour = ($_REQUEST['time_start_ampm'] == 'am') ? (int) $_REQUEST['time_start_hour'] : (int) $_REQUEST['time_start_hour'] + 12;
	$end_hour = ($_REQUEST['time_end_ampm'] == 'am') ? (int) $_REQUEST['time_end_hour'] : (int) $_REQUEST['time_end_hour'] + 12;
	$location = $employee->group;
	// Enter schedule in employee's timezone.
	$cur_timezone = date_default_timezone_get();
	date_default_timezone_set($employee->get_timezone());
	foreach ($dates as $cur_date) {
		$cur_timestamp = strtotime($cur_date);
		$event = com_calendar_event::factory();
		$event->all_day = $all_day;
		$event->employee = $employee;
		$event->title = $employee->name;
		$event->color = $employee->color;
		$event->information = 'Scheduled shift';
		$event_month = date('n', $cur_timestamp);
		$event_day = date('j', $cur_timestamp);
		$event_year = date('Y', $cur_timestamp);
		if ($event->all_day) {
			$event->start = mktime(0, 0, 0, $event_month, $event_day, $event_year);
			$event->end = mktime(23, 59, 59, $event_month, $event_day, $event_year);
		} else {
			$event->start = mktime($start_hour, (int) $_REQUEST['time_start_minute'], 0, $event_month, $event_day, $event_year);
			$event->end = mktime($end_hour, (int) $_REQUEST['time_end_minute'], 0, $event_month, $event_day, $event_year);
		}

		if ($event->all_day) {
			$days = ceil(($event->end - $event->start) / 86400);
			$event->scheduled = isset($event->employee->workday_length) ? $event->employee->workday_length : $_->config->com_calendar->workday_length;
			$event->scheduled *= 3600 * $days;
		} else {
			$event->scheduled = $event->end - $event->start;
		}

		$event->ac->other = 1;

		if ($event->save()) {
			if (!isset($first_date) || $event->start < $first_date)
				$first_date = $event->start;
			if (!isset($last_date) || $event->end > $last_date)
				$last_date = $event->end;
			$event->group = $location;
			$event->save();
		} else {
			$failed_saves .= (empty($failed_saves) ? '' : ', ').$cur_date;
		}
	}
	date_default_timezone_set($cur_timezone);

	if (empty($failed_removes)) {
		pines_notice('Work schedule entered for '.$employee->name);
	} else {
		pines_error('Could not schedule work for the following dates: '.$failed_saves);
	}
}

$total_time = $last_date - $first_date;
if ($total_time <= 86400) {
	$view_type = 'agendaDay';
} elseif ($total_time <= 604800) {
	$view_type = 'agendaWeek';
} else {
	$view_type = 'month';
}
pines_redirect(pines_url('com_calendar', 'editcalendar', array('view_type' => $view_type, 'start' => format_date($first_date), 'end' => format_date($last_date), 'employee' => $employee->guid)));