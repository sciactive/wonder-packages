<?php
/**
 * com_esp's buttons.
 *
 * @package Components\esp
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'esps' => array(
		'description' => 'ESP list.',
		'text' => 'ESPs',
		'class' => 'picon-security-high',
		'href' => pines_url('com_esp', 'list'),
		'default' => false,
		'depends' => array(
			'ability' => 'com_esp/list',
		),
	),
);