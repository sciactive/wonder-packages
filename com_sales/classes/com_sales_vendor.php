<?php
/**
 * com_sales_vendor class.
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
 * A vendor.
 *
 * @package Pines
 * @subpackage com_sales
 */
class com_sales_vendor extends entity {
	/**
	 * Load a vendor.
	 * @param int $id The ID of the vendor to load, null for a new vendor.
	 */
	public function __construct($id = null) {
		parent::__construct();
		$this->add_tag('com_sales', 'vendor');
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
	 * Create a new instance.
	 */
	public static function factory() {
		global $config;
		$class = get_class();
		$args = func_get_args();
		$entity = new $class($args[0]);
		$config->hook->hook_object($entity, $class.'->', false);
		return $entity;
	}

	/**
	 * Delete the vendor.
	 * @return bool True on success, false on failure.
	 */
	public function delete() {
		if (!parent::delete())
			return false;
		pines_log("Deleted vendor $this->name.", 'notice');
		return true;
	}

	/**
	 * Save the vendor.
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		if (!isset($this->name))
			return false;
		return parent::save();
	}

	/**
	 * Print a form to edit the vendor.
	 * @return module The form's module.
	 */
	public function print_form() {
		$module = new module('com_sales', 'form_vendor', 'content');
		$module->entity = $this;

		return $module;
	}
}

?>