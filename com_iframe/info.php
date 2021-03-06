<?php
/**
 * com_iframe's information.
 *
 * @package Components\iframe
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

return array(
	'name' => 'IFrame',
	'author' => 'SciActive',
	'version' => '1.0.1',
	'license' => 'http://www.gnu.org/licenses/agpl-3.0.html',
	'website' => 'http://www.sciactive.com',
	'short_description' => 'IFrame inline module',
	'description' => 'An iframe inline module. Many editors will filter iframes, so this component can be used to create them, even though they\'re not XHTML 1.0 Strict.',
	'depend' => array(
		'core' => '<3'
	),
);