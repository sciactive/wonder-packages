<?php
/**
 * Provides a form for the user to edit a payment type.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = (!isset($this->entity->guid)) ? 'Editing New Payment Type' : 'Editing ['.h($this->entity->name).']';
$this->note = 'Provide payment type details in this form.';
?>
<form class="pf-form" method="post" action="<?php e(pines_url('com_sales', 'paymenttype/save')); ?>">
	<?php if (isset($this->entity->guid)) { ?>
	<div class="date_info" style="float: right; text-align: right;">
		<?php if (isset($this->entity->user)) { ?>
		<div>User: <span class="date"><?php e("{$this->entity->user->name} [{$this->entity->user->username}]"); ?></span></div>
		<div>Group: <span class="date"><?php e("{$this->entity->group->name} [{$this->entity->group->groupname}]"); ?></span></div>
		<?php } ?>
		<div>Created: <span class="date"><?php e(format_date($this->entity->cdate, 'full_short')); ?></span></div>
		<div>Modified: <span class="date"><?php e(format_date($this->entity->mdate, 'full_short')); ?></span></div>
	</div>
	<?php } ?>
	<div class="pf-element">
		<label><span class="pf-label">Name</span>
			<input class="pf-field form-control" type="text" name="name" size="24" value="<?php e($this->entity->name); ?>" /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Enabled</span>
			<input class="pf-field" type="checkbox" name="enabled" value="ON"<?php echo $this->entity->enabled ? ' checked="checked"' : ''; ?> /></label>
	</div>
	<?php if ($_->config->com_sales->com_shop) { ?>
	<div class="pf-element">
		<label><span class="pf-label">Enabled in Shop</span>
			<span class="pf-note">Check to make this a web shop payment type.</span>
			<span class="pf-note">Uncheck "Enabled" to <em>only</em> show this in the web shop.</span>
			<input class="pf-field" type="checkbox" name="shop" value="ON"<?php echo $this->entity->shop ? ' checked="checked"' : ''; ?> /></label>
	</div>
	<?php } ?>
	<div class="pf-element">
		<label><span class="pf-label">Kick Drawer</span>
			<span class="pf-note">If set, when this payment type is used, the cash drawer will be kicked open.</span>
			<input class="pf-field" type="checkbox" name="kick_drawer" value="ON"<?php echo $this->entity->kick_drawer ? ' checked="checked"' : ''; ?> /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Change Type</span>
			<span class="pf-note">If set, change will be given from this payment type. Usually "Cash" is the change type.</span>
			<input class="pf-field" type="checkbox" name="change_type" value="ON"<?php echo $this->entity->change_type ? ' checked="checked"' : ''; ?> /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Minimum Charge</span>
			<span class="pf-note">The minimum charge in dollars that this payment type will accept.</span>
			<input class="pf-field form-control" type="text" name="minimum" size="24" value="<?php e($this->entity->minimum); ?>" /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Maximum Charge</span>
			<span class="pf-note">The maximum charge in dollars that this payment type will accept.</span>
			<input class="pf-field form-control" type="text" name="maximum" size="24" value="<?php e($this->entity->maximum); ?>" /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Allow Return Payment</span>
			<span class="pf-note">If set, a negative payment on a return can be used to charge a return fee.</span>
			<input class="pf-field" type="checkbox" name="allow_return" value="ON"<?php echo $this->entity->allow_return ? ' checked="checked"' : ''; ?> /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Processing Type</span>
			<span class="pf-note">This will determine how the payment is approved and processed.</span>
			<select class="pf-field form-control" name="processing_type" size="6">
				<?php foreach ($this->processing_types as $cur_type) { ?>
				<option value="<?php e($cur_type['name']); ?>" title="<?php e($cur_type['description']); ?>"<?php echo $this->entity->processing_type == $cur_type['name'] ? ' selected="selected"' : ''; ?>><?php e($cur_type['cname']); ?></option>
				<?php } ?>
			</select></label>
	</div>
	<div class="pf-element pf-buttons">
		<?php if ( isset($this->entity->guid) ) { ?>
		<input type="hidden" name="id" value="<?php e($this->entity->guid); ?>" />
		<?php } ?>
		<input class="pf-button btn btn-primary" type="submit" value="Submit" />
		<input class="pf-button btn btn-default" type="button" onclick="$_.get(<?php e(json_encode(pines_url('com_sales', 'paymenttype/list'))); ?>);" value="Cancel" />
	</div>
</form>