<?php
/**
 * com_configure's information.
 *
 * @package Pines
 * @subpackage com_configure
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'System Configurator',
	'author' => 'SciActive',
	'version' => '1.0.0',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'services' => array('configurator'),
	'short_description' => 'Manages system configuration',
	'description' => 'Allows you to edit your system\'s configuration and the configuration of any installed components.',
	'abilities' => array(
		array('edit', 'Edit Configuration', 'Let the user change (and see) configuration settings.'),
		array('peruser', 'Edit Per User Configuration', 'Let the user change (and see) per user/group configuration settings.'),
		array('view', 'View Configuration', 'Let the user see current configuration settings.')
	),
);

?>