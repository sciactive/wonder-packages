<?php
/**
 * Shows an employees timeclock history.
 *
 * @package Pines
 * @subpackage com_reports
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Zak Huber <zakhuber@gmail.com>
 * @copyright Zak Huber
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');
$group = group::factory((int)$this->location);
if (!$group->guid)
	unset($group);
$this->title = 'Employee Attendance: '.($this->employee ? $this->employee->name : $group->name).' ('.pines_date_format($this->date[0],null,'Y-m-d').' - '.pines_date_format($this->date[1],null,'Y-m-d').')';
?>
<style type="text/css" >
	/* <![CDATA[ */
	#timeclock_grid tr.total td {
		background-color: #FFFCCC;
		font-weight: bold;
	}
	/* ]]> */
</style>
<script type="text/javascript">
	// <![CDATA[
	$(function(){
		var state_xhr;
		var cur_state = JSON.parse("<?php echo (isset($this->pgrid_state) ? addslashes($this->pgrid_state) : '{}');?>");
		var cur_defaults = {
			<?php if (isset($this->employees)) { ?>
			pgrid_toolbar: true,
			pgrid_toolbar_contents: [
				{type: 'button', text: 'View', extra_class: 'icon picon_16x16_apps_user-info', double_click: true, url: '<?php echo pines_url('com_reports', 'reportattendance', array('user' => '#title#', 'start' => pines_date_format($this->date[0], null, 'Y-m-d'), 'end' => pines_date_format($this->date[1], null, 'Y-m-d'), 'location' => $this->location), false); ?>'},
				{type: 'separator'},
				{type: 'button', text: 'Spreadsheet', extra_class: 'icon picon_16x16_mimetypes_x-office-spreadsheet', multi_select: true, pass_csv_with_headers: true, click: function(e, rows){
					pines.post("<?php echo pines_url('system', 'csv'); ?>", {
						filename: 'time_attendance',
						content: rows
					});
				}}
			],
			<?php } else { ?>
			pgrid_toolbar: false,
			<?php } ?>
			pgrid_sortable: false,
			pgrid_state_change: function(state) {
				if (typeof state_xhr == "object")
					state_xhr.abort();
				cur_state = JSON.stringify(state);
				state_xhr = $.post("<?php echo pines_url('com_pgrid', 'save_state'); ?>", {view: "com_reports/report_attendance", state: cur_state});
			}
		};
		var cur_options = $.extend(cur_defaults, cur_state);
		cur_options.pgrid_sort_col = false;
		$("#timeclock_grid").pgrid(cur_options);
	});
	// ]]>
</script>
<?php if (isset($this->employees)) { ?>
<table id="timeclock_grid">
	<thead>
		<tr>
			<th>Employee</th>
			<th>Scheduled</th>
			<th>Clocked</th>
			<th>Variance</th>
		</tr>
	</thead>
	<tbody>
	<?php
		$clocked_in = 0;
		$total_count = 0;
		$totals = array();
		$all_totals['scheduled'] = 0;
		$all_totals['clocked'] = 0;
		foreach($this->employees as $cur_employee) {
			$totals[$total_count]['scheduled'] = 0;
			$totals[$total_count]['clocked'] = 0;
			foreach($cur_employee->timeclock as $clock) {
				if ($clock['time'] >= $this->date[0] && $clock['time'] <= $this->date[1]) {
					if ($clock['status'] == 'out' && ($clocked_in > 0)) {
						$totals[$total_count]['clocked'] += ($clock['time'] - $clocked_in);
						$clocked_in = 0;
					} else if ($clock['status'] == 'in') {
						$clocked_in = $clock['time'];
					}
				}
			} ?>
			<tr title="<?php echo $cur_employee->guid; ?>">
				<td><?php echo $cur_employee->name; ?></td>
				<td><?php echo round($totals[$total_count]['scheduled']/(60*60),2); ?></td>
				<td><?php echo round($totals[$total_count]['clocked']/(60*60),2); ?> hours</td>
				<td><?php echo round(($totals[$total_count]['clocked'] - $totals[$total_count]['scheduled'])/(60*60),2); ?> hours</td>
			</tr>
	<?php $all_totals['scheduled'] += 0; $all_totals['clocked'] += $totals[$total_count]['clocked']; $total_count++; } ?>
		<tr class="total">
			<td>Total</td>
			<td><?php echo round($all_totals['scheduled']/(60*60),2); ?></td>
			<td><?php echo round($all_totals['clocked']/(60*60),2); ?> hours</td>
			<td><?php echo round(($all_totals['clocked'] - $all_totals['scheduled'])/(60*60),2); ?> hours</td>
		</tr>
	</tbody>
</table>
<?php } else if (isset($this->employee)) { ?>
<div style="float: right; margin-bottom: 5px;">
	<form method="post" action="<?php echo pines_url('com_reports', 'reportattendance'); ?>">
		<input type="hidden" name="location" value="<?php echo $this->location; ?>" />
		<input type="hidden" name="start" value="<?php echo pines_date_format($this->date[0], null, 'Y-m-d'); ?>" />
		<input type="hidden" name="end" value="<?php echo pines_date_format($this->date[1], null, 'Y-m-d'); ?>" />
		<button type="submit" class="ui-corner-all ui-state-default">&laquo; Back To All Employees <span class="picon_16x16_apps_system-users" style="height: 16px; width: 16px; display: inline-block; margin: 2px 2px 0 2px;"></span></button>
	</form>
</div>
<br class="spacer" />
<table id="timeclock_grid">
	<thead>
		<tr>
			<th>Local Time</th>
			<th>Location</th>
			<th>Time</th>
			<th>In</th>
			<th>Out</th>
			<th>Total</th>
		</tr>
	</thead>
	<tbody>
	<?php
		$clocks = array();
		$dates = array();
		$clock_count = 0;
		$date_count = 0;
		foreach($this->employee->timeclock as $key => $entry) {
			if ($entry['time'] >= $this->date[0] && $entry['time'] <= $this->date[1]) {
				if ($dates[$date_count]['date'] == pines_date_format($entry['time'], null, 'Y-m-d')) {
					if ($entry['status'] == 'out') {
						$clocks[$clock_count]['out'] = $entry['time'];
						$dates[$date_count]['total'] += $this->employee->time_sum($clocks[$clock_count]['in'], $entry['time']);
					} else {
						$clock_count++;
						$clocks[$clock_count]['date'] = $date_count;
						$clocks[$clock_count]['in'] = $entry['time'];
					}
				} else {
					$clock_count++;
					$date_count++;
					$dates[$date_count]['date'] = pines_date_format($entry['time'], null, 'Y-m-d');
					$dates[$date_count]['total'] = 0;
					$clocks[$clock_count]['date'] = $date_count;
					$clocks[$clock_count]['in'] = $entry['time'];
				}
			}
		}
		$counter = 1;
		foreach ($dates as $cur_date) { ?>
		<tr>
			<td><?php echo $cur_date['date']; ?></td>
			<td><?php echo $group->name; ?></td>
			<td>Scheduled</td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
			<?php foreach ($clocks as $cur_clock) { if ($cur_clock['date'] == $counter) { ?>
				<tr>
					<td></td>
					<td></td>
					<td>Clocked</td>
					<td><?php echo pines_date_format($cur_clock['in'], null, 'g:i a'); ?></td>
					<td><?php echo pines_date_format($cur_clock['out'], null, 'g:i a'); ?></td>
					<td></td>
				</tr>
			<?php } }
			$total_hours = floor($cur_date['total']/(60*60));
			$total_mins = round(($cur_date['total']/(60))-($total_hours*60)); ?>
		<tr class="total">
			<td></td>
			<td></td>
			<td>Total</td>
			<td></td>
			<td></td>
			<td><?php echo ($total_hours > 0) ? $total_hours.'hours ' : ''; echo ($total_mins > 0) ? $total_mins.'min' : ''; ?></td>
		</tr>
		<?php $counter++; } ?>
	</tbody>
</table>
<?php } ?>