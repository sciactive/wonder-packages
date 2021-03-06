<?php
/**
 * Edit a tab.
 *
 * @package Components\dash
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Edit Tab';
$_->com_bootstrap->load();
$max_columns = $_->config->com_bootstrap->grid_columns;
$default_column = h(floor($max_columns / 3));
?>
<form class="pf-form" method="post" id="p_muid_form" action="<?php e(pines_url('com_dash', 'dashboard/tabsave', array('id' => "{$this->entity->guid}"))); ?>">
	<style type="text/css" scoped="scoped">
		#p_muid_form .new_column {
			min-height: 100px;
			cursor: pointer;
		}
	</style>
	<script type="text/javascript">
		$_(function(){
			var col_input = $("#p_muid_form [name=columns]");
			var columns = $("#p_muid_cols").sortable({
				axis: "x",
				helper: "clone",
				placeholder: "alert placeholder",
				forcePlaceholderSize: true,
				start: function(e, ui){
					var classes = ui.item.attr("class");
					ui.placeholder.addClass(classes).removeClass("alert-info");
					// Since Bootstrap's grid uses :first-child to remove left margin, move this to the end.
					ui.item.appendTo("#p_muid_cols");
				},
				update: function(){
					update_columns();
				}
			}).on("click", ".remove_column", function(){
				var col = $(this).closest(".new_column");
				// Don't remove if it's the last one.
				if (col.siblings().length) {
					col.remove();
					size_columns();
					update_columns();
				}
			}).on("click", ".grow_column", function(){
				var max_columns = $_.com_bootstrap_get_columns();
				// Check to make sure we aren't growing too big.
				var total_cols = 0;
				columns.children().each(function(){
					// Add the column width of each column.
					var col = $(this);
					for (var i=1; i<=max_columns; i++) {
						if (col.hasClass("col-sm-"+i)) {
							total_cols += i;
							return;
						}
					}
				});
				if (total_cols >= max_columns) {
					alert("You must shrink another column before you can grow this one.");
					return;
				}
				var col = $(this).closest(".new_column"), cur_size = 1;
				// Get the column's size.
				for (var i=1; i<=max_columns; i++) {
					if (col.hasClass("col-sm-"+i)) {
						cur_size = i;
						break;
					}
				}
				if (cur_size == max_columns)
					return;
				// Grow the column.
				col.removeClass("col-sm-"+cur_size).attr("class", "col-sm-"+(cur_size+1)+" "+col.attr("class"));
				update_columns();
			}).on("click", ".shrink_column", function(){
				var max_columns = $_.com_bootstrap_get_columns();
				var col = $(this).closest(".new_column"), cur_size = 1;
				// Get the column's size.
				for (var i=1; i<=max_columns; i++) {
					if (col.hasClass("col-sm-"+i)) {
						cur_size = i;
						break;
					}
				}
				if (cur_size == 1)
					return;
				// Shrink the column.
				col.removeClass("col-sm-"+cur_size).attr("class", "col-sm-"+(cur_size-1)+" "+col.attr("class"));
				update_columns();
			});
			$("#p_muid_add_column").click(function(){
				// Add a new column.
				var max_columns = $_.com_bootstrap_get_columns(), all_columns = columns.children();
				if (all_columns.length >= max_columns) {
					alert("You have the maximum number of columns.");
					return;
				}
				columns.append(columns.children(":last-child").clone().removeAttr("id"));
				size_columns();
				update_columns();
			});
			var size_columns = function(){
				// Fit the columns into the width evenly.
				var max_columns = $_.com_bootstrap_get_columns(), all_columns = columns.children();
				for (var i=1; i<=max_columns; i++)
					all_columns.removeClass("col-sm-"+i);
				all_columns.attr("class", "col-sm-"+(Math.floor(max_columns/all_columns.length))+" "+all_columns.eq(0).attr("class"));
			};
			var update_columns = function(){
				var col_struct = [], max_columns = $_.com_bootstrap_get_columns();
				columns.children().each(function(){
					var cur_col_struct = {}, col = $(this);
					// Get the column's size.
					for (var i=1; i<=max_columns; i++) {
						if (col.hasClass("col-sm-"+i)) {
							cur_col_struct.size = i;
							break;
						}
					}
					// Does the column have a key?
					if (col.attr("id"))
						cur_col_struct.key = col.attr("id");
					col_struct.push(cur_col_struct);
				});
				col_input.val(JSON.stringify(col_struct));
			};
			update_columns();

			<?php if ( !empty($this->key) ) { ?>
			$("#p_muid_delete").click(function(){
				if (!confirm("Are you sure you want to delete this tab and all of its buttons, widgets, and configuration?\nThis cannot be undone."))
					return;
				$.ajax({
					url: <?php echo json_encode(pines_url('com_dash', 'dashboard/tabremove_json', array('id' => "{$this->entity->guid}"))); ?>,
					type: "POST",
					dataType: "json",
					data: {"key": <?php echo json_encode($this->key); ?>},
					beforeSend: function(){
						$(this).text("Deleting...");
					},
					error: function(XMLHttpRequest, textStatus){
						$_.error("An error occured while trying to delete the tab:\n"+$_.safe(XMLHttpRequest.status)+": "+$_.safe(textStatus));
					},
					success: function(data){
						$(this).text("Delete Tab");
						if (data == "last") {
							alert("This is the only tab, so it can't be deleted.");
							return;
						} else if (!data) {
							$_.error("The tab could not be deleted.");
							return;
						}
						var tab_pane = $("#p_muid_form").closest(".tab-pane"),
						tab = tab_pane.data("trigger_link").closest("li"),
						shownext = tab.prev();
						if (!shownext.length)
							shownext = tab.next();
						shownext.find("a").trigger("show").tab("show");
						tab_pane.remove();
						tab.remove();
					}
				});
			});
			<?php } ?>
		});

		function p_muid_cancel() {
			$("#p_muid_form").closest(".tab-pane").data("tab_loaded", false).data("trigger_link").trigger("show");
		}
	</script>
	<div class="pf-element pf-heading">
		<?php if ( !empty($this->key) ) { ?>
		<button class="btn btn-default" id="p_muid_delete" type="button" style="float: right; margin-bottom: 4px;">Delete Tab</button>
		<?php } ?>
		<h3>Editing <?php echo isset($this->tab) ? 'Tab ['.h($this->tab['name']).']' : 'New Tab'; ?></h3>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Name</span>
			<input class="pf-field form-control" type="text" name="name" size="24" value="<?php e($this->tab['name']); ?>" /></label>
	</div>
	<div class="pf-element pf-full-width">
		<span class="pf-label">Layout</span>
		<span class="pf-field">
			<a href="javascript:void(0);" id="p_muid_add_column">Add a Column</a>
		</span>
		<br /><br />
		<div class="row" style="margin-bottom: 1em;">
			<div class="col-sm-<?php e($max_columns); ?> new_column" style="min-height: 40px; line-height: 40px; text-align: center;">
				<div class="alert alert-info">
					Button area.
				</div>
			</div>
		</div>
		<div class="row" id="p_muid_cols">
			<?php if (isset($this->tab['columns'])) { foreach ($this->tab['columns'] as $cur_key => $cur_column) {
				$col_style = h($cur_column['size'] < 1 ? floor($max_columns * $cur_column['size']) : $cur_column['size']); ?>
			<div class="col-sm-<?php echo $col_style; ?> new_column" id="<?php e($cur_key); ?>">
				<div class="alert alert-info">
					<div style="float: right;">
						<a href="javascript:void(0);" class="remove_column btn btn-xs btn-info">Remove</a>
					</div>
					<a href="javascript:void(0);" class="grow_column btn btn-xs">Grow</a> <a href="javascript:void(0);" class="shrink_column btn btn-default btn-xs">Shrink</a>
					<div style="text-align: center; margin-top: 2em;">Drag me to reorder.</div>
				</div>
			</div>
			<?php } } else { ?>
			<div class="col-sm-<?php echo $default_column; ?> new_column">
				<div class="alert alert-info">
					<div style="float: right;">
						<a href="javascript:void(0);" class="remove_column btn btn-xs btn-danger">Remove</a>
					</div>
					<a href="javascript:void(0);" class="grow_column btn btn-primary btn-xs">Grow</a> <a href="javascript:void(0);" class="shrink_column btn btn-default btn-xs">Shrink</a>
					<div style="text-align: center; margin-top: 2em;">Drag me to reorder.</div>
				</div>
			</div>
			<div class="col-sm-<?php echo $default_column; ?> new_column">
				<div class="alert alert-info">
					<div style="float: right;">
						<a href="javascript:void(0);" class="remove_column btn btn-xs btn-danger">Remove</a>
					</div>
					<a href="javascript:void(0);" class="grow_column btn btn-primary btn-xs">Grow</a> <a href="javascript:void(0);" class="shrink_column btn btn-default btn-xs">Shrink</a>
					<div style="text-align: center; margin-top: 2em;">Drag me to reorder.</div>
				</div>
			</div>
			<div class="col-sm-<?php echo $default_column; ?> new_column">
				<div class="alert alert-info">
					<div style="float: right;">
						<a href="javascript:void(0);" class="remove_column btn btn-xs btn-danger">Remove</a>
					</div>
					<a href="javascript:void(0);" class="grow_column btn btn-primary btn-xs">Grow</a> <a href="javascript:void(0);" class="shrink_column btn btn-default btn-xs">Shrink</a>
					<div style="text-align: center; margin-top: 2em;">Drag me to reorder.</div>
				</div>
			</div>
			<?php } ?>
		</div>
		<input type="hidden" name="columns" value="" />
	</div>
	<div class="pf-element pf-buttons">
		<?php if ( !empty($this->key) ) { ?>
		<input type="hidden" name="key" value="<?php e($this->key); ?>" />
		<?php } ?>
		<input class="pf-button btn btn-primary" type="submit" value="Submit" />
		<?php if ( !empty($this->key) ) { ?>
		<input class="pf-button btn btn-default" type="button" onclick="p_muid_cancel();" value="Cancel" />
		<?php } ?>
	</div>
	<br class="pf-clearing" />
</form>