<?php
/**
 * Provides a form for the user to edit a transfer.
 *
 * @package Pines
 * @subpackage com_sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');
$this->title = (!isset($this->entity->guid)) ? 'Editing New Transfer' : (($this->entity->final) ? 'Viewing ' : 'Editing ').' Transfer ['.htmlentities($this->entity->guid).']';
$this->note = 'Use this form to transfer inventory to another location.';
$pines->com_pgrid->load();
$read_only = '';
if ($this->entity->final)
	$read_only = 'readonly="readonly"';
?>
<form class="pf-form" method="post" id="transfer_details" action="<?php echo htmlentities(pines_url('com_sales', 'transfer/save')); ?>">
	<script type="text/javascript">
		// <![CDATA[
		var stock;
		var stock_table;
		var available_stock_table;
		var stock_dialog;
		var available_stock = <?php
		$all_stock = array();
		foreach ($this->stock as $stock) {
			if ($stock->in_array($this->entity->stock))
				continue;
			$export_stock = array(
				'key' => $stock->guid,
				'values' => array(
					(string) $stock->product->sku,
					(string) $stock->product->name,
					(string) $stock->serial,
					(string) $stock->vendor->name,
					(string) "{$stock->location->name} [{$stock->location->groupname}]",
					(string) $stock->cost,
					(string) $stock->status
				)
				
			);
			$all_stock[] = $export_stock;
		}
		echo json_encode($all_stock);
		?>;

		function update_stock() {
			var all_rows = stock_table.pgrid_get_all_rows().pgrid_export_rows();
			available_stock_table.pgrid_get_selected_rows().pgrid_deselect_rows();
			// Save the data into a hidden form element.
			stock.val(JSON.stringify(all_rows));
		}
		
		pines(function(){
			stock = $("#stock");
			stock_table = $("#stock_table");
			available_stock_table = $("#available_stock_table");
			stock_dialog = $("#stock_dialog");

			<?php if (!$this->entity->final && empty($this->entity->received)) { ?>
			stock_table.pgrid({
				pgrid_paginate: false,
				pgrid_view_height: "300px",
				pgrid_toolbar: true,
				pgrid_toolbar_contents : [
					{
						type: 'button',
						text: 'Add',
						extra_class: 'picon picon-document-new',
						selection_optional: true,
						click: function(){
							stock_dialog.dialog('open');
						}
					},
					{
						type: 'button',
						text: 'Remove',
						extra_class: 'picon picon-edit-delete',
						click: function(e, rows){
							available_stock_table.pgrid_add(rows.pgrid_export_rows());
							rows.pgrid_delete();
							update_stock();
						}
					}
				]
			});
			<?php } else { ?>
			stock_table.pgrid({
				pgrid_paginate: false
			});
			<?php } ?>
			// Needs to be gridified before it's hidden.
			available_stock_table.pgrid({
				pgrid_paginate: false,
				pgrid_height: '400px;'
			}).pgrid_get_all_rows().pgrid_delete();
			available_stock_table.pgrid_add(available_stock);

			// Stock Dialog
			stock_dialog.dialog({
				bgiframe: true,
				autoOpen: false,
				modal: true,
				width: 600,
				buttons: {
					"Done": function() {
						var cur_stock_rows = available_stock_table.pgrid_get_selected_rows();
						var cur_stock = cur_stock_rows.pgrid_export_rows();
						if (!cur_stock[0]) {
							alert("Please select stock.");
							return;
						}
						stock_table.pgrid_add(cur_stock);
						cur_stock_rows.pgrid_delete();
						$(this).dialog('close');
					}
				},
				close: function(event, ui) {
					update_stock();
				}
			});
			
			update_stock();
		});
		// ]]>
	</script>
	<?php if (isset($this->entity->guid)) { ?>
	<div class="date_info" style="float: right; text-align: right;">
		<?php if (isset($this->entity->user)) { ?>
		<div>User: <span class="date"><?php echo "{$this->entity->user->name} [{$this->entity->user->username}]"; ?></span></div>
		<div>Group: <span class="date"><?php echo "{$this->entity->group->name} [{$this->entity->group->groupname}]"; ?></span></div>
		<?php } ?>
		<div>Created: <span class="date"><?php echo format_date($this->entity->p_cdate, 'full_short'); ?></span></div>
		<div>Modified: <span class="date"><?php echo format_date($this->entity->p_mdate, 'full_short'); ?></span></div>
	</div>
	<?php } ?>
	<div class="pf-element">
		<span class="pf-label">Status</span>
		<span class="pf-field">
			<?php echo ($this->entity->final) ? 'Committed' : 'Not Committed'; ?>, <?php echo ($this->entity->finished) ? 'Received' : (empty($this->entity->received) ? 'Not Received' : 'Partially Received'); ?>
		</span>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Reference #</span>
			<input class="pf-field ui-widget-content" type="text" name="reference_number" size="24" value="<?php echo $this->entity->reference_number; ?>" <?php echo $read_only; ?> /></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Destination</span>
			<?php if (!empty($this->entity->received)) { ?>
				<span class="pf-note">Destination cannot be changed after items have been received.</span>
			<?php } ?>
			<select class="pf-field ui-widget-content" name="destination"<?php echo (empty($this->entity->received) ? '' : ' disabled="disabled"'); ?> <?php echo $read_only; ?>>
				<?php echo $pines->user_manager->get_group_tree('<option value="#guid#"#selected#>#mark##name# [#groupname#]</option>', $this->locations, $this->entity->destination->guid); ?>
			</select></label>
	</div>
	<div class="pf-element">
		<label><span class="pf-label">Shipper</span>
			<select class="pf-field ui-widget-content" name="shipper" <?php echo $read_only; ?>>
				<option value="null">-- None --</option>
				<?php foreach ($this->shippers as $cur_shipper) { ?>
				<option value="<?php echo $cur_shipper->guid; ?>"<?php echo $this->entity->shipper->guid == $cur_shipper->guid ? ' selected="selected"' : ''; ?>><?php echo $cur_shipper->name; ?></option>
				<?php } ?>
			</select></label>
	</div>
	<div class="pf-element">
		<?php if (!$this->entity->final) { ?>
		<script type="text/javascript">
			// <![CDATA[
			pines(function(){
				$("#eta").datepicker({
					dateFormat: "yy-mm-dd",
					showOtherMonths: true,
					selectOtherMonths: true
				});
			});
			// ]]>
		</script>
		<?php } ?>
		<label><span class="pf-label">ETA</span>
			<input class="pf-field ui-widget-content" type="text" id="eta" name="eta" size="24" value="<?php echo ($this->entity->eta ? date('Y-m-d', $this->entity->eta) : ''); ?>" <?php echo $read_only; ?> /></label>
	</div>
	<div class="pf-element pf-full-width">
		<span class="pf-label">Stock</span>
		<div class="pf-group">
			<div class="pf-field">
				<table id="stock_table">
					<thead>
						<tr>
							<th>SKU</th>
							<th>Product</th>
							<th>Serial</th>
							<th>Vendor</th>
							<th>Location</th>
							<th>Cost</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($this->entity->stock as $cur_stock) {
								if (!isset($cur_stock->guid))
									continue;
								if (isset($missing_products[$cur_stock->product->guid])) {
									$missing_products[$cur_stock->product->guid]['quantity']++;
								} else {
									$missing_products[$cur_stock->product->guid] = array('entity' => $cur_stock->product, 'quantity' => 1);
								}
								?>
						<tr title="<?php echo $cur_stock->guid; ?>">
							<td><?php echo $cur_stock->product->sku; ?></td>
							<td><?php echo $cur_stock->product->name; ?></td>
							<td><?php echo $cur_stock->serial; ?></td>
							<td><?php echo $cur_stock->vendor->name; ?></td>
							<td><?php echo "{$cur_stock->location->name} [{$cur_stock->location->groupname}]"; ?></td>
							<td><?php echo $cur_stock->cost; ?></td>
							<td><?php echo $cur_stock->status; ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<input type="hidden" id="stock" name="stock" size="24" />
		</div>
	</div>
	<div id="stock_dialog" title="Add Stock" style="display: none;">
		<table id="available_stock_table">
			<thead>
				<tr>
					<th>SKU</th>
					<th>Product</th>
					<th>Serial</th>
					<th>Vendor</th>
					<th>Location</th>
					<th>Cost</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<tr><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>
			</tbody>
		</table>
		<br class="pf-clearing" />
	</div>
	<?php if (!empty($this->entity->received)) { ?>
		<div class="pf-element pf-full-width">
			<span class="pf-label">Received Inventory</span>
			<?php
			$received = array();
			foreach ($this->entity->received as $cur_entity) {
				if (!isset($received[$cur_entity->product->guid]))
					$received[$cur_entity->product->guid] = array('entity' => $cur_entity->product, 'serials' => array());
				if (isset($missing_products[$cur_entity->product->guid])) {
					$missing_products[$cur_entity->product->guid]['quantity']--;
					if (!$missing_products[$cur_entity->product->guid]['quantity'])
						unset($missing_products[$cur_entity->product->guid]);
				}
				$received[$cur_entity->product->guid]['serials'][] = isset($cur_entity->serial) ? $cur_entity->serial : '';
			}
			?>
			<?php foreach ($received as $cur_entry) { ?>
			<div class="pf-field pf-full-width ui-widget-content ui-corner-all" style="margin-bottom: 5px; padding: .5em;">
				SKU: <?php echo $cur_entry['entity']->sku; ?><br />
				Product: <?php echo $cur_entry['entity']->name; ?><br />
				Quantity: <?php echo count($cur_entry['serials']); ?>
				<?php if ($cur_entry['entity']->serialized) { ?>
				<br />
				Serials: <?php echo implode(', ', $cur_entry['serials']); ?>
				<?php } ?>
			</div>
			<?php } ?>
		</div>
		<?php if (!empty($missing_products)) { ?>
		<div class="pf-element pf-full-width">
			<span class="pf-label">Missing Inventory</span>
			<?php foreach ($missing_products as $cur_entry) { ?>
			<div class="pf-field pf-full-width ui-widget-content ui-corner-all" style="margin-bottom: 5px; padding: .5em;">
				SKU: <?php echo $cur_entry['entity']->sku; ?><br />
				Product: <?php echo $cur_entry['entity']->name; ?><br />
				Quantity: <?php echo $cur_entry['quantity']; ?>
			</div>
			<?php } ?>
		</div>
		<?php } ?>
	<?php } ?>
	<br class="pf-clearing" />
	<div class="pf-element pf-buttons">
		<?php if ( isset($this->entity->guid) ) { ?>
		<input type="hidden" name="id" value="<?php echo $this->entity->guid; ?>" />
		<?php } if (!$this->entity->final) { ?>
		<input type="hidden" id="save" name="save" value="" />
		<input class="pf-button ui-state-default ui-priority-primary ui-corner-all" type="submit" name="submit" value="Save" onclick="$('#save').val('save');" />
		<input class="pf-button ui-state-default ui-priority-primary ui-corner-all" type="submit" name="submit" value="Commit" onclick="$('#save').val('commit');" />
		<input class="pf-button ui-state-default ui-priority-secondary ui-corner-all" type="button" onclick="pines.get('<?php echo htmlentities(pines_url('com_sales', 'transfer/list')); ?>');" value="Cancel" />
		<?php } ?>
	</div>
</form>