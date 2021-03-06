<?php
/**
 * Clock an employee in or out, returning their status in JSON.
 *
 * @package Components\hrm
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_hrm/clock') && !gatekeeper('com_hrm/manageclock') )
	punt_user(null, pines_url('com_hrm', 'employee/timeclock/clock', $_REQUEST));

if ($_REQUEST['id'] == 'self') {
	$employee = com_hrm_employee::factory($_SESSION['user']->guid);
	if ($_->config->com_hrm->timeclock_verify_pin && !empty($_SESSION['user']->pin) && $_REQUEST['pin'] != $_SESSION['user']->pin) {
		$_->page->ajax(json_encode('pin'));
		return;
	}
} else {
	if ( !gatekeeper('com_hrm/manageclock') )
		punt_user(null, pines_url('com_hrm', 'employee/timeclock/clock', $_REQUEST));
	$employee = com_hrm_employee::factory((int) $_REQUEST['id']);
}

if (!isset($employee->guid)) {
	$_->page->ajax('false');
	return;
}

if ($employee->timeclock->clocked_in_time()) {
	$success = $employee->timeclock->clock_out($_REQUEST['comment']);
} else {
	$success = $employee->timeclock->clock_in();
}

if (!$success || !$employee->save()) {
	$_->page->ajax('false');
	return;
}


$_->page->ajax(json_encode($employee->timeclock->clocked_in_time()));