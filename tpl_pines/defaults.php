<?php
/**
 * tpl_pines' configuration.
 *
 * @package Pines
 * @subpackage tpl_pines
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

return array(
	array(
		'name' => 'fancy_style',
		'cname' => 'Fancy Styling',
		'description' => 'Use fancier styling.',
		'value' => array('font', 'shadows'),
		'options' => array(
			'Fancy fonts.' => 'font',
			'Drop shadows.' => 'shadows'
		),
		'peruser' => true,
	),
	array(
		'name' => 'use_header_image',
		'cname' => 'Use Header Image',
		'description' => 'Show a header image (instead of just text) at the top of the page.',
		'value' => true,
		'peruser' => true,
	),
	array(
		'name' => 'header_image',
		'cname' => 'Header Image',
		'description' => 'The header image to use.',
		'value' => isset($_SESSION['user']->group) ? $_SESSION['user']->group->get_logo() : $pines->config->rela_location.$pines->config->upload_location.'logos/default_logo.png',
		'peruser' => true,
	),
	array(
		'name' => 'buttonized_menu',
		'cname' => 'Buttonized Menu',
		'description' => 'Make the main menu look more like buttons.',
		'value' => false,
		'peruser' => true,
	),
	array(
		'name' => 'center_menu',
		'cname' => 'Centered Menu',
		'description' => 'Center the main menu.',
		'value' => false,
		'peruser' => true,
	),
	array(
		'name' => 'menu_delay',
		'cname' => 'Menu Delay',
		'description' => 'Make menus delay before closing when the mouse leaves them. This makes it easier to navigate menus, but the menus may become slower.',
		'value' => false,
		'peruser' => true,
	),
	array(
		'name' => 'ajax',
		'cname' => 'Use Ajax',
		'description' => 'Use the experimental AJAX code to load pages without refreshing.',
		'value' => false,
		'peruser' => true,
	),
);

?>