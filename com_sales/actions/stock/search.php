<?php
/**
 * Search stock, returning JSON.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/seestock'))
	punt_user(null, pines_url('com_sales', 'stock/search', $_REQUEST));

$product = com_sales_product::factory((int) $_REQUEST['product']);
$serial = $_REQUEST['serial'];
$location = empty($_REQUEST['location']) ? null : group::factory((int) $_REQUEST['location']);
$quantity = (int) $_REQUEST['quantity'];
$not_guids = (array) json_decode($_REQUEST['not_guids']);

if (!isset($product->guid) || (empty($serial) && (empty($location) || !isset($location->guid))) || $quantity < 1) {
	$_->page->ajax('false');
	return;
}

$selector = array('&',
		'tag' => array('com_sales', 'stock'),
		'data' => array(
			array('available', true)
		),
		'ref' => array(
			array('product', $product)
		)
	);

if (!empty($serial))
	$selector['data'][] = array('serial', $serial);

if (!empty($location) && isset($location->guid))
	$selector['ref'][] = array('location', $location);

if ($not_guids) {
	$not_guids = array_map('intval', $not_guids);
	$stock = (array) $_->nymph->getEntities(
			array('class' => com_sales_stock, 'limit' => $quantity),
			$selector,
			array('!&',
				'guid' => $not_guids
			)
		);
} else {
	$stock = (array) $_->nymph->getEntities(
			array('class' => com_sales_stock, 'limit' => $quantity),
			$selector
		);
}

foreach ($stock as &$cur_stock) {
	$cur_stock = array(
		'guid' => "$cur_stock->guid",
		'product' => "{$cur_stock->product->guid}",
		'location' => "{$cur_stock->location->guid}",
		'location_name' => $cur_stock->location->name,
		'serial' => $cur_stock->serial
	);
}

$_->page->ajax(json_encode($stock));