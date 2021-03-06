<?php
/**
 * List payment types.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/listpaymenttypes') )
	punt_user(null, pines_url('com_sales', 'paymenttype/list'));

$_->com_sales->list_payment_types();