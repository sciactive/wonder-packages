<?php
/**
 * Delete a transfer.
 *
 * @package Pines
 * @subpackage com_sales
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/managestock') ) {
	$config->user_manager->punt_user("You don't have necessary permission.", pines_url('com_sales', 'listtransfers', null, false));
	return;
}

$list = explode(',', $_REQUEST['id']);
foreach ($list as $cur_transfer) {
	$this_transfer = $config->run_sales->get_transfer($cur_transfer);
	if ( !empty($this_transfer->received) || !$config->run_sales->delete_transfer($cur_transfer) )
		$failed_deletes .= (empty($failed_deletes) ? '' : ', ').$cur_transfer;
}
if (empty($failed_deletes)) {
	display_notice('Selected transfer(s) deleted successfully.');
} else {
	display_error('Could not delete transfers with given IDs: '.$failed_deletes);
	display_notice('Note that transfers cannot be deleted after items have been received on them.');
}

$config->run_sales->list_transfers();
?>