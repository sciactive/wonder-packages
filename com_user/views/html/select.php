<?php
/**
 * A view to load the user selector.
 *
 * @package Components\user
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Angela Murrell <angela@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
?>
<script type="text/javascript">
	$_.loadjs("<?php e($_->config->location); ?>components/com_user/includes/jquery.userselect.js");
	$_.com_user_autouser_url = <?php echo json_encode(pines_url('com_user', 'search')); ?>;
</script>