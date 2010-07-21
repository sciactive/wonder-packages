<?php
/**
 * Provide a receipt of a sale.
 *
 * @package Pines
 * @subpackage com_sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/editsale') && !gatekeeper('com_sales/newsale') )
	punt_user('You don\'t have necessary permission.', pines_url('com_sales', 'sale/printreceipt', array('id' => $_REQUEST['id'])));

$entity = com_sales_sale::factory((int) $_REQUEST['id']);

header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="receipt"');
header('Content-Transfer-Encoding: binary');
$pines->page->override = true;
$pines->page->override_doc($entity->receipt_text(48, 72));

?>