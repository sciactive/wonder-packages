<?php
/**
 * List products.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/listproducts') )
	punt_user(null, pines_url('com_sales', 'product/list'));

$pines->com_sales->list_products($_REQUEST['enabled'] != 'false', $_REQUEST['show']);