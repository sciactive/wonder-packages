<?php
/**
 * Set processing type and payment action arrays.
 *
 * @package Components\sales
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

/**
 * List of payment processing types.
 *
 * Payment processing types allow another component to handle the processing
 * of payments, such as credit card or gift card payments.
 *
 * To add a processing type, your code must add a new array with the
 * following values:
 *
 * - "name" - The name of your type. Ex: 'com_giftcard/giftcard'
 * - "cname" - The common name of your action. Ex: 'Gift Card'
 * - "description" - A description of the action. Ex: 'Deduct the payment from a gift card.'
 * - "callback" - Callback to your function. Ex: array($_->com_giftcard, 'process_giftcard')
 *
 * The callback will be passed an array which may contain the following
 * associative entries:
 *
 * - "action" - The processing which is being requested.
 * - "type" - When approving payments, will be "charge" or "return".
 * - "name" - The name of the type being called.
 * - "payment" - The sale's/return's payment entry. This holds information about the payment.
 * - "ticket" - The sale/return entity.
 *
 * "action" will be one of:
 *
 * - "request" - The payment type has been selected.
 * - "approve" - The sale is being invoiced, and the payment needs to be
 *   approved.
 * - "tender" - The sale is being processed, and the payment needs to be
 *   processed.
 * - "change" - The sale requires change to be given, and this payment type has
 *   been selected to give change.
 * - "void" - The sale is being voided, and the payment needs to be voided.
 * - "return" - The payment is being returned and the funds need to be returned.
 *
 * If "action" is "request", the callback can provide a form to collect
 * information from the user. It is expected to return a module, which will be
 * placed in the $result argument. The module should not be attached to a
 * position. The form element should not be included. Only include the inputs of
 * the form. The form's inputs will be parsed into an array and saved as a
 * property called "data" in the payment entry. If you don't need any
 * information from the user, simply do nothing.
 *
 * If "action" is "request_cust", it is the same as "request", except that the
 * form is meant to be shown to a customer in the web shop, not an
 * employee. This might differ, for example, if a credit card form includes a
 * "slide card" option for employees, but not customers.
 *
 * If "action" is "approve", the callback needs to set "status" in the payment
 * array to "approved", "declined", "info_requested", or
 * "manager_approval_needed".
 *
 * If "action" is "tender", the callback needs to set "status" to "tendered" on
 * success.
 *
 * If "action" is "change", the callback needs to set the "change_given"
 * property on the sale object to true or false.
 *
 * If "action" is "void", the callback needs to set "status" to "voided" on
 * success.
 *
 * @var array $_->config->com_sales->processing_types
 */
$_->config->com_sales->processing_types = array();

/**
 * List of product actions.
 *
 * Product actions are callbacks that can be called when a product is
 * received, adjusted, sold, voided, or returned.
 *
 * To add a product action, your code must add a new array with the
 * following values:
 *
 * - "type" - An array or string of the event(s) the action should be called
 *   for. Out of "received", "adjusted", "sold", "voided", and "returned".
 * - "name" - The name of your action (not a 2be action).
 *   Ex: 'com_gamephear/create_gamephear_account'
 * - "cname" - The common name of your action. Ex: 'Create GamePhear Account'
 * - "description" - A description of the action.
 *   Ex: 'Creates a GamePhear account for the customer.'
 * - "callback" - Callback to your function.
 *   Ex: array($_->com_gamephear, 'create_account')
 *
 * The callback will be passed an array which may contain the following
 * associative entries:
 *
 * - "type" - The type of event that has occurred.
 * - "name" - The name of the action being called.
 * - "product" - The product entity.
 * - "stock_entry" - The stock entry entity.
 * - "ticket" - The sale/return entity.
 * - "po" - The PO entity.
 * - "transfer" - The transfer entity.
 *
 * @var array $_->config->com_sales->product_actions
 */
$_->config->com_sales->product_actions = array();