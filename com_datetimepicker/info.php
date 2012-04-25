<?php
/**
 * com_datetimepicker's information.
 *
 * @package Components
 * @subpackage datetimepicker
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'Time Picker Addon',
	'author' => 'SciActive (Component), Trent Richardson (JavaScript)',
	'version' => '1.0.1dev',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'short_description' => 'Time Picker jQuery plugin addon',
	'description' => 'A JavaScript date/time picker jQuery component addon.',
	'depend' => array(
		'pines' => '<2',
		'component' => 'com_jquery'
	),
);

?>