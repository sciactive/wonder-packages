<?php
/**
 * com_messenger's information.
 *
 * @package Pines
 * @subpackage com_messenger
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'Instant Messenger',
	'author' => 'SciActive (Component), Abhinav Singh (JAXL)',
	'version' => '0.10.2dev',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'short_description' => 'An instant messenger.',
	'description' => 'An instant messenger that works within the website.',
	'depend' => array(
		'pines' => '<2',
		'component' => 'com_jquery&com_bootstrap&com_pform'
	),
);

?>