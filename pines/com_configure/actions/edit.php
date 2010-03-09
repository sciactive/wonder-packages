<?php
/**
 * Edit configuration settings.
 *
 * @package Pines
 * @subpackage com_configure
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_configure/edit') )
	punt_user('You don\'t have necessary permission.', pines_url('com_configure', 'edit', $_GET, false));

if (!array_key_exists($_REQUEST['component'], $pines->configurator->config_files)) {
	display_error('Given component either does not exist, or has no configuration file!');
	return;
}

$component = com_configure_component::factory($_REQUEST['component']);
$component->print_form();

?>