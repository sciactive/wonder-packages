<?php
/**
 * com_pgrid's information.
 *
 * @package Pines
 * @subpackage com_pgrid
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'Pines Grid',
	'author' => 'SciActive',
	'version' => '1.0.0',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'short_description' => 'Pines Grid jQuery plugin',
	'description' => 'A JavaScript data grid jQuery component. Supports many features, and fully themeable using jQuery UI.',
	'depend' => array(
		'pines' => '<2',
		'service' => 'icons',
		'component' => 'com_jquery'
	),
	'abilities' => array(
		array('clearallstates', 'Clear All States', 'Clear all users\' pgrid states.')
	),
);

?>