<?php
/**
 * Show list of per user configurable components.
 *
 * @package Pines
 * @subpackage com_configure
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_configure/edit') && !gatekeeper('com_configure/view') )
	punt_user('You don\'t have necessary permission.', pines_url('com_configure', 'listperuser', $_GET, false));

if (isset($_REQUEST['message']))
	display_notice($_REQUEST['message']);

$pines->configurator->list_components_peruser();

?>