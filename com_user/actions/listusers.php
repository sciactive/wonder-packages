<?php
/**
 * Manage the system users.
 *
 * @package Pines
 * @subpackage com_user
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_user/manageusers') )
	punt_user('You don\'t have necessary permission.', pines_url('com_user', 'listusers', null, false));

$config->user_manager->list_users();
?>