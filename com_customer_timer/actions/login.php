<?php
/**
 * List customers.
 *
 * @package Pines
 * @subpackage com_customer_timer
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_customer_timer/login') ) {
	$config->user_manager->punt_user("You don't have necessary permission.", pines_url('com_customer_timer', 'login', null, false));
	return;
}

$module = new module('com_customer_timer', 'login', 'content');
?>