<?php
/**
 * Display a form to enter calendar events.
 *
 * Built upon:
 * FullCalendar Created by Adam Shaw
 * http://arshaw.com/fullcalendar/
 * 
 * @package Components\calendar
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<style type="text/css" >
	#p_muid_form .form_center {
		text-align: center;
	}
	#p_muid_form .form_input {
		width: 170px;
	}
</style>
<script type='text/javascript'>
	$_(function(){
		$("#p_muid_start").datepicker({
			dateFormat: "yy-mm-dd",
			changeMonth: true,
			changeYear: true,
			showOtherMonths: true,
			selectOtherMonths: true
		});
		$("#p_muid_end").datepicker({
			dateFormat: "yy-mm-dd",
			changeMonth: true,
			changeYear: true,
			showOtherMonths: true,
			selectOtherMonths: true
		});

		<?php if (gatekeeper('com_calendar/managecalendar')) { ?>
		// Location Tree
		var location = $("#p_muid_form [name=location]");
		$("#p_muid_form .location_tree")
		.bind("select_node.jstree", function(e, data){
			var selected = data.inst.get_selected().attr("id").replace("p_muid_", "");
			location.val(selected);
			update_employees(selected);
		})
		.bind("before.jstree", function (e, data){
			if (data.func == "parse_json" && "args" in data && 0 in data.args && "attr" in data.args[0] && "id" in data.args[0].attr)
				data.args[0].attr.id = "p_muid_"+data.args[0].attr.id;
		})
		.bind("loaded.jstree", function(e, data){
			var path = data.inst.get_path("#"+data.inst.get_settings().ui.initially_select, true);
			if (!path.length) return;
			data.inst.open_node("#"+path.join(", #"), false, true);
		})
		.jstree({
			"plugins" : [ "themes", "json_data", "ui" ],
			"json_data" : {
				"ajax" : {
					"dataType" : "json",
					"url" : <?php echo json_encode(pines_url('com_jstree', 'groupjson')); ?>
				}
			},
			"ui" : {
				"select_limit" : 1,
				"initially_select" : ["<?php echo (int) $this->location; ?>"]
			}
		});

		// This function reloads the employees when switching between locations.
		var update_employees = function(group_id){
			var employee = $("#p_muid_form [name=employee]");
			employee.empty();
			<?php
			// Load employee departments.
			foreach ($_->config->com_hrm->employee_departments as $cur_dept) {
				$cur_dept_info = explode(':', $cur_dept);
				$cur_name = $cur_dept_info[0];
				$cur_color = $cur_dept_info[1];
				$cur_select = (!isset($this->entity->employee->group) && $this->entity->district == $cur_name) ? 'selected=\"selected\"' : '';
			?>
				employee.append("<option value=\"<?php e($cur_name); ?>:<?php e($cur_color); ?>\" <?php echo $cur_select; ?>><?php e($cur_name); ?></option>");
			<?php }
			// Load employees for this location.
			foreach ($this->employees as $cur_employee) {
				if (!isset($cur_employee->group))
					continue;
				$cur_select = (isset($this->entity->employee->group) && $this->entity->employee->is($cur_employee)) ? 'selected=\"selected\"' : ''; ?>
				if (group_id == <?php echo json_encode($cur_employee->group->guid); ?>)
					employee.append("<option value=\"<?php e($cur_employee->guid); ?>\" <?php echo $cur_select; ?>><?php e($cur_employee->name); ?></option>");
			<?php } ?>
		};
		<?php } ?>

		var timespan = $("[name=time_start], [name=time_end]", "#p_muid_form");
		$("#p_muid_form [name=all_day]").change(function(){
			if ($(this).is(":checked")) {
				timespan.attr("disabled", "disabled");
			} else {
				timespan.removeAttr("disabled");
			}
		}).change();

		$("#p_muid_start").change(function(){
			var start_date = new Date($(this).val());
			var end_date = new Date($("#p_muid_end").val());
			if (start_date > end_date)
				$("#p_muid_end").val($(this).val());
		}).change();

		$(".combobox", ".calendar_form").each(function(){
			var box = $(this);
			var autobox = box.children("input").autocomplete({
				minLength: 0,
				source: $.map(box.children("select").children(), function(elem){
					return $(elem).attr("value");
				})
			});
			box.children("a").hover(function(){
				$(this).addClass("ui-icon-circle-triangle-s").removeClass("ui-icon-triangle-1-s");
			}, function(){
				$(this).addClass("ui-icon-triangle-1-s").removeClass("ui-icon-circle-triangle-s");
			}).click(function(){
				autobox.focus().autocomplete("search", "");
			});
		});
	});
</script>
<div class="pf-form calendar_form" id="p_muid_form">
	<div style="float: left;">
		<div class="pf-element">
			<label>
				<span class="pf-label">Label</span><br />
				<input class="form_input" type="text" id="p_muid_event_label" name="event_label" value="<?php e($this->entity->label); ?>" />
			</label>
		</div>
		<div class="pf-element">
			<label>
				<span class="pf-label">Info</span><br />
				<textarea rows="2" cols="18" name="information"><?php e($this->entity->information); ?></textarea>
			</label>
		</div>
		<?php if (gatekeeper('com_calendar/managecalendar')) { ?>
		<div class="pf-element">
			<label>
				<span class="pf-label">Employee</span><br />
				<select class="form_input" name="employee"></select>
			</label>
			<label><input class="pf-field" type="checkbox" name="private" value="ON" <?php echo ($this->entity->private) ? 'checked="checked" ' : ''; ?>/>Private</label>
		</div>
		<div class="pf-element location_tree" style="padding-bottom: 1em; width: 90%; max-height: 75px;"></div>
		<?php } else { ?>
		<input type="hidden" name="employee" value="<?php e($_SESSION['user']->guid); ?>" />
		<input type="hidden" name="private" value="ON" checked="checked" />
		<?php } ?>
	</div>
	<?php
		if (isset($this->entity->start)) {
			$start_date = format_date($this->entity->start, 'date_sort', '', $this->timezone);
			$start_time = format_date($this->entity->start, 'time_short', '', $this->timezone);
			$end_date = format_date($this->entity->end, 'date_sort', '', $this->timezone);
			$end_time = format_date($this->entity->end, 'time_short', '', $this->timezone);
		}
	?>
	<div style="float: right;">
		<div class="pf-element pf-full-width">
			<label style="float: right;"><input class="pf-field" type="checkbox" name="all_day" value="ON" <?php echo ($this->entity->all_day) ? 'checked="checked" ' : ''; ?>/>All Day</label>
			<span class="pf-label">Start</span><br style="clear: right;" />
			<input class="form_center" type="text" size="10" id="p_muid_start" name="start" value="<?php empty($start_date) ? e(format_date(time(), 'date_sort', '', $this->timezone)) : e($start_date); ?>" />
			<span class="combobox">
				<input class="pf-field" type="text" name="time_start" size="10" value="<?php empty($start_time) ? e(format_date(time(), 'time_short', '', $this->timezone)) : e($start_time); ?>" />
				<a href="javascript:void(0);" class="ui-icon ui-icon-triangle-1-s"></a>
				<select style="display: none;">
					<option value="12:00 AM">12:00 AM</option>
					<option value="1:00 AM">1:00 AM</option>
					<option value="2:00 AM">2:00 AM</option>
					<option value="3:00 AM">3:00 AM</option>
					<option value="4:00 AM">4:00 AM</option>
					<option value="5:00 AM">5:00 AM</option>
					<option value="6:00 AM">6:00 AM</option>
					<option value="7:00 AM">7:00 AM</option>
					<option value="8:00 AM">8:00 AM</option>
					<option value="9:00 AM">9:00 AM</option>
					<option value="10:00 AM">10:00 AM</option>
					<option value="11:00 AM">11:00 AM</option>
					<option value="12:00 PM">12:00 PM</option>
					<option value="1:00 PM">1:00 PM</option>
					<option value="2:00 PM">2:00 PM</option>
					<option value="3:00 PM">3:00 PM</option>
					<option value="4:00 PM">4:00 PM</option>
					<option value="5:00 PM">5:00 PM</option>
					<option value="6:00 PM">6:00 PM</option>
					<option value="7:00 PM">7:00 PM</option>
					<option value="8:00 PM">8:00 PM</option>
					<option value="9:00 PM">9:00 PM</option>
					<option value="10:00 PM">10:00 PM</option>
					<option value="11:00 PM">11:00 PM</option>
				</select>
			</span>
		</div>
		<div class="pf-element pf-full-width">
			<span class="pf-label">End</span><br style="clear: right;" />
			<input class="form_center" type="text" size="10" id="p_muid_end" name="end" value="<?php empty($end_date) ? e(format_date(time(), 'date_sort', '', $this->timezone)) : e($end_date); ?>" />
			<span class="combobox">
				<input class="pf-field" type="text" name="time_end" size="10" value="<?php empty($end_time) ? e(format_date(time(), 'time_short', '', $this->timezone)) : e($end_time); ?>" />
				<a href="javascript:void(0);" class="ui-icon ui-icon-triangle-1-s"></a>
				<select style="display: none;">
					<option value="12:00 AM">12:00 AM</option>
					<option value="1:00 AM">1:00 AM</option>
					<option value="2:00 AM">2:00 AM</option>
					<option value="3:00 AM">3:00 AM</option>
					<option value="4:00 AM">4:00 AM</option>
					<option value="5:00 AM">5:00 AM</option>
					<option value="6:00 AM">6:00 AM</option>
					<option value="7:00 AM">7:00 AM</option>
					<option value="8:00 AM">8:00 AM</option>
					<option value="9:00 AM">9:00 AM</option>
					<option value="10:00 AM">10:00 AM</option>
					<option value="11:00 AM">11:00 AM</option>
					<option value="12:00 PM">12:00 PM</option>
					<option value="1:00 PM">1:00 PM</option>
					<option value="2:00 PM">2:00 PM</option>
					<option value="3:00 PM">3:00 PM</option>
					<option value="4:00 PM">4:00 PM</option>
					<option value="5:00 PM">5:00 PM</option>
					<option value="6:00 PM">6:00 PM</option>
					<option value="7:00 PM">7:00 PM</option>
					<option value="8:00 PM">8:00 PM</option>
					<option value="9:00 PM">9:00 PM</option>
					<option value="10:00 PM">10:00 PM</option>
					<option value="11:00 PM">11:00 PM</option>
				</select>
			</span>
		</div>
		<div class="pf-element">
			<small>Using timezone: <?php e($this->timezone); ?></small>
			<input type="hidden" name="timezone" value="<?php e($this->timezone); ?>" />
		</div>
	</div>
	<input type="hidden" name="location" value="<?php e($this->location); ?>" />
	<?php if (isset($this->entity->guid)) { ?>
	<input type="hidden" name="id" value="<?php e($this->entity->guid); ?>" />
	<?php } ?>
</div>