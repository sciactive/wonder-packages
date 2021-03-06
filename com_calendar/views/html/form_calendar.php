<?php
/**
 * Display a form to view schedules for different company divisions/locations.
 *
 * @package Components\calendar
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Actions';
$_->com_jstree->load();
$_->com_ptags->load();
if ($_->config->com_calendar->com_customer)
	$_->com_customer->load_customer_select();
?>
<style type="text/css" >
	#p_muid_filter button {
		padding: 4px;
		font-size: 8px;
		line-height: 10px;
	}
	.p_muid_btn {
		display: inline-block;
		width: 16px;
		height: 16px;
	}
	#p_muid_actions button {
		padding: 0.2em;
	}
	#p_muid_actions button .ui-button-text {
		padding: 0;
	}
	#p_muid_interaction_dialog ul {
		font-size: 0.8em;
		list-style-type: disc;
		padding-left: 10px;
	}
	.calendar_form .combobox {
		position: relative;
	}
	.calendar_form .combobox input {
		padding-right: 32px;
	}
	.calendar_form .combobox a {
		display: block;
		position: absolute;
		right: 8px;
		top: 50%;
		margin-top: -8px;
	}
	.ui-autocomplete {
		max-height: 200px;
		overflow-y: auto;
		/* prevent horizontal scrollbar */
		overflow-x: hidden;
		/* add padding to account for vertical scrollbar */
		padding-right: 20px;
	}
	/* IE 6 doesn't support max-height
	 * we use height instead, but this forces the menu to always be this tall
	 */
	* html .ui-autocomplete {
		height: 200px;
	}
