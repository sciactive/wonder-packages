<?php
/**
 * com_sales's display control.
 *
 * @package Pines
 * @subpackage com_sales
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( gatekeeper('com_sales/managecustomers') || gatekeeper('com_sales/newcustomer') ||
	 gatekeeper('com_sales/managemanufacturers') || gatekeeper('com_sales/newmanufacturer') ||
	 gatekeeper('com_sales/manageshippers') || gatekeeper('com_sales/newshipper') ||
	 gatekeeper('com_sales/manageproducts') || gatekeeper('com_sales/newproduct') ||
	 gatekeeper('com_sales/managevendors') || gatekeeper('com_sales/newvendor') ||
	 gatekeeper('com_sales/managepos') || gatekeeper('com_sales/newpo') ) {
	$com_sales_menu_id = $page->main_menu->add('POS');
	$com_sales_menu_id_sales = $page->main_menu->add('Sales', '#', $com_sales_menu_id);
	if ( gatekeeper('com_sales/managecustomers') )
		$page->main_menu->add('Customers', pines_url('com_sales', 'listcustomers'), $com_sales_menu_id_sales);
	if ( gatekeeper('com_sales/newcustomer') )
		$page->main_menu->add('New Customer', pines_url('com_sales', 'newcustomer'), $com_sales_menu_id_sales);
	$com_sales_menu_id_inv = $page->main_menu->add('Inventory', '#', $com_sales_menu_id);
	if ( gatekeeper('com_sales/receive') )
		$page->main_menu->add('Receive', pines_url('com_sales', 'receive'), $com_sales_menu_id_inv);
	if ( gatekeeper('com_sales/managestock') ) {
		$page->main_menu->add('Current Stock', pines_url('com_sales', 'liststock'), $com_sales_menu_id_inv);
		$page->main_menu->add('All Stock', pines_url('com_sales', 'liststock', array('all' => 'true')), $com_sales_menu_id_inv);
	}
	if ( gatekeeper('com_sales/managepos') )
		$page->main_menu->add('Purchase Orders', pines_url('com_sales', 'listpos'), $com_sales_menu_id_inv);
	if ( gatekeeper('com_sales/newpo') )
		$page->main_menu->add('New PO', pines_url('com_sales', 'newpo'), $com_sales_menu_id_inv);
	$com_sales_menu_id_setup = $page->main_menu->add('Setup', '#', $com_sales_menu_id);
	if ( gatekeeper('com_sales/manageproducts') )
		$page->main_menu->add('Products', pines_url('com_sales', 'listproducts'), $com_sales_menu_id_setup);
	if ( gatekeeper('com_sales/newproduct') )
		$page->main_menu->add('New Product', pines_url('com_sales', 'newproduct'), $com_sales_menu_id_setup);
	if ( gatekeeper('com_sales/managemanufacturers') )
		$page->main_menu->add('Manufacturers', pines_url('com_sales', 'listmanufacturers'), $com_sales_menu_id_setup);
	if ( gatekeeper('com_sales/newmanufacturer') )
		$page->main_menu->add('New Manufacturer', pines_url('com_sales', 'newmanufacturer'), $com_sales_menu_id_setup);
	if ( gatekeeper('com_sales/managevendors') )
		$page->main_menu->add('Vendors', pines_url('com_sales', 'listvendors'), $com_sales_menu_id_setup);
	if ( gatekeeper('com_sales/newvendor') )
		$page->main_menu->add('New Vendor', pines_url('com_sales', 'newvendor'), $com_sales_menu_id_setup);
	if ( gatekeeper('com_sales/manageshippers') )
		$page->main_menu->add('Shippers', pines_url('com_sales', 'listshippers'), $com_sales_menu_id_setup);
	if ( gatekeeper('com_sales/newshipper') )
		$page->main_menu->add('New Shipper', pines_url('com_sales', 'newshipper'), $com_sales_menu_id_setup);
	if ( gatekeeper('com_sales/managetaxfees') )
		$page->main_menu->add('Taxes/Fees', pines_url('com_sales', 'listtaxfees'), $com_sales_menu_id_setup);
	if ( gatekeeper('com_sales/newtaxfee') )
		$page->main_menu->add('New Tax/Fee', pines_url('com_sales', 'newtaxfee'), $com_sales_menu_id_setup);
}

?>