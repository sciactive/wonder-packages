<?php
/**
 * Display a form to swap inventory on a sale.
 * 
 * @package Components
 * @subpackage sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<form class="pf-form" id="p_muid_form" action="">
	<div class="pf-element pf-heading">
		<h3>Item to Swap</h3>
	</div>
	<div class="pf-element">
		<?php foreach ($this->entity->products as $cur_product) {
			$serial = $cur_product['serial'] ? $cur_product['serial'] : 'No Serial';
			?>
		<input class="pf-field ui-widget-content" type="radio" name="swap_item" value="<?php echo htmlspecialchars($cur_product['sku'].':'.$cur_product['serial']); ?>" /> <?php echo htmlspecialchars($cur_product['entity']->name.' ('.$serial.')'); ?><br/>
		<?php } ?>
	</div>
	<div class="pf-element pf-heading">
		<h3>New Item</h3>
	</div>
	<div class="pf-element">
		<span class="pf-label">Item Serial</span>
		<input class="pf-field" type="text" name="new_serial" value="" />
	</div>
</form>