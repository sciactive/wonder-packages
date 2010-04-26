<?php
/**
 * Delete a group.
 *
 * @package Pines
 * @subpackage com_user
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_user/deletegroup') )
	punt_user('You don\'t have necessary permission.', pines_url('com_user', 'listgroups'));

$list = explode(',', $_REQUEST['id']);
foreach ($list as $cur_group) {
	$cur_entity = group::factory((int) $cur_group);
	if ( !isset($cur_entity->guid) || !$cur_entity->delete() )
		$failed_deletes .= (empty($failed_deletes) ? '' : ', ').$cur_group;
}
if (empty($failed_deletes)) {
	pines_notice('Selected group(s) deleted successfully.');
} else {
	pines_error('Could not delete groups with given IDs: '.$failed_deletes);
}

redirect(pines_url('com_user', 'listgroups'));

?>