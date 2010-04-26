<?php
/**
 * Edits an employees timeclock history.
 *
 * @package Pines
 * @subpackage com_hrm
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');
$this->title = "Edit Timeclock for {$this->entity->name}";
?>
<script type="text/javascript">
	// <![CDATA[
	$(function(){
		var cur_entry;
		var new_entry;
		var timezone = "<?php echo addslashes($this->entity->get_timezone()); ?>";
		function format_time(elem, timestamp) {
			elem.html("Formatting...");
			$.ajax({
				url: "<?php echo pines_url('system', 'date_format'); ?>",
				type: "POST",
				dataType: "text",
				data: {"timestamp": timestamp, "timezone": timezone},
				error: function(){
					elem.html("Couldn't format.");
				},
				success: function(data){
					elem.html(data);
				}
			});
		}

		function clean_up() {
			// Sort by timestamp.
			$("#timeclock_edit").prepend($("#timeclock_edit div.entry").get().sort(function(a, b){
				return $(a).find(".timestamp").text() - $(b).find(".timestamp").text();
			}));
			// Make sure statuses are sequential.
			$("#timeclock_edit .entry:even .status").html("in");
			$("#timeclock_edit .entry:odd .status").html("out");
			save_to_form();
		}

		function save_to_form() {
			// Turn the entries into an array.
			var entries = [];
			$("#timeclock_edit div.entry").each(function(){
				entries[entries.length] = {
					"time": parseInt($(this).find(".timestamp").text()),
					"status": $(this).find(".status").text()
				};
			});
			$("#timeclock_form input[name=clock]").val(JSON.stringify(entries));
		}

		$("#timeclock_edit .time").live("mouseover", function(){
			$(this).closest("div").addClass("ui-state-hover");
		}).live("mouseout", function(){
			$(this).closest("div").removeClass("ui-state-hover");
		}).live("click", function(){
			cur_entry = $(this).closest(".pf-element");
			$("#cur_time").val($(this).text());
			$("#date_time_dialog").dialog("open");
		});

		$("#timeclock_edit div.entry button").live("click", function(){
			$(this).closest(".pf-element").animate({height: 0, opacity: 0}, "normal", function(){
				$(this).remove();
				clean_up();
			});
		});

		$("#timeclock_edit button.add-button").click(function(){
			new_entry = $("#timeclock_entry_template").clone().addClass("entry").removeAttr("id");
			$(this).before(new_entry);
			new_entry.find(".timestamp").html(Math.floor(new Date().getTime() / 1000));
			format_time(new_entry.find(".time"), new_entry.find(".timestamp").text());
			clean_up();
			new_entry.slideDown("normal");
			$("#new_time").val("now");
			$("#add_time_dialog").dialog("open");
		});

		$("#date_time_dialog").dialog({
			bgiframe: true,
			autoOpen: false,
			modal: true,
			buttons: {
				"Done": function() {
					$.ajax({
						url: "<?php echo pines_url('system', 'date_get_timestamp'); ?>",
						type: "POST",
						dataType: "text",
						data: {"date": $("#cur_time").val(), "timezone": timezone},
						error: function(){
							alert("Couldn't get a timestamp from the server.");
							$("#date_time_dialog").dialog('close');
						},
						success: function(data){
							$("#date_time_dialog").dialog('close');
							cur_entry.find(".timestamp").html(data);
							format_time(cur_entry.find(".time"), data);
							cur_entry.find("div").addClass("ui-state-highlight");
							clean_up();
						}
					});
				}
			}
		});
		
		$("#add_time_dialog").dialog({
			bgiframe: true,
			autoOpen: false,
			modal: true,
			buttons: {
				"Done": function() {
					$.ajax({
						url: "<?php echo pines_url('system', 'date_get_timestamp'); ?>",
						type: "POST",
						dataType: "text",
						data: {"date": $("#new_time").val(), "timezone": timezone},
						error: function(){
							alert("Couldn't get a timestamp from the server.");
							$("#add_time_dialog").dialog('close');
						},
						success: function(data){
							$("#add_time_dialog").dialog('close');
							new_entry.find(".timestamp").html(data);
							format_time(new_entry.find(".time"), data);
							new_entry.find("div").addClass("ui-state-highlight");
							clean_up();
						}
					});
				}
			}
		});
		save_to_form();
	});
	// ]]>
</script>
<div class="pf-form" id="timeclock_edit">
<?php foreach($this->entity->timeclock as $key => $entry) { ?>
	<div class="pf-element pf-full-width entry">
		<div style="padding: 3px;" class="ui-helper-clearfix ui-widget-content ui-corner-all">
			<button style="float: right; margin: 3px;" class="ui-state-default ui-corner-all">Delete</button>
			<span class="pf-label time" style="cursor: pointer;"><?php echo format_date($entry['time'], 'full_sort', '', $this->entity->get_timezone(true)); ?></span>
			<span class="pf-note timestamp"><?php echo $entry['time']; ?></span>
			<span class="pf-field status"><?php echo $entry['status']; ?></span>
		</div>
	</div>
<?php } ?>
	<div id="timeclock_entry_template" class="pf-element pf-full-width" style="display: none;">
		<div style="padding: 3px;" class="ui-helper-clearfix ui-widget-content ui-corner-all">
			<button style="float: right; margin: 3px;" class="ui-state-default ui-corner-all">Delete</button>
			<span class="pf-label time" style="cursor: pointer;"></span>
			<span class="pf-note timestamp"></span>
			<span class="pf-field status"></span>
		</div>
	</div>
	<button style="float: right; margin: 3px;" class="add-button ui-state-default ui-corner-all">Add</button>
	<div id="date_time_dialog" title="Adjust Time">
		<span>Time:</span><br />
		<input id="cur_time" type="text" size="24" /><br />
		<small>Relative times are calculated from now, so "-1 day" means this time, yesterday.</small><br />
		<br /><span>Examples:</span><br />
		<small>now</small><br />
		<small>10 September 2000 8:13 AM</small><br />
		<small>10 September 2000 8:13 AM +8 hours</small><br />
		<small>-1 day</small><br />
		<small>+1 week 2 days 4 hours 2 seconds</small><br />
		<small>next Thursday</small><br />
		<small>last Monday 4pm</small>
	</div>
	<div id="add_time_dialog" title="Add a New Time">
		<span>Time:</span><br />
		<input id="new_time" type="text" size="24" /><br />
		<small>Relative times are calculated from now, so "-1 day" means 24 hours ago.</small><br />
		<br /><span>Examples:</span><br />
		<small>2 hours ago</small><br />
		<small>Jul 10 8:13</small><br />
		<small>10 July 2000 8:13 AM PST</small><br />
		<small>-1 day</small><br />
		<small>+1 week 2 days 4 hours 2 seconds</small><br />
		<small>next Thursday</small><br />
		<small>last Monday 4pm</small>
	</div>
	<form method="post" id="timeclock_form" action="<?php echo htmlentities(pines_url('com_hrm', 'savetimeclock')); ?>">
		<input type="hidden" name="clock" value="" />
		<input type="hidden" name="id" value="<?php echo $this->entity->guid; ?>" />
		<div class="pf-element pf-buttons">
			<input class="pf-button ui-state-default ui-priority-primary ui-corner-all" type="submit" value="Submit" />
			<input class="pf-button ui-state-default ui-priority-secondary ui-corner-all" type="button" onclick="pines.get('<?php echo htmlentities(pines_url('com_hrm', 'listtimeclocks')); ?>');" value="Cancel" />
		</div>
	</form>
</div>