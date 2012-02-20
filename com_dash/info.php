<?php
/**
 * com_dash's information.
 *
 * @package Pines
 * @subpackage com_dash
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'Dashboard',
	'author' => 'SciActive',
	'version' => '1.0.0beta4',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'short_description' => 'Configurable dashboard',
	'description' => 'Provides a dashboard with quick links and widgets.',
	'depend' => array(
		'pines' => '>=1.0.4&<2',
		'service' => 'entity_manager&user_manager',
		'component' => 'com_jquery&com_bootstrap&com_pform'
	),
	'abilities' => array(
		array('dash', 'Dashboard', 'User can have a dashboard.')
	),
);

?>