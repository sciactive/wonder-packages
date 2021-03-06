<?php
/**
 * Delete a manufacturer.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if (!$_->config->com_sales->enable_manufacturers)
	throw HttpClientException(null, 404);

if ( !gatekeeper('com_sales/deletemanufacturer') )
	punt_user(null, pines_url('com_sales', 'manufacturer/list'));

$list = explode(',', $_REQUEST['id']);
foreach ($list as $cur_manufacturer) {
	$cur_entity = com_sales_manufacturer::factory((int) $cur_manufacturer);
	if ( !isset($cur_entity->guid) || !$cur_entity->delete() )
		$failed_deletes .= (empty($failed_deletes) ? '' : ', ').$cur_manufacturer;
}
if (empty($failed_deletes)) {
	pines_notice('Selected manufacturer(s) deleted successfully.');
} else {
	pines_error('Could not delete manufacturers with given IDs: '.$failed_deletes);
}

pines_redirect(pines_url('com_sales', 'manufacturer/list'));