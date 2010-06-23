<?php
/**
 * Provide a receipt of a return.
 *
 * @package Pines
 * @subpackage com_sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/editreturn') && !gatekeeper('com_sales/newreturn') && !gatekeeper('com_sales/newreturnwsale') )
	punt_user('You don\'t have necessary permission.', pines_url('com_sales', 'return/receipt', array('id' => $_REQUEST['id'])));

$entity = com_sales_return::factory((int) $_REQUEST['id']);
$entity->print_receipt();

?>