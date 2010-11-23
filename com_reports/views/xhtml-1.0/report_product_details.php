<?php
/**
 * Shows a product details report.
 *
 * @package Pines
 * @subpackage com_reports
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

$this->title = 'Product Details Report ['.$this->location->name.']';
if (!$this->all_time)
	$this->note = format_date($this->start_date, 'date_short').' - '.format_date($this->end_date, 'date_short');

$pines->icons->load();
$pines->com_jstree->load();
$pines->com_pgrid->load();
if (isset($_SESSION['user']) && is_array($_SESSION['user']->pgrid_saved_states))
	$this->pgrid_state = $_SESSION['user']->pgrid_saved_states['com_reports/report_issues'];
?>
<style type="text/css" >
	/* <![CDATA[ */
	.p_muid_return td {
		color: red;
	}
	/* ]]> */
</style>
<script type="text/javascript">
	// <![CDATA[
	pines(function(){
		pines.search_details = function(){
			// Submit the form with all of the fields.
			pines.post("<?php echo addslashes(pines_url('com_reports', 'reportproducts')); ?>", {
				"location": location,
				"all_time": all_time,
				"start_date": start_date,
				"end_date": end_date
			});
		};

		// Timespan Defaults
		var all_time = <?php echo $this->all_time ? 'true' : 'false'; ?>;
		var start_date = "<?php echo $this->start_date ? addslashes(format_date($this->start_date, 'date_sort')) : ''; ?>";
		var end_date = "<?php echo $this->end_date ? addslashes(format_date($this->end_date, 'date_sort')) : ''; ?>";
		// Location Defaults
		var location = "<?php echo $this->location->guid; ?>";

		var cur_defaults = {
			pgrid_toolbar: true,
			pgrid_toolbar_contents: [
				{type: 'button', text: 'Location', extra_class: 'picon picon-applications-internet', selection_optional: true, click: function(){details_grid.location_form();}},
				{type: 'button', text: 'Timespan', extra_class: 'picon picon-view-time-schedule', selection_optional: true, click: function(){details_grid.date_form();}},
				{type: 'separator'},
				{type: 'button', title: 'Select All', extra_class: 'picon picon-document-multiple', select_all: true},
				{type: 'button', title: 'Select None', extra_class: 'picon picon-document-close', select_none: true},
				{type: 'separator'},
				{type: 'button', title: 'Make a Spreadsheet', extra_class: 'picon picon-x-office-spreadsheet', multi_select: true, pass_csv_with_headers: true, click: function(e, rows){
					pines.post("<?php echo addslashes(pines_url('system', 'csv')); ?>", {
						filename: 'product_details',
						content: rows
					});
				}}
			],
			pgrid_sortable: true,
			pgrid_sort_col: 2,
			pgrid_sort_ord: "desc"
		};
		var cur_options = $.extend(cur_defaults);
		var details_grid = $("#p_muid_grid").pgrid(cur_options);

		details_grid.date_form = function(){
			$.ajax({
				url: "<?php echo addslashes(pines_url('com_reports', 'dateselect')); ?>",
				type: "POST",
				dataType: "html",
				data: {"all_time": all_time, "start_date": start_date, "end_date": end_date},
				error: function(XMLHttpRequest, textStatus){
					pines.error("An error occured while trying to retreive the date form:\n"+XMLHttpRequest.status+": "+textStatus);
				},
				success: function(data){
					if (data == "")
						return;
					var form = $("<div title=\"Date Selector\" />");
					form.dialog({
						bgiframe: true,
						autoOpen: true,
						height: 315,
						modal: true,
						open: function(){
							form.html(data);
						},
						close: function(){
							form.remove();
						},
						buttons: {
							"Done": function(){
								if (form.find(":input[name=timespan_saver]").val() == "alltime") {
									all_time = true;
								} else {
									all_time = false;
									start_date = form.find(":input[name=start_date]").val();
									end_date = form.find(":input[name=end_date]").val();
								}
								form.dialog('close');
								pines.search_details();
							}
						}
					});
				}
			});
		};
		details_grid.location_form = function(){
			$.ajax({
				url: "<?php echo addslashes(pines_url('com_reports', 'locationselect')); ?>",
				type: "POST",
				dataType: "html",
				data: {"location": location},
				error: function(XMLHttpRequest, textStatus){
					pines.error("An error occured while trying to retreive the location form:\n"+XMLHttpRequest.status+": "+textStatus);
				},
				success: function(data){
					if (data == "")
						return;
					var form = $("<div title=\"Location Selector\" />");
					form.dialog({
						bgiframe: true,
						autoOpen: true,
						height: 250,
						modal: true,
						open: function(){
							form.html(data);
						},
						close: function(){
							form.remove();
						},
						buttons: {
							"Done": function(){
								location = form.find(":input[name=location]").val();
								form.dialog('close');
								pines.search_details();
							}
						}
					});
				}
			});
		};
	});
	// ]]>
</script>
<div class="pf-element pf-full-width">
	<table id="p_muid_grid">
		<thead>
			<tr>
				<th>ID</th>
				<th>Date</th>
				<th>Transaction</th>
				<th>Delivery</th>
				<th>Location</th>
				<th>Employee</th>
				<th>Customer</th>
				<th>SKU</th>
				<th>Serial</th>
				<th>Product</th>
				<th>Unit Cost</th>
				<th>Price</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($this->transactions as $cur_tx) {
				if (empty($cur_tx->products))
					continue;
				if ($cur_tx->has_tag('return')) {
					$class = 'class="p_muid_return"';
					$tx_type = 'RETURN';
				} else {
					$class = '';
					if ($cur_tx->status == 'voided')
						$tx_type = 'VOID';
					elseif ($cur_tx->status == 'invoiced')
						$tx_type = 'INVOICE';
					else
						$tx_type = 'SALE';
				}
				foreach ($cur_tx->products as $cur_item) { ?>
				<tr <?php echo $class; ?>>
					<td><?php echo $tx_type.$cur_tx->id; ?></td>
					<td><?php echo format_date($cur_tx->p_cdate); ?></td>
					<td><?php echo $tx_type; //htmlspecialchars($cur_tx->status); ?></td>
					<td><?php echo htmlspecialchars($cur_item['delivery']); ?></td>
					<td><?php echo htmlspecialchars($cur_tx->group->name); ?></td>
					<td><?php echo htmlspecialchars($cur_tx->user->name); ?></td>
					<td><?php echo htmlspecialchars($cur_tx->customer->name); ?></td>
					<td><?php echo htmlspecialchars($cur_item['sku']); ?></td>
					<td><?php echo htmlspecialchars($cur_item['serial']); ?></td>
					<td><?php echo htmlspecialchars($cur_item['entity']->name); ?></td>
					<td>$<?php echo round($cur_item['entity']->vendors[0]['cost'], 2); ?></td>
					<td>$<?php echo round($cur_item['price'], 2); ?></td>
				</tr>
			<?php }
			} ?>
		</tbody>
	</table>
</div>