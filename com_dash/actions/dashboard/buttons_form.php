<?php
/**
 * Show a form list of buttons.
 *
 * @package Components\dash
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_dash/dash') || !gatekeeper('com_dash/editdash') )
	punt_user(null, pines_url('com_dash'));

if (!empty($_REQUEST['id']) && gatekeeper('com_dash/manage'))
	$dashboard = com_dash_dashboard::factory((int) $_REQUEST['id']);
else
	$dashboard =& $_SESSION['user']->dashboard;
if (!isset($dashboard->guid))
	throw new HttpClientException(null, 400);
if ($dashboard->locked && !gatekeeper('com_dash/manage'))
	throw new HttpClientException(null, 403);

$module = new module('com_dash', 'dashboard/buttons_form');
$module->current_buttons = $dashboard->tabs[$_REQUEST['key']]['buttons'];
$module->buttons_size = $dashboard->tabs[$_REQUEST['key']]['buttons_size'];
$module->buttons = $_->com_dash->button_types();
foreach ($module->buttons as $cur_component => $cur_button_set) {
	foreach ($cur_button_set as $cur_button_name => $cur_button) {
		// Check its conditions.
		foreach ((array) $cur_button['depends'] as $cur_type => $cur_value) {
			if (!$_->depend->check($cur_type, $cur_value)) {
				unset($module->buttons[$cur_component][$cur_button_name]);
				if (!$module->buttons[$cur_component])
					unset($module->buttons[$cur_component]);
			}
		}
	}
}

$_->page->ajax($module->render(), 'text/html');