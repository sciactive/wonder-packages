<?php
/**
 * Provides a form for payment info.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Payment Options';
?>
<script type="text/javascript">
	$_(function(){
		var buttons = $(":button, :submit, :reset", "#p_muid_form .pf-buttons").click(function(){
			buttons.attr("disabled", "disabled").addClass("disabled");
		});
	});
</script>
<div class="pf-form">
	<?php if (!$this->review_form) { // The totals are already shown on the review part if the pages are combined. ?>
	<div class="pf-element">
		<span class="pf-label">Subtotal</span>
		<span class="pf-field">$<?php echo number_format($_SESSION['com_shop_sale']->subtotal, 2); ?></span>
	</div>
	<?php if ($_SESSION['com_shop_sale']->item_fees) { ?>
	<div class="pf-element">
		<span class="pf-label">Item Fees</span>
		<span class="pf-field">$<?php echo number_format($_SESSION['com_shop_sale']->item_fees, 2); ?></span>
	</div>
	<?php } ?>
	<div class="pf-element">
		<span class="pf-label">Tax</span>
		<span class="pf-field">$<?php echo number_format($_SESSION['com_shop_sale']->taxes, 2); ?></span>
	</div>
	<div class="pf-element">
		<span class="pf-label">Sale Total</span>
		<span class="pf-field">$<?php echo number_format($_SESSION['com_shop_sale']->total, 2); ?></span>
	</div>
	<?php } if (count($this->payment_types) == 1) { $cur_payment_type = $this->payment_types[0]; ?>
	<div class="pf-element pf-heading">
		<h3><?php e($cur_payment_type->name); ?></h3>
	</div>
	<?php if (!empty($this->payment)) { ?>
	<script type="text/javascript">
		$_(function(){
			var form = $("#p_muid_form");
			var data = <?php echo json_encode($this->payment->data); ?>;
			if (data) {
				$.each(data, function(i, val){
					form.find(":input:not(:radio, :checkbox)[name="+i+"]").val(val);
					form.find(":input:radio[name="+i+"][value="+val+"]").attr("checked", "checked");
					if (val == "")
						form.find(":input:checkbox[name="+i+"]").removeAttr("checked");
					else
						form.find(":input:checkbox[name="+i+"][value="+val+"]").attr("checked", "checked");
				});
			}
		});
	</script>
	<?php } ?>
	<form id="p_muid_form" method="POST" action="<?php e(pines_url('com_shop', 'checkout/paymentsave')); ?>">
		<br class="pf-clearing" />
		<?php
		$_->com_sales->call_payment_process(array(
			'action' => 'request_cust',
			'name' => $cur_payment_type->processing_type,
			'ticket' => $_SESSION['com_shop_sale']
		), $module);

		if (isset($module))
			echo $module->render();
		?>
		<?php if ($this->review_form) { ?>
		<div class="pf-element pf-full-width">
			<span class="pf-label">Order Comments</span>
			<textarea class="pf-field" rows="1" cols="35" name="comments"><?php e($this->entity->comments); ?></textarea>
		</div>
		<?php } ?>
		<div class="pf-element pf-buttons">
			<input type="hidden" name="com_shop_payment_id" value="<?php e($cur_payment_type->guid); ?>" />
			<input class="pf-button btn btn-primary" type="submit" value="<?php echo $this->review_form ? h($_->config->com_shop->complete_order_text) : 'Continue'; ?>" />
		</div>
	</form>
	<?php } else { ?>
	<script type="text/javascript">
		$_(function(){
			var get_form = function(payment_data){
				$.ajax({
					url: <?php echo json_encode(pines_url('com_shop', 'checkout/paymentform')); ?>,
					type: "POST",
					dataType: "html",
					data: {"name": payment_data.processing_type},
					error: function(XMLHttpRequest, textStatus){
						$_.error("An error occured while trying to retrieve the data form:\n"+$_.safe(XMLHttpRequest.status)+": "+$_.safe(textStatus));
					},
					success: function(data){
						if (data == null)
							return;
						$("#p_muid_payment_form").slideUp("fast", function(){
							var form = $(this).html(data);
							if (payment_data.data) {
								$.each(payment_data.data, function(i, val){
									form.find(":input:not(:radio, :checkbox)[name="+i+"]").val(val);
									form.find(":input:radio[name="+i+"][value="+val+"]").attr("checked", "checked");
									if (val == "")
										form.find(":input:checkbox[name="+i+"]").removeAttr("checked");
									else
										form.find(":input:checkbox[name="+i+"][value="+val+"]").attr("checked", "checked");
								});
							}
							form.slideDown("fast");
						});
					}
				});
			};

			$("#p_muid_payment_types").on("change", "input[name=payment_type]", function(){
				var radio = $(this);
				var payment = JSON.parse(radio.val());
				if (radio.attr("checked"))
					get_form(payment);
				$("input[name=com_shop_payment_id]", "#p_muid_form").val(payment.guid);
			});
			$("input:checked[name=payment_type]", "#p_muid_payment_types").change();
		});
	</script>
	<div class="pf-element" id="p_muid_payment_types">
		<?php foreach ($this->payment_types as $cur_payment_type) { ?>
		<label><input type="radio" name="payment_type" value="<?php e(json_encode(array('guid' => "$cur_payment_type->guid", 'processing_type' => $cur_payment_type->processing_type, 'name' => $cur_payment_type->name, 'data' => $cur_payment_type->is($this->payment->entity) ? $this->payment->data : null))); ?>"<?php echo $cur_payment_type->is($this->payment->entity) ? ' checked="checked"' : ''; ?> /> <?php e($cur_payment_type->name); ?></label>
		<?php } ?>
	</div>
	<form id="p_muid_form" method="POST" action="<?php e(pines_url('com_shop', 'checkout/paymentsave')); ?>">
		<br class="pf-clearing" />
		<div id="p_muid_payment_form"></div>
		<?php if ($this->review_form) { ?>
		<div class="pf-element pf-full-width">
			<span class="pf-label">Order Comments</span>
			<textarea class="pf-field" rows="1" cols="35" name="comments"><?php e($this->entity->comments); ?></textarea>
		</div>
		<?php } ?>
		<div class="pf-element pf-buttons">
			<input type="hidden" name="com_shop_payment_id" value="" />
			<input class="pf-button btn btn-primary" type="submit" value="<?php echo $this->review_form ? h($_->config->com_shop->complete_order_text) : 'Continue'; ?>" />
		</div>
	</form>
	<?php } ?>
</div>