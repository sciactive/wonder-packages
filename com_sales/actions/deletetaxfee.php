<?php
/**
 * Delete a tax/fee.
 *
 * @package Pines
 * @subpackage com_sales
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/deletetaxfee') ) {
	$config->user_manager->punt_user("You don't have necessary permission.", pines_url('com_sales', 'listtaxfees', null, false));
	return;
}

$list = explode(',', $_REQUEST['id']);
foreach ($list as $cur_tax_fee) {
    if ( !$config->run_sales->delete_tax_fee($cur_tax_fee) )
        $failed_deletes .= (empty($failed_deletes) ? '' : ', ').$cur_tax_fee;
}
if (empty($failed_deletes)) {
    display_notice('Selected tax/fee(s) deleted successfully.');
} else {
    display_error('Could not delete tax/fees with given IDs: '.$failed_deletes);
}

$config->run_sales->list_tax_fees();
?>