<?php
/**
 * List daily cash drawer counts.
 *
 * @package Pines
 * @subpackage com_sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_sales/listcashcounts') )
	punt_user('You don\'t have necessary permission.', pines_url('com_sales', 'cashcount/list'));

if (!empty($_REQUEST['start_date']))
	$start_date = strtotime($_REQUEST['start_date'].' 00:00');
if (!empty($_REQUEST['end_date']))
	$end_date = strtotime($_REQUEST['end_date'].' 23:59');
if (!empty($_REQUEST['location'])) {
	$location = group::factory((int) $_REQUEST['location']);
	if (!isset($location->guid))
		$location = null;
}
$old = ($_REQUEST['old'] == 'true');
$pines->com_sales->list_cashcounts($start_date, $end_date, $location, $old);
?>