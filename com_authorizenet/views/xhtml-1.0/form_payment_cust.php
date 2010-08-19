<?php
/**
 * Provides a printable payment form (customer version).
 *
 * @package Pines
 * @subpackage com_authorizenet
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');
?>
<div class="pf-form">
	<div class="pf-element">
		<label><span class="pf-label">Name on Card</span>
			<input class="pf-field ui-widget-content ui-corner-all" type="text" id="p_muid_name_first" name="name_first" value="<?php echo htmlentities($this->name_first); ?>" /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Card Number</span>
			<input class="pf-field ui-widget-content ui-corner-all" type="text" id="p_muid_card_number" name="card_number" value="<?php echo htmlentities($this->card_number); ?>" /></label>
	</div>
	<div class="pf-element">
		<span class="pf-label">Expiration Date, CCV</span>
		<select class="pf-field ui-widget-content ui-corner-all" id="p_muid_card_exp_month" name="card_exp_month">
			<?php foreach (array(
					'01' => '01 January',
					'02' => '02 February',
					'03' => '03 March',
					'04' => '04 April',
					'05' => '05 May',
					'06' => '06 June',
					'07' => '07 July',
					'08' => '08 August',
					'09' => '09 September',
					'10' => '10 October',
					'11' => '11 November',
					'12' => '12 December'
				) as $key => $value) { ?>
			<option value="<?php echo $key; ?>"<?php echo $this->card_exp_month == $key ? ' selected="selected"' : ''; ?>><?php echo $value; ?></option>
			<?php } ?>
		</select>
		<select class="pf-field ui-widget-content ui-corner-all" id="p_muid_card_exp_year" name="card_exp_year">
			<?php for ($i = 0; $i <= 25; $i++) { ?>
			<option value="<?php echo date('y', strtotime("+$i years")); ?>"<?php echo $this->card_exp_year == date('y', strtotime("+$i years")) ? ' selected="selected"' : ''; ?>><?php echo date('y', strtotime("+$i years")); ?></option>
			<?php } ?>
		</select>
		<input class="pf-field ui-widget-content ui-corner-all" type="password" name="cid" size="3" value="<?php echo htmlentities($this->cid); ?>" />
	</div>
</div>