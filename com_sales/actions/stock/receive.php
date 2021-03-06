<?php
/**
 * Receive inventory.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/receive') )
	punt_user(null, pines_url('com_sales', 'stock/receive'));

if (!isset($_REQUEST['products'])) {
	$module = $_->com_sales->print_receive_form();
	if ((int) $_REQUEST['location'])
		$module->location = (int) $_REQUEST['location'];
	if ($_REQUEST['shipments'])
		$module->shipments = explode(',', $_REQUEST['shipments']);
	return;
}

$shipments_json = (array) json_decode($_REQUEST['shipments']);
if (!$shipments_json)
	$shipments_transfers = $shipments_pos = array();
else {
	// These are the shipments (POs and transfers) selected to be received on
	// the form. The GUIDs can be either a PO or transfer.
	$shipments_json = array_map('intval', $shipments_json);
	$shipments_transfers = $_->nymph->getEntities(
			array('class' => com_sales_transfer),
			array('&',
				'tag' => array('com_sales', 'transfer')
			),
			array('|',
				'guid' => $shipments_json
			)
		);
	$shipments_pos = $_->nymph->getEntities(
			array('class' => com_sales_po),
			array('&',
				'tag' => array('com_sales', 'po')
			),
			array('|',
				'guid' => $shipments_json
			)
		);
}

$products_json = (array) json_decode($_REQUEST['products']);
if (!$products_json) {
	pines_notice('Invalid product list!');
	$_->com_sales->print_receive_form();
	return;
}
$products = array();
foreach ($products_json as $key => $cur_product) {
	$products[$key] = array(
		'product_code' => trim($cur_product->values[0]),
		'serial' => trim($cur_product->values[1]),
		'quantity' => (int) $cur_product->values[2]
	);
}

if (gatekeeper('com_sales/receivelocation') && isset($_REQUEST['location'])) {
	$location = group::factory((int) $_REQUEST['location']);
	if (!isset($location->guid)) {
		pines_notice('Specified location not found.');
		return;
	}
	if (!$location->is($_SESSION['user']->group) && !$location->inArray($_SESSION['user']->group->get_descendants())) {
		pines_notice('Specified location is not under yours.');
		return;
	}
} else {
	$location = $_SESSION['user']->group;
}

$module = new module('com_sales', 'stock/showreceived', 'content');
$module->location = $location;

foreach ($products as $cur_product) {
	$cur_product_entity = $_->com_sales->get_product_by_code($cur_product['product_code']);
	if (!isset($cur_product_entity)) {
		pines_notice("Product with code {$cur_product['product_code']} not found! Skipping...");
		continue;
	}
	if ($cur_product_entity->serialized && empty($cur_product['serial'])) {
		pines_notice("Product [{$cur_product_entity->name}] with code {$cur_product['product_code']} requires a serial! Skipping...");
		continue;
	}
	for ($i = 0; $i < $cur_product['quantity']; $i++) {
		$origin = $stock = null;
		$serial = empty($cur_product['serial']) ? null : trim($cur_product['serial']);
		// Search for the product on a transfer.
		$origin = $_->com_sales->get_origin_transfer($cur_product_entity, $serial, $location, $shipments_transfers);
		if (isset($origin) && isset($origin[0])) {
			$stock = $origin[1];
			$origin = $origin[0];
			$status = 'received_transfer';
		} else {
			// Search for the product on a PO.
			$origin = $_->com_sales->get_origin_po($cur_product_entity, $location, $shipments_pos);
			$stock = com_sales_stock::factory();
			$stock->product = $cur_product_entity;
			if ($cur_product_entity->serialized) {
				$stock->serial = $serial;
				if ($_->config->com_sales->unique_serials) {
					$test = $_->nymph->getEntity(array('class' => com_sales_stock, 'skip_ac' => true), array('&', 'tag' => array('com_sales', 'stock'), 'strict' => array('serial', $stock->serial)));
					if (isset($test)) {
						pines_notice("There is already a stock entry with the serial {$stock->serial}. Serials must be unique.");
						continue;
					}
				}
			}
			$stock->vendor = $origin->vendor;
			// TODO: Save the stock's cost from the PO.
			$status = 'received_po';
		}
		if (!isset($origin)) {
			pines_notice("Product [{$cur_product_entity->name}] with code {$cur_product['product_code']} was not found on any PO or transfer! Skipping...");
			continue;
		}

		$stock->receive($status, $origin, $location);
		if (!$stock->save()) {
			pines_error("Stock entry for product [{$cur_product_entity->name}] couldn't be saved.");
			continue;
		}

		if ($status == 'received_po') {
			// Check for warehouse items being assigned the incoming stock.
			$wh_sales = $_->nymph->getEntities(
					array('class' => com_sales_sale, 'skip_ac' => true),
					array('&',
						'tag' => array('com_sales', 'sale'),
						'ref' => array(
							array('products', $origin),
							array('products', $cur_product_entity)
						)
					),
					array('|',
						'strict' => array(
							array('status', 'invoiced'),
							array('status', 'paid')
						)
					)
				);
			$changed = false;
			foreach ($wh_sales as $cur_sale) {
				foreach ($cur_sale->products as &$cur_sale_product) {
					if ($cur_sale_product['delivery'] != 'warehouse' || !$cur_product_entity->is($cur_sale_product['entity']))
						continue;
					if (!isset($cur_sale_product['po']) || !$origin->is($cur_sale_product['po']))
						continue;
					// Have they already all been assigned?
					if ((count($cur_sale_product['stock_entities']) - count($cur_sale_product['returned_stock_entities'])) >= $cur_sale_product['quantity'])
						continue;
					// Assign this stock to the warehouse order.
					$cur_sale_product['stock_entities'][] = $stock;
					if ($cur_product_entity->serialized)
						$cur_sale_product['serial'] = $stock->serial;
					// Break out so we don't assign two.
					$changed = true;
					break;
				}
				unset($cur_sale_product);
				if ($changed) {
					// The stock is now attached to a sale, so it's not available anymore.
					pines_log("Setting stock entry $stock->guid to unavailable. It is being assigned to a warehouse order on sale $cur_sale->id.", 'info');
					if (!($stock->remove('sold_pending_shipping', $cur_sale, $stock->location) && $stock->save())) {
						pines_notice("Stock entry $stock->guid could not be removed, and it belongs on sale $cur_sale->id.");
					} else {
						if (!$cur_sale->save()) {
							pines_log("Couldn't assign incoming stock on PO {$origin->po_number} to the warehouse order {$cur_sale->id}.", 'error');
							pines_error("Couldn't assign incoming stock on PO {$origin->po_number} to the warehouse order {$cur_sale->id}.");
						}
					}
					// We found the right sale and updated it, so break out now.
					break;
				}
			}
		}

		$module->success[] = array($stock, $origin);
	}
}