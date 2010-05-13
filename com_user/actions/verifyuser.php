<?php
/**
 * Verify a newly registered user's e-mail address.
 *
 * @package Pines
 * @subpackage com_user
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

$user = user::factory((int) $_REQUEST['id']);

if (!isset($user->guid)) {
	pines_notice('The specified user id is not available.');
	$pines->user_manager->print_login();
	return;
}

if ($_REQUEST['secret'] != $user->secret) {
	pines_notice('The secret code given does not match this user.');
	$pines->user_manager->print_login();
	return;
}

$user->enabled = true;
unset($user->secret);

if ($user->save()) {
	pines_log('Validated user ['.$user->username.']');
	$pines->user_manager->login($user);
	$notice = new module('com_user', 'note_welcome', 'content');
} else {
	pines_error('Error saving user.');
}

?>