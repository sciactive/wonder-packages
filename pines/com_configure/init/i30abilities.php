<?php
/**
 * Add abilities.
 *
 * @package Pines
 * @subpackage com_configure
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( isset($pines->ability_manager) ) {
	$pines->ability_manager->add('com_configure', 'edit', 'Edit Configuration', 'Let the user change (and see) configuration settings.');
	$pines->ability_manager->add('com_configure', 'view', 'View Configuration', 'Let the user see current configuration settings.');
}

?>