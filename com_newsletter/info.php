<?php
/**
 * com_newsletter's information.
 *
 * @package Pines
 * @subpackage com_newsletter
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'Newsletter Manager',
	'author' => 'SciActive',
	'version' => '1.0.0',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'short_description' => 'Manage newsletters to your users',
	'description' => 'Create and send newsletters to your users.',
	'abilities' => array(
		array('listmail', 'List Mail', 'Let users view the mailbox.'),
		array('send', 'Send', 'Let users send out mailings.')
	),
);

?>