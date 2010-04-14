<?php
/**
 * Display a form to edit configuration settings.
 *
 * @package Pines
 * @subpackage com_configure
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');
$this->title = "Editing Configuration for {$this->entity->info->name} {$this->entity->info->version} ({$this->entity->name})";
if ($this->entity->peruser)
	$this->note = "For user/group {$this->entity->user->name} [{$this->entity->user->username}{$this->entity->user->groupname}].";
?>
<style type="text/css">
	/* <![CDATA[ */
	#configuration_form .setting .ui-ptags {
		display: inline-block;
	}
	#configuration_form .default .pf-field {
		display: inline-block;
		padding: .2em;
		margin-right: .3em;
	}
	/* ]]> */
</style>
<script type="text/javascript">
	// <![CDATA[
	$(function(){
		$("#configuration_form .do_tags").ptags({ptags_delimiter: ';;'});
		$("#configuration_form").delegate("input.default_checkbox", "change", function(){
			var checkbox = $(this);
			if (checkbox.attr("checked")) {
				checkbox.closest("div.pf-element").children("div.default").hide().end().children("div.setting").show();
			} else {
				checkbox.closest("div.pf-element").children("div.setting").hide().end().children("div.default").show();
			}
		}).find("input.default_checkbox").change();
	});
	// ]]>
</script>
<form id="configuration_form" class="pf-form" action="<?php echo htmlentities(pines_url('com_configure', 'save')); ?>" method="post">
	<div class="pf-element pf-heading">
		<p>Check a setting to set it manually, or leave it unchecked to use the <?php echo $this->entity->peruser ? 'system configured' : 'default'; ?> setting.</p>
	</div>
	<?php foreach ($this->entity->defaults as $cur_var) {
		if (key_exists($cur_var['name'], $this->entity->config_keys)) {
			$is_default = false;
			$cur_value = $this->entity->config_keys[$cur_var['name']];
		} else {
			$is_default = true;
			$cur_value = $cur_var['value'];
		} ?>
	<div class="pf-element pf-full-width">
		<label><span class="pf-label"><input type="checkbox" class="default_checkbox ui-widget-content" name="manset_<?php echo $cur_var['name']; ?>" value="ON" <?php echo $is_default ? '' : 'checked="checked" '; ?>/> <?php echo $cur_var['cname']; ?></span></label>
		<span class="pf-note"><?php print_r($cur_var['description']); ?></span>
		<div class="setting" style="display: none;">
			<?php if (is_array($cur_var['options'])) { ?>
				<?php foreach($cur_var['options'] as $key => $cur_option) {
					$display = is_string($key) ? $key : $cur_option; ?>
				<div class="pf-group">
					<label><input class="pf-field ui-widget-content" type="<?php echo is_array($cur_var['value']) ? 'checkbox' : 'radio'; ?>" name="opt_multi_<?php echo $cur_var['name']; ?><?php echo is_array($cur_var['value']) ? '[]' : ''; ?>" value="<?php echo addslashes(htmlentities(serialize($cur_option))); ?>" <?php echo ( (is_array($cur_value) && in_array($cur_option, $cur_value) || (!is_array($cur_value) && $cur_value == $cur_option)) ? 'checked="checked" ' : ''); ?>/> <?php echo htmlentities($display); ?></label><br />
				</div>
				<?php } ?>
			<?php } elseif (is_array($cur_var['value'])) { ?>
				<div class="pf-group">
					<?php if (is_int($cur_var['value'][0])) { ?>
					<input class="pf-field ui-widget-content do_tags" type="text" name="opt_int_<?php echo $cur_var['name']; ?>" value="<?php echo implode(';;', $cur_value); ?>" />
					<?php } elseif (is_float($cur_var['value'][0])) { ?>
					<input class="pf-field ui-widget-content do_tags" type="text" name="opt_float_<?php echo $cur_var['name']; ?>" value="<?php echo implode(';;', $cur_value); ?>" />
					<?php } elseif (is_string($cur_var['value'][0])) { ?>
					<div class="pf-field"><textarea rows="3" cols="35" class="ui-widget-content do_tags" style="width: 100%;" name="opt_string_<?php echo $cur_var['name']; ?>"><?php echo htmlentities(implode(';;', $cur_value), true); ?></textarea></div>
					<?php } ?>
				</div>
			<?php } else { ?>
				<?php if (is_bool($cur_var['value'])) { ?>
				<input class="pf-field ui-widget-content" type="checkbox" name="opt_bool_<?php echo $cur_var['name']; ?>" value="ON" <?php echo ($cur_value ? 'checked="checked" ' : ''); ?>/>
				<?php } elseif (is_int($cur_var['value'])) { ?>
				<input class="pf-field ui-widget-content" type="text" name="opt_int_<?php echo $cur_var['name']; ?>" value="<?php echo $cur_value; ?>" />
				<?php } elseif (is_float($cur_var['value'])) { ?>
				<input class="pf-field ui-widget-content" type="text" name="opt_float_<?php echo $cur_var['name']; ?>" value="<?php echo $cur_value; ?>" />
				<?php } elseif (is_string($cur_var['value'])) { ?>
				<div class="pf-field pf-full-width"><textarea rows="3" cols="35" class="ui-widget-content" style="width: 100%;" name="opt_string_<?php echo $cur_var['name']; ?>"><?php echo htmlentities($cur_value, true); ?></textarea></div>
				<?php } else { ?>
				<div class="pf-field pf-full-width"><textarea rows="3" cols="35" class="ui-widget-content" style="width: 100%;" name="opt_serial_<?php echo $cur_var['name']; ?>"><?php echo htmlentities(serialize($cur_value), true); ?></textarea></div>
				<?php } ?>
			<?php } ?>
		</div>
		<div class="pf-group default" style="display: none;">
			<?php if (is_array($cur_var['value'])) {
				foreach ($cur_var['value'] as $key => $cur_value) {
					echo '<div class="pf-field ui-corner-all ui-state-default ui-state-disabled">'.htmlentities(print_r(is_string($key) ? $key : $cur_value, true)).'</div>';
				}
			} else {
				echo '<div class="pf-field ui-corner-all ui-state-default ui-state-disabled">';
				if (is_bool($cur_var['value']))
					$cur_var['value'] = $cur_var['value'] ? 'Yes' : 'No';
				echo htmlentities(print_r($cur_var['value'], true));
				echo '</div>';
			} ?>
		</div>
	</div>
	<?php } ?>
	<div class="pf-element pf-buttons">
		<?php if ($this->entity->peruser) { ?>
		<input type="hidden" name="peruser" value="1" />
		<?php } ?>
		<input type="hidden" name="component" value="<?php echo $this->entity->name; ?>" />
		<input class="pf-button ui-state-default ui-priority-primary ui-corner-all" type="submit" value="Save" name="save" />
		<input class="pf-button ui-state-default ui-priority-secondary ui-corner-all" type="reset" value="Reset" name="reset" />
	</div>
</form>