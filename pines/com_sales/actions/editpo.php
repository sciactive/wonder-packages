<?php
/**
 * Provide a form to edit a PO.
 *
 * @package Pines
 * @subpackage com_sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if (isset($_REQUEST['id'])) {
	if ( !gatekeeper('com_sales/editpo') )
		punt_user('You don\'t have necessary permission.', pines_url('com_sales', 'editpo', array('id' => $_REQUEST['id']), false));
} else {
	if ( !gatekeeper('com_sales/newpo') )
		punt_user('You don\'t have necessary permission.', pines_url('com_sales', 'editpo', null, false));
}

$entity = com_sales_po::factory((int) $_REQUEST['id']);
$entity->print_form();

?>