</style>
<script type='text/javascript'>
	$_(function(){
		var change_counter = 0;
		$("#p_muid_employee").change(function(){
			if (change_counter > 0)
				$_.get(<?php echo json_encode(pines_url('com_calendar', 'editcalendar')); ?>, {
					"view_type": <?php echo json_encode($this->view_type); ?>,
					"start": <?php echo json_encode(format_date($this->date[0], 'date_sort', '', $this->timezone)); ?>,
					"end": <?php echo json_encode(format_date($this->date[1] - 1, 'date_sort', '', $this->timezone)); ?>,
					"location": <?php echo json_encode((string) $this->location->guid); ?>,
					"employee": $(this).val(),
					"descendants": <?php echo $this->descendants ? '"true"' : '"false"'; ?>,
					"filter": <?php echo json_encode($this->filter); ?>
				});
			change_counter++;
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

		$("#p_muid_filter").on("click", "button", function() {
			$_.get(<?php echo json_encode(pines_url('com_calendar', 'editcalendar')); ?>, {
				view_type: <?php echo json_encode($this->view_type); ?>,
				start: <?php echo json_encode(format_date($this->date[0], 'date_sort', '', $this->timezone)); ?>,
				end: <?php echo json_encode(format_date($this->date[1] - 1, 'date_sort', '', $this->timezone)); ?>,
				location: <?php echo json_encode((string) $this->location->guid); ?>,
				employee: <?php echo json_encode((string) $this->employee->guid); ?>,
				descendants: <?php echo $this->descendants ? '"true"' : '"false"'; ?>,
				filter: $(this).attr("data-value")
			});
		});
		<?php if ($_->config->com_calendar->com_customer) { ?>
		$("#p_muid_new_interaction [name=interaction_date]").datepicker({
			dateFormat: "yy-mm-dd",
			changeMonth: true,
			changeYear: true,
			showOtherMonths: true,
			selectOtherMonths: true
		});

		$("#p_muid_customer").customerselect();
		<?php } ?>
	});

	// Change the location / division within the company.
	$_.<?php echo $this->cal_muid; ?>_select_location = function(){
		var descendants = <?php echo $this->descendants ? 'true' : 'false'; ?>;
		$.ajax({
			url: <?php echo json_encode(pines_url('com_calendar', 'locationselect')); ?>,
			type: "POST",
			dataType: "html",
			data: {
				"location": <?php echo json_encode((string) $this->location->guid); ?>,
				"descendants": descendants ? "true" : "false"
			},
			error: function(XMLHttpRequest, textStatus){
				$_.error("An error occured while trying to retrieve the company schedule form:\n"+$_.safe(XMLHttpRequest.status)+": "+$_.safe(textStatus));
			},
			success: function(data){
				if (data == "")
					return;
				$_.pause();
				var form = $("<div title=\"Choose Location\"></div>").html(data+"<br />").dialog({
					bgiframe: true,
					autoOpen: true,
					modal: true,
					close: function(){
						form.remove();
					},
					buttons: {
						"View Schedule": function(){
							form.dialog('close');
							var schedule_location = form.find(":input[name=location]").val();
							descendants = form.find(":input[name=descendants]").attr('checked');
							$_.get(<?php echo json_encode(pines_url('com_calendar', 'editcalendar')); ?>, {
								"view_type": <?php echo json_encode($this->view_type); ?>,
								"start": <?php echo json_encode(format_date($this->date[0], 'date_sort', '', $this->timezone)); ?>,
								"end": <?php echo json_encode(format_date($this->date[1] - 1, 'date_sort', '', $this->timezone)); ?>,
								"location": schedule_location,
								"descendants": descendants ? "true" : "false",
								"filter": <?php echo json_encode($this->filter); ?>
							});
						}
					}
				});
				$_.play();
			}
		});
	};

	// Create a new event.
	$_.<?php echo $this->cal_muid; ?>_new_event = function(start, end, all_day){
		$.ajax({
			url: <?php echo json_encode(pines_url('com_calendar', 'editevent')); ?>,
			type: "POST",
			dataType: "html",
			data: {
				"location": <?php echo json_encode((string) $this->location->guid); ?>,
				"start": start,
				"end": end,
				"all_day": all_day ? "true" : "false",
				"timezone": <?php echo json_encode($this->timezone); ?>
			},
			error: function(XMLHttpRequest, textStatus){
				$_.error("An error occured while trying to retrieve the new event form:\n"+$_.safe(XMLHttpRequest.status)+": "+$_.safe(textStatus));
			},
			success: function(data){
				if (data == "")
					return;
				$_.pause();
				var form = $("<div title=\"Add a New Event\"></div>").html(data+"<br />").dialog({
					bgiframe: true,
					autoOpen: true,
					modal: true,
					width: 550,
					close: function(){
						form.remove();
					},
					buttons: {
						"Add Event": function(){
							$.ajax({
								url: <?php echo json_encode(pines_url('com_calendar', 'saveevent')); ?>,
								type: "POST",
								dataType: "json",
								data: {
									employee: form.find(":input[name=employee]").val(),
									event_label: form.find(":input[name=event_label]").val(),
									information: form.find(":input[name=information]").val(),
									private_event: !!form.find(":input[name=private]").attr('checked'),
									all_day: !!form.find(":input[name=all_day]").attr('checked'),
									start: form.find(":input[name=start]").val(),
									end: form.find(":input[name=end]").val(),
									time_start: form.find(":input[name=time_start]").val(),
									time_end: form.find(":input[name=time_end]").val(),
									location: form.find(":input[name=location]").val(),
									descendants: <?php echo $this->descendants ? '"true"' : '"false"'; ?>,
									employee_view: <?php echo isset($this->employee) ? '"true"' : '"false"'; ?>,
									view_type: <?php echo json_encode($this->view_type); ?>,
									calendar_start: <?php echo json_encode(format_date($this->date[0], 'date_sort', '', $this->timezone)); ?>,
									calendar_end: <?php echo json_encode(format_date($this->date[1] - 1, 'date_sort', '', $this->timezone)); ?>,
									timezone: form.find(":input[name=timezone]").val()
								},
								error: function(){
									$_.error("An error occured while trying to save the event.");
								},
								success: function(data) {
									if (!data.result)
										$_.error("Couldn't save the event.");
									if (data.message)
										alert(data.message);
									form.dialog('close');
									$(<?php echo json_encode("#{$this->cal_muid}_calendar"); ?>).fullCalendar('refetchEvents');
								}
							});
						},
						"Cust. Appt.": function(){
							$("#p_muid_new_interaction [name=interaction_date]").val(form.find(":input[name=start]").val());
							$("#p_muid_new_interaction [name=interaction_time]").val(form.find(":input[name=time_start]").val());
							form.dialog('close');
							$_.<?php echo $this->cal_muid; ?>_new_appointment();
						}
					}
				});
				$_.play();
			}
		});
	};
	// Edit an existing event.
	$_.<?php echo $this->cal_muid; ?>_edit_event = function(event_id){
		$.ajax({
			url: <?php echo json_encode(pines_url('com_calendar', 'editevent')); ?>,
			type: "POST",
			dataType: "html",
			data: {"id": event_id, "timezone": <?php echo json_encode($this->timezone); ?>},
			error: function(XMLHttpRequest, textStatus){
				$_.error("An error occured while trying to retrieve the event form:\n"+$_.safe(XMLHttpRequest.status)+": "+$_.safe(textStatus));
			},
			success: function(data){
				if (data == "")
					return;
				$_.pause();
				var form = $("<div title=\"Editing Event ["+$_.safe(event_id)+"]\"></div>").html(data+"<br />").dialog({
					bgiframe: true,
					autoOpen: true,
					modal: true,
					width: 550,
					close: function(){
						form.remove();
						$_.selected_event.removeClass('ui-state-disabled');
					},
					buttons: {
						"Save Event": function(){
							$.ajax({
								url: <?php echo json_encode(pines_url('com_calendar', 'saveevent')); ?>,
								type: "POST",
								dataType: "json",
								data: {
									id: form.find(":input[name=id]").val(),
									employee: form.find(":input[name=employee]").val(),
									event_label: form.find(":input[name=event_label]").val(),
									information: form.find(":input[name=information]").val(),
									private_event: !!form.find(":input[name=private]").attr('checked'),
									all_day: !!form.find(":input[name=all_day]").attr('checked'),
									start: form.find(":input[name=start]").val(),
									end: form.find(":input[name=end]").val(),
									time_start: form.find(":input[name=time_start]").val(),
									time_end: form.find(":input[name=time_end]").val(),
									location: form.find(":input[name=location]").val(),
									descendants: <?php echo $this->descendants ? '"true"' : '"false"'; ?>,
									employee_view: <?php echo isset($this->employee) ? '"true"' : '"false"'; ?>,
									view_type: <?php echo json_encode($this->view_type); ?>,
									calendar_start: <?php echo json_encode(format_date($this->date[0], 'date_sort', '', $this->timezone)); ?>,
									calendar_end: <?php echo json_encode(format_date($this->date[1] - 1, 'date_sort', '', $this->timezone)); ?>,
									timezone: form.find(":input[name=timezone]").val()
								},
								error: function(){
									$_.error("An error occured while trying to save the event.");
								},
								success: function(data) {
									if (!data.result)
										$_.error("Couldn't save the event.");
									if (data.message)
										alert(data.message);
									form.dialog('close');
									$(<?php echo json_encode("#{$this->cal_muid}_calendar"); ?>).fullCalendar('refetchEvents');
								}
							});
						},
						"Delete Event": function(){
							if (!confirm("Are you sure you want to delete this event?"))
								return;
							$.ajax({
								url: <?php echo json_encode(pines_url('com_calendar', 'deleteevents')); ?>,
								type: "POST",
								dataType: "json",
								data: {"events": Array(form.find(":input[name=id]").val())},
								error: function(){
									$_.error("An error occured while trying to delete the event.");
								},
								success: function(data) {
									if (data)
										$_.error("Couldn't delete "+$_.safe(data));
									$(<?php echo json_encode("#{$this->cal_muid}_calendar"); ?>).fullCalendar('removeEvents', form.find(":input[name=id]").val());
									form.dialog('close');
								}
							});
						}
					}
				});
				$_.play();
			}
		});
	};
	<?php if (gatekeeper('com_calendar/managecalendar')) { ?>
	// Create a quick work schedule for an entire location.
	$_.<?php echo $this->cal_muid; ?>_quick_schedule = function(){
		$.ajax({
			url: <?php echo json_encode(pines_url('com_calendar', 'editlineup')); ?>,
			type: "POST",
			dataType: "html",
			data: {"location": <?php echo json_encode((string) $this->location->guid); ?>},
			error: function(XMLHttpRequest, textStatus){
				$_.error("An error occured while trying to retrieve the quick schedule form:\n"+$_.safe(XMLHttpRequest.status)+": "+$_.safe(textStatus));
			},
			success: function(data){
				if (data == "")
					return;
				$_.pause();
				var form = $("<div title=\"Quick Schedule for <?php e($this->location->name); ?>\"></div>").html(data+"<br />").dialog({
					bgiframe: true,
					autoOpen: true,
					modal: true,
					close: function(){
						form.remove();
					},
					buttons: {
						"Add to Schedule": function(){
							form.dialog('close');
							$_.post(<?php echo json_encode(pines_url('com_calendar', 'savelineup')); ?>, {
								location: form.find(":input[name=location]").val(),
								shifts: form.find(":input[name=shifts]").val()
							});
						}
					}
				});
				$_.play();
				// Have to position again because of datepicker.
				form.dialog("option", "position", "center");
			}
		});
	};
	// Create an employee work schedule.
	$_.<?php echo $this->cal_muid; ?>_new_schedule = function(){
		<?php if (isset($this->employee)) { ?>
		$.ajax({
			url: <?php echo json_encode(pines_url('com_calendar', 'editschedule')); ?>,
			type: "POST",
			dataType: "html",
			data: {"employee": <?php echo json_encode((string) $this->employee->guid); ?>},
			error: function(XMLHttpRequest, textStatus){
				$_.error("An error occured while trying to retrieve the schedule form:\n"+$_.safe(XMLHttpRequest.status)+": "+$_.safe(textStatus));
			},
			success: function(data){
				if (data == "")
					return;
				$_.pause();
				var form = $("<div title=\"Schedule Work for <?php e($this->employee->name); ?>\"></div>").html(data+"<br />").dialog({
					bgiframe: true,
					autoOpen: true,
					modal: true,
					close: function(){
						form.remove();
					},
					buttons: {
						"Add to Schedule": function(){
							form.dialog('close');
							$_.post(<?php echo json_encode(pines_url('com_calendar', 'saveschedule')); ?>, {
								employee: form.find(":input[name=employee]").val(),
								all_day: !!form.find(":input[name=all_day]").attr('checked'),
								time_start_hour: form.find(":input[name=time_start_hour]").val(),
								time_start_minute: form.find(":input[name=time_start_minute]").val(),
								time_start_ampm: form.find(":input[name=time_start_ampm]").val(),
								time_end_hour: form.find(":input[name=time_end_hour]").val(),
								time_end_minute: form.find(":input[name=time_end_minute]").val(),
								time_end_ampm: form.find(":input[name=time_end_ampm]").val(),
								dates: form.find(":input[name=dates]").val()
							});
						}
					}
				});
				$_.play();
				// Have to position again because of datepicker.
				form.dialog("option", "position", "center");
			}
		});
		<?php } else { ?>
		alert('An employee must be selected in order to create a work schedule.');
		<?php } ?>
	};
	<?php } ?>
	<?php if ($_->config->com_calendar->com_customer) { ?>
	// Create an appointment.
	$_.<?php echo $this->cal_muid; ?>_new_appointment = function(){
		var interaction_dialog = $("#p_muid_new_interaction");

		// Interaction Dialog
		interaction_dialog.dialog({
			bgiframe: true,
			autoOpen: false,
			modal: true,
			width: 415,
			buttons: {
				"Create": function(){
					var loader;
					$.ajax({
						url: <?php echo json_encode(pines_url('com_customer', 'interaction/add')); ?>,
						type: "POST",
						dataType: "json",
						data: {
							customer: $("#p_muid_new_interaction [name=customer]").val(),
							employee: $("#p_muid_new_interaction [name=employee]").val(),
							date: $("#p_muid_new_interaction [name=interaction_date]").val(),
							time: $("#p_muid_new_interaction [name=interaction_time]").val(),
							type: $("#p_muid_new_interaction [name=interaction_type]").val(),
							status: $("#p_muid_new_interaction [name=interaction_status]").val(),
							comments: $("#p_muid_new_interaction [name=interaction_comments]").val(),
							timezone: <?php echo json_encode($this->timezone); ?>
						},
						beforeSend: function(){
							loader = new PNotify({
								title: 'Saving',
								text: 'Creating customer interaction...',
								icon: 'picon picon-throbber',
								nonblock: {
									nonblock: true
								},
								hide: false,
								history: {
									history: false
								}
							});
						},
						complete: function(){
							loader.remove();
						},
						error: function(XMLHttpRequest, textStatus){
							$_.error("An error occured:\n"+$_.safe(XMLHttpRequest.status)+": "+$_.safe(textStatus));
						},
						success: function(data){
							if (!data) {
								alert("Could not create the appointment.");
								return;
							} else if (data == 'conflict') {
								alert("This customer already has an appointment during this timeslot.");
								return;
							}
							alert("Successfully created the appointment.");
							$("#p_muid_new_interaction [name=customer]").val('');
							$("#p_muid_new_interaction [name=interaction_comments]").val('');
							interaction_dialog.dialog("close");
							window.location.reload();
						}
					});
				}
			}
		});

		interaction_dialog.dialog('open');
	};

	// Edit an appointment.
	$_.<?php echo $this->cal_muid; ?>_edit_appointment = function(appointment_id){
		var interaction_dialog = $("#p_muid_interaction_dialog");

		// Interaction Dialog
		interaction_dialog.dialog({
			bgiframe: true,
			autoOpen: false,
			modal: true,
			width: 400,
			close: function(){
				$_.selected_event.removeClass('ui-state-disabled');
			},
			buttons: {
				"Update": function(){
					var loader;
					$.ajax({
						url: <?php echo json_encode(pines_url('com_customer', 'interaction/process')); ?>,
						type: "POST",
						dataType: "json",
						data: {
							id: $("#p_muid_interaction_dialog [name=id]").val(),
							status: $("#p_muid_interaction_dialog [name=status]").val(),
							review_comments: $("#p_muid_interaction_dialog [name=review_comments]").val()
						},
						beforeSend: function(){
							loader = new PNotify({
								title: 'Updating',
								text: 'Processing customer interaction...',
								icon: 'picon picon-throbber',
								nonblock: {
									nonblock: true
								},
								hide: false,
								history: {
									history: false
								}
							});
						},
						complete: function(){
							loader.remove();
						},
						error: function(XMLHttpRequest, textStatus){
							$_.error("An error occured:\n"+$_.safe(XMLHttpRequest.status)+": "+$_.safe(textStatus));
						},
						success: function(data){
							if (!data) {
								alert("Could not update the interaction. Do you have permission?");
								return;
							} else if (data == 'closed') {
								alert("This interaction is already closed.");
								return;
							} else if (data == 'comments') {
								alert("You must provide information in the comments section.");
								return;
							}
							alert("Successfully updated the interaction.");
							$("#p_muid_interaction_dialog [name=review_comments]").val('');
							interaction_dialog.dialog("close");
							$_.selected_event.removeClass('ui-state-disabled');
							window.location.reload();
						}
					});
				}
			}
		});
		$.ajax({
			url: <?php echo json_encode(pines_url('com_customer', 'interaction/info')); ?>,
			type: "POST",
			dataType: "json",
			data: {id: appointment_id, timezone: <?php echo json_encode($this->timezone); ?>},
			error: function(XMLHttpRequest, textStatus){
				$_.error("An error occured:\n"+$_.safe(XMLHttpRequest.status)+": "+$_.safe(textStatus));
			},
			success: function(data){
				if (!data) {
					alert("No appointment was found matching the selected item.");
					return;
				}
				$("#p_muid_interaction_dialog [name=id]").val(appointment_id);
				$("#p_muid_interaction_customer").empty().append('<a data-entity="'+$_.safe(data.customer_guid)+'" data-entity-context="com_customer_customer">'+$_.safe(data.customer)+'</a>');
				if (data.sale_url != '') {
					$("#p_muid_interaction_sale").empty().append('<a data-entity="'+$_.safe(data.sale_guid)+'" data-entity-context="com_sales_sale">'+$_.safe(data.sale)+'</a>');
					$("#p_muid_sale_info").show();
				} else
					$("#p_muid_sale_info").hide();
				$("#p_muid_interaction_type").empty().append($_.safe(data.type)+' - '+$_.safe(data.contact_info));
				$("#p_muid_interaction_employee").empty().append($_.safe(data.employee));
				$("#p_muid_interaction_created_date").empty().append($_.safe(data.created_date));
				$("#p_muid_interaction_date").empty().append($_.safe(data.date));
				$("#p_muid_interaction_comments").empty().append($_.safe(data.comments));
				$("#p_muid_interaction_notes").empty().append((data.review_comments.length > 0) ? "<li>"+$.map(data.review_comments, $_.safe).join("</li><li>")+"</li>" : "");
				$("#p_muid_interaction_dialog [name=status]").val(data.status);

				interaction_dialog.dialog('open');
				$("#p_muid_interaction_dialog [name=review_comments]").focus();
			}
		});
	};
	<?php } ?>
</script>
<?php if (!$this->is_widget) { ?>
<div id="p_muid_filter" class="btn-group" style="padding: 0; margin: 0;">
	<button class="btn btn-default btn-xs<?php echo ($this->filter == 'all') ? ' active' : ''; ?>" type="button" data-value="all" title="Show all calendar events.">all</button>
	<button class="btn btn-default btn-xs<?php echo ($this->filter == 'shifts') ? ' active' : ''; ?>" type="button" data-value="shifts" title="Show scheduled employee shifts.">shifts</button>
	<button class="btn btn-default btn-xs<?php echo ($this->filter == 'appointments') ? ' active' : ''; ?>" type="button" data-value="appointments" title="Show customer appointments.">appts</button>
	<button class="btn btn-default btn-xs<?php echo ($this->filter == 'events') ? ' active' : ''; ?>" type="button" data-value="events" title="Show calendar events.">events</button>
</div>
<?php } if (!$this->is_widget || gatekeeper('com_calendar/managecalendar') || gatekeeper('com_calendar/editcalendar')) { ?>
<div style="margin: 0.75em 0;" id="p_muid_actions">
	<?php if (!$this->is_widget) { ?>
	<button class="btn btn-default btn-xs" type="button" onclick="$_.<?php echo $this->cal_muid; ?>_select_location();" title="Select Location"><span class="p_muid_btn picon picon-applications-internet"></span></button>
	<?php } if (gatekeeper('com_calendar/managecalendar') || gatekeeper('com_calendar/editcalendar')) { ?>
	<button class="btn btn-default btn-xs" type="button" onclick="$_.<?php echo $this->cal_muid; ?>_new_appointment();" title="New Appointment"><span class="p_muid_btn picon picon-appointment-new"></span></button>
	<button class="btn btn-default btn-xs" type="button" onclick="$_.<?php echo $this->cal_muid; ?>_new_event();" title="New Event"><span class="p_muid_btn picon picon-resource-calendar-insert"></span></button>
	<?php if (gatekeeper('com_calendar/managecalendar')) { ?>
	<button class="btn btn-default btn-xs" type="button" onclick="$_.<?php echo $this->cal_muid; ?>_quick_schedule();" title="Quick Schedule"><span class="p_muid_btn picon picon-view-calendar-workweek"></span></button>
	<button class="btn btn-default btn-xs" type="button" onclick="$_.<?php echo $this->cal_muid; ?>_new_schedule();" title="Personal Schedule" <?php echo !isset($this->employee) ? 'disabled="disabled"' : '';?>><span class="p_muid_btn picon picon-list-resource-add"></span></button>
	<?php }
	} ?>
</div>
<?php } if (!$this->is_widget) { ?>
<div>
	<select id="p_muid_employee" name="employee" style="width: 100%;">
		<option value="all"><?php e($this->location->name); ?></option>
		<?php
		// Load employees for this location.
		foreach ($this->employees as $cur_employee) {
			if (!isset($cur_employee->group))
				continue;
			$cur_select = (isset($this->employee->group) && $this->employee->is($cur_employee)) ? 'selected="selected"' : '';
			if ( $this->location->guid == $cur_employee->group->guid || ($this->descendants && $cur_employee->is_descendant($this->location)) )
				echo '<option value="'.h($cur_employee->guid).'" '.$cur_select.'>'.h($cur_employee->name).'</option>';
		} ?>
	</select>
</div>
<?php } if ($_->config->com_calendar->com_customer) { ?>
<div id="p_muid_interaction_dialog" title="Process Customer Interaction" style="display: none;">
	<div class="pf-form">
		<div class="pf-element">
			<span class="pf-label">Customer</span>
			<span class="pf-field" id="p_muid_interaction_customer"></span>
		</div>
		<div class="pf-element" id="p_muid_sale_info" style="display: none">
			<span class="pf-label">Sale</span>
			<span class="pf-field" id="p_muid_interaction_sale"></span>
		</div>
		<div class="pf-element">
			<span class="pf-label">Employee</span>
			<span class="pf-field" id="p_muid_interaction_employee"></span>
		</div>
		<div class="pf-element">
			<span class="pf-label">Interaction Type</span>
			<span class="pf-field" id="p_muid_interaction_type"></span>
		</div>
		<div class="pf-element">
			<span class="pf-label">Created</span>
			<span class="pf-field" id="p_muid_interaction_created_date"></span>
		</div>
		<div class="pf-element">
			<span class="pf-label">Date</span>
			<span class="pf-field" id="p_muid_interaction_date"></span>
		</div>
		<div class="pf-element pf-full-width">
			<span class="pf-full-width" id="p_muid_interaction_comments"></span>
			<div class="pf-full-width">
				<ul id="p_muid_interaction_notes"></ul>
			</div>
		</div>
		<div class="pf-element pf-full-width">
			<textarea rows="3" cols="40" name="review_comments"></textarea>
		</div>
		<div class="pf-element">
			<label>
				<span class="pf-label">Status</span>
				<select name="status">
					<option value="open">Open</option>
					<option value="closed">Closed</option>
					<option value="canceled">Canceled</option>
				</select>
			</label>
		</div>
		<input type="hidden" name="id" value="" />
	</div>
	<br class="pf-clearing" />
</div>
<div id="p_muid_new_interaction" title="Create a Customer Appointment" style="display: none;">
	<div class="pf-form calendar_form">
		<div class="pf-element">
			<label><span class="pf-label">Customer</span>
				<input type="text" id="p_muid_customer" name="customer" size="22" value="" /></label>
		</div>
		<?php if (gatekeeper('com_customer/manageinteractions')) { ?>
		<div class="pf-element">
			<label><span class="pf-label">Employee</span>
				<select name="employee">
				<?php foreach ($this->employees as $cur_employee) {
					$selected = $_SESSION['user']->is($cur_employee) ? ' selected="selected"' : '';
					if ($cur_employee->in_group($this->location) || ($this->descendants && $cur_employee->is_descendant($this->location)))
						echo '<option value="'.h($cur_employee->guid).'"'.$selected.'>'.h($cur_employee->name).'</option>"';
				} ?>
			</select></label>
		</div>
		<?php } else { ?>
		<div class="pf-element">
			<label><span class="pf-label">Employee</span>
				<?php e($_SESSION['user']->name); ?></label>
		</div>
		<input type="hidden" name="employee" value="<?php e($_SESSION['user']->guid); ?>" />
		<?php } ?>
		<div class="pf-element">
			<label><span class="pf-label">Interaction Type</span>
				<select name="interaction_type">
					<?php foreach ($_->config->com_customer->interaction_types as $cur_type) {
						$cur_type = explode(':', $cur_type);
						echo '<option value="'.h($cur_type[1]).'">'.h($cur_type[1]).'</option>';
					} ?>
				</select></label>
		</div>
		<div class="pf-element">
			<label><span class="pf-label">Date</span>
				<input type="text" size="22" name="interaction_date" value="<?php e(format_date(time(), 'date_sort')); ?>" /></label>
		</div>
		<div class="pf-element pf-full-width">
			<span class="pf-label">Time</span>
			<span class="combobox">
				<input type="text" name="interaction_time" size="20" value="" />
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
		</div>
		<div class="pf-element">
			<label>
				<span class="pf-label">Status</span>
				<select name="interaction_status">
					<option value="open">Open</option>
					<option value="closed">Closed</option>
				</select>
			</label>
		</div>
		<div class="pf-element pf-full-width">
			<textarea rows="3" cols="40" name="interaction_comments"></textarea>
		</div>
	</div>
	<br class="pf-clearing" />
</div>
<?php }