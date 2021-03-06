<?php
/**
 * List warehouse items that need to be ordered.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/viewwarehouse') && !gatekeeper('com_sales/warehouse') )
	punt_user(null, pines_url('com_sales', 'warehouse/pending_info'));

list ($sale_id, $key) = explode('_', $_REQUEST['id']);
$sale = com_sales_sale::factory((int) $sale_id);
if (!isset($sale->guid)) {
	$_->page->ajax('Couldn\'t find specified sale.', 'text/plain');
	return;
}

$product = $sale->products[(int) $key]['entity'];
if (!isset($product->guid)) {
	$_->page->ajax('Couldn\'t find specified product.', 'text/plain');
	return;
}

// Warehouse group.
$warehouse = group::factory($_->config->com_sales->warehouse_group);
if (!isset($warehouse->guid)) {
	pines_error('Warehouse group is not configured correctly.');
	return;
}

$module = new module('com_sales', 'warehouse/pending_info');

// Find warehouse stock.
$module->warehouse_entity = $warehouse;
$module->warehouse = $_->nymph->getEntities(
		array('class' => com_sales_stock, 'skip_ac' => true),
		array('&',
			'tag' => array('com_sales', 'stock'),
			'data' => array('available', true),
			'ref' => array(
				array('location', $warehouse),
				array('product', $product)
			)
		)
	);

// Find PO products.
$module->pos = (array) $_->nymph->getEntities(
		array('class' => com_sales_po, 'skip_ac' => true),
		array('&',
			'tag' => array('com_sales', 'po'),
			'data' => array(array('final', true), array('finished', false)),
			'ref' => array(
				array('destination', $warehouse),
				array('pending_products', $product)
			)
		)
	);

// Find transfer products.
$module->transfers = (array) $_->nymph->getEntities(
		array('class' => com_sales_transfer, 'skip_ac' => true),
		array('&',
			'tag' => array('com_sales', 'transfer'),
			'data' => array(array('final', true), array('shipped', true), array('finished', false)),
			'ref' => array(
				array('destination', $warehouse),
				array('pending_products', $product)
			)
		)
	);

// Find item in current inventory.
$stock = (array) $_->nymph->getEntities(
		array('class' => com_sales_stock, 'skip_ac' => true),
		array('&',
			'tag' => array('com_sales', 'stock'),
			'data' => array('available', true),
			'isset' => 'location',
			'ref' => array('product', $product)
		),
		array('!&',
			'ref' => array('location', $warehouse)
		)
	);
$module->locations = array();
$module->locations_serials = array();
foreach ($stock as $cur_stock) {
	if (!isset($cur_stock->location->guid))
		continue;
	if (!$cur_stock->location->inArray($module->locations))
		$module->locations[] = $cur_stock->location;
	$module->locations_serials[$cur_stock->location->guid][] = $cur_stock;
}

$module->product = $product;

$_->page->ajax($module->render(), 'text/html');