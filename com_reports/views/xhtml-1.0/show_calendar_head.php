<?php
/**
 * Includes for the sales report calendar.
 *
 *
 * Built upon:
 *
 * FullCalendar Created by Adam Shaw
 * http://arshaw.com/fullcalendar/
 *
 * @package Pines
 * @subpackage com_reports
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');
?>
<script type="text/javascript">
	// <![CDATA[
	pines.loadcss("<?php echo htmlentities($pines->config->rela_location); ?>components/com_reports/includes/fullcalendar.css");
	pines.loadjs("<?php echo htmlentities($pines->config->rela_location); ?>components/com_reports/includes/fullcalendar.min.js");
	// ]]>
</script>
