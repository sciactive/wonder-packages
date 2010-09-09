<?php
/**
 * Save changes to a transfer.
 *
 * @package Pines
 * @subpackage com_sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/managestock') )
	punt_user(null, pines_url('com_sales', 'transfer/list'));

if ( isset($_REQUEST['id']) ) {
	$transfer = com_sales_transfer::factory((int) $_REQUEST['id']);
	if (!isset($transfer->guid) || $transfer->final) {
		pines_error('Requested transfer id is not accessible.');
		return;
	}
} else {
	$transfer = com_sales_transfer::factory();
}

// General
$transfer->reference_number = $_REQUEST['reference_number'];
// Destination can't be changed after items have been received.
if (empty($transfer->received)) {
	$transfer->destination = group::factory((int) $_REQUEST['destination']);
	if (!isset($transfer->destination->guid))
		$transfer->destination = null;
}
$transfer->shipper = com_sales_shipper::factory((int) $_REQUEST['shipper']);
if (!isset($transfer->shipper->guid))
	$transfer->shipper = null;
$transfer->eta = strtotime($_REQUEST['eta']);

// Stock
// Stock can't be changed after items have been received.
if (empty($transfer->received)) {
	$transfer->stock = (array) json_decode($_REQUEST['stock']);
	foreach ($transfer->stock as $key => &$cur_stock) {
		$cur_stock = com_sales_stock::factory((int) $cur_stock->key);
		if (!isset($cur_stock->guid))
			unset($transfer->stock[$key]);
	}
	unset($cur_stock);
}

if (!isset($transfer->destination)) {
	$transfer->print_form();
	pines_error('Specified destination is not valid.');
	return;
}
if (!isset($transfer->shipper)) {
	$transfer->print_form();
	pines_error('Specified shipper is not valid.');
	return;
}

$transfer->ac->other = 2;

if ($_REQUEST['save'] == 'commit')
	$transfer->final = true;

if ($transfer->save()) {
	if ($transfer->final) {
		pines_notice('Committed transfer ['.$transfer->guid.']');
	} else {
		pines_notice('Saved transfer ['.$transfer->guid.']');
	}
} else {
	pines_error('Error saving transfer. Do you have permission?');
}

redirect(pines_url('com_sales', 'transfer/list'));

?>