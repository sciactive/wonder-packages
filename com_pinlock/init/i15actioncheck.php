<?php
/**
 * Check and protect actions.
 *
 * @package Components\pinlock
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if (!isset($_SESSION['user']) || empty($_SESSION['user']->pin))
	return;

$com_pinlock__request_component = empty($_->request_component) ? $_->config->default_component : $_->request_component;
$com_pinlock__request_action = empty($_->request_action) ? 'default' : $_->request_action;
if (!in_array("{$com_pinlock__request_component}/{$com_pinlock__request_action}", $_->config->com_pinlock->actions)) {
	unset($com_pinlock__request_component, $com_pinlock__request_action);
	return;
}

if ($_POST['com_pinlock_continue'] == 'true') {
	$com_pinlock__sessionid = $_POST['sessionid'];
	if ($_POST['pin'] == $_SESSION['user']->pin) {
		$_POST = unserialize($_SESSION[$com_pinlock__sessionid]['post']);
		$_GET = unserialize($_SESSION[$com_pinlock__sessionid]['get']);
		pines_session('write');
		unset($_SESSION[$com_pinlock__sessionid]);
		pines_session('close');
		$_REQUEST = array_merge((array) $_POST, (array) $_GET);
		unset($com_pinlock__request_component, $com_pinlock__request_action, $com_pinlock__sessionid);
		return;
	}
	if ($_->config->com_pinlock->allow_switch) {
		$com_pinlock__users = $_->user_manager->get_users();
		foreach ($com_pinlock__users as $com_pinlock__cur_user) {
			if (empty($com_pinlock__cur_user->pin))
				continue;
			if ($_POST['pin'] == $com_pinlock__cur_user->pin) {
				$_POST = unserialize($_SESSION[$com_pinlock__sessionid]['post']);
				$_GET = unserialize($_SESSION[$com_pinlock__sessionid]['get']);
				pines_log("PIN based user switch from {$_SESSION['user']->username} to {$com_pinlock__cur_user->username}.", 'notice');
				$_->user_manager->login($com_pinlock__cur_user);
				pines_notice("Logged in as {$com_pinlock__cur_user->username}.");
				unset(
						$com_pinlock__request_component,
						$com_pinlock__request_action,
						$com_pinlock__sessionid,
						$com_pinlock__cur_user,
						$com_pinlock__users
					);
				return;
			}
		}
		unset($com_pinlock__cur_user);
		unset($com_pinlock__users);
	}
	$_->com_pinlock->sessionid = $com_pinlock__sessionid;
	pines_session('write');
	$_SESSION['com_pinlock__attempts']++;
	pines_session('close');
	if ($_SESSION['com_pinlock__attempts'] >= $_->config->com_pinlock->max_tries) {
		pines_log('Maximum failed login attempts reached.', 'warning');
		$_->user_manager->logout();
		punt_user('Maximum failed login attempts reached.', pines_url());
	}
	pines_notice('Incorrect PIN.');
	unset($com_pinlock__sessionid);
} else {
	$_->com_pinlock->sessionid = 'com_pinlock'.rand(0, 1000000000);
	pines_session('write');
	$_SESSION['com_pinlock__attempts'] = 0;
	$_SESSION[$_->com_pinlock->sessionid] = array(
		'post' => serialize($_POST),
		'get' => serialize($_GET)
	);
	pines_session('close');
}
$_->com_pinlock->component = $com_pinlock__request_component;
$_->com_pinlock->action = $com_pinlock__request_action;
$_->request_component = 'com_pinlock';
$_->request_action = 'enterpin';
