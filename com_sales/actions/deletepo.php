<?php
/**
 * Delete a PO.
 *
 * @package Pines
 * @subpackage com_sales
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/deletepo') ) {
	$config->user_manager->punt_user("You don't have necessary permission.", pines_url('com_sales', 'listpos', null, false));
	return;
}

$list = explode(',', $_REQUEST['id']);
foreach ($list as $cur_po) {
	$this_po = $config->run_sales->get_po($cur_po);
	if ( !empty($this_po->received) || !$config->run_sales->delete_po($cur_po) )
		$failed_deletes .= (empty($failed_deletes) ? '' : ', ').$cur_po;
}
if (empty($failed_deletes)) {
	display_notice('Selected PO(s) deleted successfully.');
} else {
	display_error('Could not delete POs with given IDs: '.$failed_deletes);
	display_notice('Note that POs cannot be deleted after items have been received on them.');
}

$config->run_sales->list_pos();
?>