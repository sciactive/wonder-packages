<?php
/**
 * Display a form to view sales reports.
 *
 * @package Components\reports
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');

$this->title = 'New Report';
$_->com_jstree->load();
?>
<style type="text/css" >
	.form_date {
		width: 80%;
		text-align: center;
	}
</style>
<script type='text/javascript'>
	$_(function(){
		$("#p_muid_form [name=start], #p_muid_form [name=end]").datepicker({
			dateFormat: "yy-mm-dd",
			changeMonth: true,
			changeYear: true,
			showOtherMonths: true,
			selectOtherMonths: true
		});
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
			employee.append("<option value='all' selected='selected'>Entire Location</option>");
			<?php foreach ($this->employees as $cur_employee) { // Load employees for the current location.
				if (!isset($cur_employee->group))
					continue;
				$cur_select = (isset($this->employee->group) && $this->employee->is($cur_employee)) ? 'selected=\"selected\"' : ''; ?>
				if (group_id == <?php echo json_encode($cur_employee->group->guid); ?>) {
					employee.append("<option value=\"<?php e($cur_employee->guid); ?>\" <?php echo $cur_select; ?>><?php e($cur_employee->name); ?></option>");
				}
			<?php } ?>
		};
	});
</script>
<form class="pf-form" method="post" id="p_muid_form" action="<?php e(pines_url('com_reports', 'reportsales')); ?>">
	<div class="pf-element">
		<label><input type="checkbox" name="descendants" value="ON" <?php echo $this->descendants ? 'checked="checked"' : ''; ?> /> Include Descendants</label>
		<div class="pf-element location_tree"></div>
	</div>
	<div class="pf-element pf-full-width">
		<select class="form-control" style="max-width: 100%;" name="employee"></select>
	</div>
	<div class="pf-element" style="padding-bottom: 0px;">
		<span class="pf-note">Start</span>
		<input class="form_date form-control" type="text" name="start" value="<?php ($this->date[0]) ? e(format_date($this->date[0], 'date_sort')) : e(format_date(time(), 'date_sort')); ?>" />
	</div>
	<div class="pf-element">
		<span class="pf-note">End</span>
		<input class="form_date form-control" type="text" name="end" value="<?php ($this->date[1]) ? e(format_date($this->date[1] - 1, 'date_sort')) : e(format_date(time(), 'date_sort')); ?>" />
	</div>
	<div class="pf-element">
		<input type="hidden" name="location" value="<?php e($this->location); ?>" />
		<input class="btn btn-primary" type="submit" value="View Report" />
	</div>
</form>