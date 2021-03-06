<?php
/**
 * Save default customer groups.
 *
 * @package Components\customer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_customer/defaultgroups') )
	punt_user(null, pines_url('com_customer', 'defaultgroups'));

// Save the default primary group.
$cur_group = $_->nymph->getEntity(
		array('class' => group),
		array('&',
			'tag' => array('com_user', 'group'),
			'data' => array('default_customer_primary', true)
		)
	);
if (isset($cur_group->guid)) {
	$cur_group->default_customer_primary = false;
	$cur_group->save();
}
$group = group::factory((int) $_REQUEST['group']);
if (isset($group->guid)) {
	$group->default_customer_primary = true;
	$group->save();
}

// Save the default secondary groups.
$cur_groups = $_->nymph->getEntities(
		array('class' => group),
		array('&',
			'tag' => array('com_user', 'group'),
			'data' => array('default_customer_secondary', true)
		)
	);
foreach ($cur_groups as $cur_group) {
	$cur_group->default_customer_secondary = false;
	$cur_group->save();
}
foreach ((array) $_REQUEST['groups'] as $cur_group_guid) {
	$cur_group = group::factory((int) $cur_group_guid);
	if (isset($cur_group->guid)) {
		$cur_group->default_customer_secondary = true;
		$cur_group->save();
	}
}

pines_notice('Default groups saved.');
pines_redirect(pines_url('com_customer', 'defaultgroups'));