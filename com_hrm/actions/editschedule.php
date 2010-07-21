<?php
/**
 * Edit a work schedule for an employee in the company.
 *
 * @package Pines
 * @subpackage com_hrm
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper() )
	punt_user('You don\'t have necessary permission.', pines_url('com_hrm', 'editschedule'));

$employee = com_hrm_employee::factory((int)$_REQUEST['employee']);
if (!isset($employee->guid))
	return;

$employee->schedule_form();

?>