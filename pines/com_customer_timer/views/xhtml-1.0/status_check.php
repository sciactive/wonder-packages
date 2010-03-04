<?php
/**
 * Checks and notifies about customer timer status.
 *
 * @package Pines
 * @subpackage com_customer_timer
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');
?>
<script type="text/javascript">
	// <![CDATA[
	$(function(){pines.com_customer_timer = {
		"ppm": <?php echo (int) $pines->config->com_customer_timer->ppm; ?>,
		"level_critical": <?php echo (int) $pines->config->com_customer_timer->level_critical; ?>,
		"level_warning": <?php echo (int) $pines->config->com_customer_timer->level_warning; ?>,
		"status_url": "<?php echo pines_url('com_customer_timer', 'status_json'); ?>",
		"status_page_url": "<?php echo pines_url('com_customer_timer', 'status'); ?>"
	};});
	// ]]>
</script>
<script type="text/javascript" src="<?php echo $pines->config->rela_location; ?>components/com_customer_timer/includes/status_check.js"></script>