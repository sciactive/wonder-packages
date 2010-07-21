<?php
/**
 * Shows a quote, invoice, or receipt for a sale or return.
 *
 * @package Pines
 * @subpackage com_sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

$sale = $this->entity->has_tag('sale');

switch ($this->entity->status) {
	case 'quoted':
		$this->doc_title = $sale ? 'Quote' : 'Quoted Return';
		break;
	case 'invoiced':
		$this->doc_title = 'Invoice';
		break;
	case 'paid':
	case 'processed':
		$this->doc_title = $sale ? 'Receipt' : 'Return Receipt';
		break;
	case 'voided':
		$this->doc_title = $sale ? 'Sale Void' : 'Return Void';
		break;
	default:
		$this->doc_title = $sale ? 'Sale' : 'Return';
		break;
}
?>
<style type="text/css">
	/* <![CDATA[ */
	#p_muid_receipt .data_col .name {
		font-weight: bold;
	}
	#p_muid_receipt .left_side {
		margin-bottom: 10px;
		float: left;
		clear: left;
	}
	#p_muid_receipt .right_side {
		margin-bottom: 10px;
		float: right;
		clear: right;
	}
	#p_muid_receipt .barcode h1 {
		margin-bottom: 0px;
		padding-right: 15px;
		text-align: right;
	}
	#p_muid_receipt .barcode img {
		margin-top: 0px;
	}
	#p_muid_receipt .right_text {
		text-align: right;
	}
	#p_muid_receipt .left_side div, #p_muid_receipt .right_side div {
		float: left;
	}
	#p_muid_receipt .data_col {
		float: left;
		margin-left: 10px;
		padding-right: 15px;
	}
	#p_muid_receipt .left_side span, #p_muid_receipt .right_side span {
		display: block;
	}
	#p_muid_receipt .aligner {
		text-align: right;
		width: 65px;
	}
	#p_muid_item_list {
		text-align: left;
		border-bottom: 1px solid black;
		border-collapse: collapse;
	}
	#p_muid_item_list th {
		border-bottom: 1px solid black;
		padding: 2px;
	}
	#p_muid_item_list tr td p {
		margin: 0;
	}
	#p_muid_receipt .receipt_note, #p_muid_receipt .comments {
		font-size: 75%;
	}
	/* ]]> */
