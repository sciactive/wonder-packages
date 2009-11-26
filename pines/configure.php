<?php
/**
 * Configuration for the Pines template.
 *
 * @package Pines
 * @subpackage tpl_pines
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

/**
 * Pines template class.
 *
 * @package Pines
 * @subpackage tpl_pines
 */
class tpl_pines extends template {
	/**
	 * The template format.
	 * @var string $format
	 */
	var $format = 'xhtml-1.0-strict-desktop';
	/**
	 * The editor CSS location.
	 *
	 * Filled at runtime.
	 * @var string $editor_css
	 */
	var $editor_css = '';
	/**
	 * Whether to show a header image, instead of text.
	 * @var bool $header_image
	 */
	var $header_image = true;
	/**
	 * jQuery UI theme to use.
	 *
	 * Available are:
	 * - "dark-hive"
	 * - "redmond"
	 * - "smoothness"
	 * - "start"
	 * - "ui-darkness"
	 * - "ui-lightness"
	 *
	 * @var string $theme
	 */
	var $theme = 'smoothness';
	/**
	 * Provide a theme switcher to choose a jQuery UI theme.
	 *
	 * @var bool $theme_switcher
	 */
	var $theme_switcher = true;
	/**
	 * Use Google CDN to host jQuery and jQuery UI.
	 *
	 * @var bool $google_cdn
	 */
	var $google_cdn = true;

	function __construct() {
		global $config;
		$this->editor_css = $config->rela_location.'templates/pines/css/editor.css';
	}
}

$config->template = new tpl_pines;

?>