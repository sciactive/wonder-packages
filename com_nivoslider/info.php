<?php
/**
 * com_nivoslider's information.
 *
 * @package Components\nivoslider
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'Nivo Slider',
	'author' => 'SciActive (Component), Gilbert Pellegrom (JavaScript)',
	'version' => '1.0.1',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'short_description' => 'Nivo Slider jQuery plugin',
	'description' => 'A JavaScript image slider jQuery component.',
	'depend' => array(
		'core' => '<3',
		'service' => 'uploader',
		'component' => 'com_jquery'
	),
);