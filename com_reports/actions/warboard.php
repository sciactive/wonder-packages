<?php
/**
 * Show the company warboard.
 *
 * @package Components\reports
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_reports/warboard') )
	punt_user(null, pines_url('com_reports', 'warboard'));

$warboard = $_->nymph->getEntity(array('class' => com_reports_warboard), array('&', 'tag' => array('com_reports', 'warboard')));

if (!isset($warboard->guid)) {
	$warboard = com_reports_warboard::factory();
	$warboard->save();
}

$warboard->show();