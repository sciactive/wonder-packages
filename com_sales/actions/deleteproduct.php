<?php
/**
 * Delete a product.
 *
 * @package Pines
 * @subpackage com_sales
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/deleteproduct') ) {
	$config->user_manager->punt_user("You don't have necessary permission.", pines_url('com_sales', 'listproducts', null, false));
	return;
}

$list = explode(',', $_REQUEST['id']);
foreach ($list as $cur_product) {
	if ( !$config->run_sales->delete_product($cur_product) )
		$failed_deletes .= (empty($failed_deletes) ? '' : ', ').$cur_product;
}
if (empty($failed_deletes)) {
	display_notice('Selected product(s) deleted successfully.');
} else {
	display_error('Could not delete products with given IDs: '.$failed_deletes);
}

$config->run_sales->list_products();
?>