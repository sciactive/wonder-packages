<?php
/**
 * A view to load the customer selector.
 *
 * @package Components\customer
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<script type="text/javascript">
	$_.loadjs("<?php e($_->config->location); ?>components/com_customer/includes/jquery.customerselect.js");
	$_.com_customer_autocustomer_url = <?php echo json_encode(pines_url('com_customer', 'customer/search')); ?>;
</script>