<?php
/**
 * com_plaza's information.
 *
 * @package Pines
 * @subpackage com_plaza
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'Plaza Package Manager',
	'author' => 'SciActive',
	'version' => '1.0.0',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'short_description' => 'Plaza package manager',
	'description' => 'Find, install, and manage packages.',
	'depend' => array(
		'pines' => '<2',
		'component' => 'com_jquery&com_package'
	),
	'abilities' => array(
		array('listpackages', 'List Packages', 'User can see packages.')
	),
);

?>