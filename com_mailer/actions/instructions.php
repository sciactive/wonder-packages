<?php
/**
 * Show email instructions.
 *
 * @package Components\mailer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_mailer/listtemplates') )
	punt_user(null, pines_url('com_mailer', 'instructions'));

$module = new module('com_mailer', 'instructions', 'content');