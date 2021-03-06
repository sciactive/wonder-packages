<?php
/**
 * Reset customer points.
 *
 * @package Components\customer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !$_->config->com_customer->resetpoints && !gatekeeper('com_customer/resetpoints') )
	punt_user(null, pines_url('com_customer', 'customer/list'));

$list = explode(',', $_REQUEST['id']);
foreach ($list as $cur_customer) {
	$cur_entity = com_customer_customer::factory((int) $cur_customer);
	if ( !isset($cur_entity->guid) ) {
		$failed_resets .= ($failed_resets ? ', ' : '').$cur_customer;
		continue;
	}
	$cur_entity->points = 0;
	$cur_entity->peak_points = 0;
	$cur_entity->total_points = 0;
	if ( !$cur_entity->save() )
		$failed_resets .= ($failed_resets ? ', ' : '').$cur_customer;
}
if (!$failed_resets) {
	pines_notice('Customer points reset successfully.');
} else {
	pines_error("Could not reset points of customers with given IDs: $failed_resets");
}

pines_redirect(pines_url('com_customer', 'customer/list'));