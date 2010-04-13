<?php
/**
 * List customers.
 *
 * @package Pines
 * @subpackage com_customer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_customer/listcompanies') )
	punt_user('You don\'t have necessary permission.', pines_url('com_customer', 'listcompanies'));

$pines->com_customer->list_companies();
?>