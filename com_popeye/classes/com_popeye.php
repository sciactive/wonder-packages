<?php
/**
 * com_popeye class.
 *
 * @package Components\popeye
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * com_popeye main class.
 *
 * A JavaScript image slideshow widget.
 *
 * @package Components\popeye
 */
class com_popeye extends component {
	/**
	 * Whether the popeye JavaScript has been loaded.
	 * @access private
	 * @var bool $js_loaded
	 */
	private $js_loaded = false;

	/**
	 * Load the JavaScript.
	 *
	 * This will place the required scripts into the document's head section.
	 */
	function load() {
		if (!$this->js_loaded) {
			$module = new module('com_popeye', 'popeye', 'head');
			$module->render();
			$this->js_loaded = true;
		}
	}
}