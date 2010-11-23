<?php
/**
 * Edit a quick work schedule for a company location.
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
	punt_user(null, pines_url('com_hrm', 'editlineup'));

$location = group::factory((int)$_REQUEST['location']);
if (!isset($location->guid))
	return;

$pines->com_hrm->lineup_form($location);

?>