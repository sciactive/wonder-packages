<?php
/**
 * com_messenger class.
 *
 * @package Components\messenger
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

/**
 * com_messenger main class.
 *
 * @package Components\messenger
 */
class com_messenger extends component {
	/**
	 * Whether the pchat JavaScript has been loaded.
	 * @access private
	 * @var bool $js_loaded
	 */
	private $js_loaded = false;

	/**
	 * Load the scripts.
	 *
	 * This will place the required scripts into the document's head section.
	 */
	function load() {
		if (!$this->js_loaded) {
			$module = new module('com_messenger', 'pchat', 'head');
			$module->render();
			$this->js_loaded = true;
		}
	}

	/**
	 * Make a temporary secret to use as a password for current user XMPP login.
	 * @return string The secret password.
	 */
	function get_temp_secret() {
		if (!isset($_SESSION['user']->guid))
			return '';
		pines_session('write');
		$_SESSION['user']->xmpp_secret = uniqid('xmpp_secret_');
		$_SESSION['user']->xmpp_secret_expire = strtotime('+5 minutes');
		$_SESSION['user']->save();
		pines_session('close');
		return $_SESSION['user']->xmpp_secret;
	}
}

?>