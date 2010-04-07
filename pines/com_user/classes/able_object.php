<?php
/**
 * able_object class.
 *
 * @package Pines
 * @subpackage com_user
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

/**
 * Entities which support abilities, such as users and groups.
 *
 * @package Pines
 * @subpackage com_user
 */
class able_object extends entity implements able_object_interface {
	public function grant($ability) {
		if ( !in_array($ability, $this->abilities) ) {
			return $this->abilities = array_merge(array($ability), $this->abilities);
		} else {
			return true;
		}
	}

	public function revoke($ability) {
		if ( in_array($ability, $this->abilities) ) {
			return $this->abilities = array_values(array_diff($this->abilities, array($ability)));
		} else {
			return true;
		}
	}
}

?>