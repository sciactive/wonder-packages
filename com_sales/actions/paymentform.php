<?php
/**
 * Provide a form for a payment process type to collect information.
 *
 * @package Pines
 * @subpackage com_sales
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( isset($_REQUEST['id']) ) {
	if ( !gatekeeper('com_sales/editsale') )
		punt_user('You don\'t have necessary permission.', pines_url('com_sales', 'listsales', null, false));
	$sale = com_sales_sale::factory((int) $_REQUEST['id']);
} else {
	if ( !gatekeeper('com_sales/newsale') )
		punt_user('You don\'t have necessary permission.', pines_url('com_sales', 'listsales', null, false));
	$sale = com_sales_sale::factory();
}

if ($pines->run_sales->com_customer && $sale->status != 'invoiced' && $sale->status != 'paid') {
	$sale->customer = null;
	if (preg_match('/^\d+/', $_REQUEST['customer'])) {
		$sale->customer = com_customer_customer::factory(intval($_REQUEST['customer']));
		if (is_null($sale->customer->guid))
			$sale->customer = null;
	}
}

$pines->page->override = true;
$pines->run_sales->call_payment_process(array(
	'action' => 'request',
	'name' => $_REQUEST['name'],
	'sale' => $sale
));

?>