</style>
<div id="p_muid_receipt" class="pf-form pf-form-twocol">
	<?php
	// Sales rep and sales group entities.
	$sales_rep = $this->entity->user;
	$sales_group = $this->entity->group;
	// Set the location of the group logo image.
	if (isset($sales_group))
		$group_logo = $sales_group->get_logo(true);
	// Document id number.
	$doc_id = ($sale ? 'SA' : 'RE') . $this->entity->id;
	?>
	<div class="left_side">
		<span><img src="<?php echo htmlentities($group_logo); ?>" alt="<?php echo htmlentities($pines->config->page_title); ?>" /></span>
	</div>
	<div class="right_side barcode">
		<h1><?php echo htmlentities($this->doc_title); ?></h1>
		<img src="<?php echo htmlentities(pines_url('com_barcode', 'image', array('code' => $doc_id, 'height' => '60', 'width' => '300', 'style' => '850'))); ?>" alt="Barcode" />
	</div>
	<?php if (isset($sales_rep->guid)) { ?>
	<div class="left_side location">
		<div class="aligner">Location:</div>
		<div class="data_col">
			<span class="name"><?php echo htmlentities($sales_group->name); ?></span>
			<?php if ($sales_group->address_type == 'us') { ?>
			<span><?php echo htmlentities("{$sales_group->address_1}\n{$sales_group->address_2}"); ?></span>
			<span><?php echo htmlentities($sales_group->city); ?>, <?php echo htmlentities($sales_group->state); ?> <?php echo htmlentities($sales_group->zip); ?></span>
			<?php } else { ?>
			<span><?php echo htmlentities($sales_group->address_international); ?></span>
			<?php } ?>
			<span><?php echo format_phone($sales_group->phone); ?></span>
		</div>
	</div>
	<?php } ?>
	<div class="right_side receipt_info">
		<div class="right_text">
			<span><?php echo $sale ? 'Sale' : 'Return'; ?> #:</span>
			<span>Date:</span>
			<span>Time:</span>
			<?php if (!$sale && isset($this->entity->sale)) { ?><span>Sale:</span><?php } ?>
			<?php if (isset($sales_rep->guid)) { ?><span>Sales Rep:</span><?php } ?>
		</div>
		<div class="data_col">
			<span><?php echo htmlentities($this->entity->id); ?></span>
			<?php switch($this->entity->status) {
				case 'invoiced':
					echo '<span>'.format_date($this->entity->invoice_date, 'date_short').'</span>';
					echo '<span>'.format_date($this->entity->invoice_date, 'time_short').'</span>';
					break;
				case 'paid':
					echo '<span>'.format_date($this->entity->tender_date, 'date_short').'</span>';
					echo '<span>'.format_date($this->entity->tender_date, 'time_short').'</span>';
					break;
				case 'processed':
					echo '<span>'.format_date($this->entity->process_date, 'date_short').'</span>';
					echo '<span>'.format_date($this->entity->process_date, 'time_short').'</span>';
					break;
				case 'voided':
					echo '<span>'.format_date($this->entity->void_date, 'date_short').'</span>';
					echo '<span>'.format_date($this->entity->void_date, 'time_short').'</span>';
					break;
				default:
					echo '<span>'.format_date($this->entity->p_cdate, 'date_short').'</span>';
					echo '<span>'.format_date($this->entity->p_cdate, 'time_short').'</span>';
					break;
			} ?>
			<?php if (!$sale && isset($this->entity->sale)) { ?><span><?php echo htmlentities($this->entity->sale->id); ?></span><?php } ?>
			<?php if (isset($sales_rep->guid)) { ?><span><?php echo htmlentities($sales_rep->name); ?></span><?php } ?>
		</div>
	</div>
	<?php if ($pines->config->com_sales->com_customer && isset($this->entity->customer)) { ?>
	<div class="left_side customer">
		<div class="aligner">
			<span>Bill To:</span>
		</div>
		<div class="data_col">
			<span><strong>
				<?php echo htmlentities($this->entity->customer->name); ?>
				<?php if (isset($this->entity->customer->company->name)) {
					echo htmlentities(" ( {$this->entity->customer->company->name} )");
				} ?>
			</strong></span>
			<?php if ($this->entity->customer->address_type == 'us') { if (!empty($this->entity->customer->address_1)) { ?>
			<span><?php echo htmlentities($this->entity->customer->address_1.' '.$this->entity->customer->address_2); ?></span>
			<span><?php echo htmlentities($this->entity->customer->city); ?>, <?php echo htmlentities($this->entity->customer->state); ?> <?php echo htmlentities($this->entity->customer->zip); ?></span>
			<?php } } else {?>
			<span><?php echo htmlentities($this->entity->customer->address_international); ?></span>
			<?php } ?>
		</div>
	</div>
	<?php } ?>
	<div class="pf-element pf-full-width left_side">
		<table id="p_muid_item_list" width="100%">
			<thead>
				<tr>
					<th>SKU</th>
					<th>Item</th>
					<th>Description</th>
					<th class="right_text">Qty</th>
					<th class="right_text">Price</th>
					<th class="right_text">Total</th>
				</tr>
			</thead>
			<tbody>
				<?php if (is_array($this->entity->products)) { foreach ($this->entity->products as $cur_product) {
					if ($cur_product['entity']->hide_on_invoice)
						continue;
					?>
				<tr>
					<td><?php echo htmlentities($cur_product['entity']->sku); ?></td>
					<td><?php echo htmlentities($cur_product['entity']->name); ?></td>
					<td><?php echo $cur_product['entity']->short_description; ?></td>
					<td class="right_text"><?php echo htmlentities($cur_product['quantity']); ?></td>
					<td class="right_text">$<?php echo $pines->com_sales->round($cur_product['price'], true); ?><?php echo empty($cur_product['discount']) ? '' : htmlentities(" - {$cur_product['discount']}"); ?></td>
					<td class="right_text">$<?php echo $pines->com_sales->round($cur_product['line_total'], true); ?></td>
				</tr>
				<?php } } ?>
			</tbody>
		</table>
	</div>
	<div class="pf-element pf-full-width">
		<?php if (is_array($this->entity->payments) && ($this->entity->status == 'paid' || $this->entity->status == 'processed' || $this->entity->status == 'voided')) { ?>
		<div class="left_side">
			<div><strong>Payments<?php if (!$sale) echo ' Returned' ?>:</strong></div>
			<hr style="clear: both;" />
			<div class="right_text">
				<?php foreach ($this->entity->payments as $cur_payment) { ?>
				<span><?php echo htmlentities($cur_payment['label']); ?>:</span>
				<?php } ?>
				<hr style="visibility: hidden;" />
				<span>Amount <?php echo $sale ? 'Tendered' : 'Refunded'; ?>:</span>
				<?php if ($sale) { ?><span>Change:</span><?php } ?>
			</div>
			<div class="data_col right_text">
				<?php foreach ($this->entity->payments as $cur_payment) { ?>
				<span>$<?php echo $pines->com_sales->round($cur_payment['amount'], true); ?></span>
				<?php } ?>
				<hr />
				<span>$<?php echo $pines->com_sales->round($this->entity->amount_tendered, true); ?></span>
				<?php if ($sale) { ?><span>$<?php echo $pines->com_sales->round($this->entity->change, true); ?></span><?php } ?>
			</div>
		</div>
		<?php } ?>
		<div class="right_side">
			<div><strong>Totals:</strong></div>
			<hr style="clear: both;" />
			<div class="right_text">
				<span>Subtotal:</span>
				<?php if ($this->entity->item_fees > 0) { ?><span>Item Fees:</span><?php } ?>
				<span>Tax:</span>
				<hr style="visibility: hidden;" />
				<span><strong>Total: </strong></span>
			</div>
			<div class="data_col right_text">
				<span>$<?php echo $pines->com_sales->round($this->entity->subtotal, true); ?></span>
				<?php if ($this->entity->item_fees > 0) { ?><span>$<?php echo $pines->com_sales->round($this->entity->item_fees, true); ?></span><?php } ?>
				<span>$<?php echo $pines->com_sales->round($this->entity->taxes, true); ?></span>
				<hr />
				<span><strong>$<?php echo $pines->com_sales->round($this->entity->total, true); ?></strong></span>
			</div>
		</div>
	</div>
	<?php if (!empty($this->entity->comments)) { ?>
	<div class="pf-element pf-full-width">
		<div class="pf-field">
			<span class="pf-label">Comments:</span>
			<br />
			<span class="pf-field comments"><?php echo htmlentities($this->entity->comments); ?></span>
		</div>
	</div>
	<?php } ?>
	<?php
	switch ($this->entity->status) {
		case 'quoted':
			$label = $pines->config->com_sales->quote_note_label;
			$text = $pines->config->com_sales->quote_note_text;
			break;
		case 'invoiced':
			$label = $pines->config->com_sales->invoice_note_label;
			$text = $pines->config->com_sales->invoice_note_text;
			break;
		case 'paid':
			$label = $pines->config->com_sales->receipt_note_label;
			$text = $pines->config->com_sales->receipt_note_text;
			break;
		case 'processed':
			$label = $pines->config->com_sales->return_note_label;
			$text = $pines->config->com_sales->return_note_text;
			break;
	}
	if (!empty($text)) {
	?>
	<div class="pf-element pf-full-width">
		<span class="pf-label"><?php echo htmlentities($label); ?></span>
		<br />
		<div class="pf-field receipt_note"><?php echo htmlentities($text); ?></div>
	</div>
	<?php } ?>
</div>