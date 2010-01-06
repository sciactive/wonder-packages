<?php
/**
 * com_sales_shipper class.
 *
 * @package Pines
 * @subpackage com_sales
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

/**
 * A shipper.
 *
 * @package Pines
 * @subpackage com_sales
 */
class com_sales_shipper extends entity {
	/**
	 * Load a shipper.
	 * @param int $id The ID of the shipper to load, null for a new shipper.
	 */
	public function __construct($id = null) {
		parent::__construct();
		$this->add_tag('com_sales', 'shipper');
		if (!is_null($id)) {
			global $config;
			$entity = $config->entity_manager->get_entity($id, $this->tags, get_class($this));
			if (is_null($entity))
				return;
			$this->guid = $entity->guid;
			$this->parent = $entity->parent;
			$this->tags = $entity->tags;
			$this->entity_cache = array();
			$this->put_data($entity->get_data());
		}
	}

	/**
	 * Delete the shipper.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted shipper $this->name.", 'notice');
		return true;
	}

	/**
	 * Save the shipper.
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		if (!isset($this->name))
			return false;
		return parent::save();
	}

	/**
	 * Print a form to edit the shipper.
	 * @return module The form's module.
	 */
	public function print_form() {
		$module = new module('com_sales', 'form_shipper', 'content');
		$module->entity = $this;

		return $module;
	}
}

?>