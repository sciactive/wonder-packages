<?php
/**
 * Tests the file uploader.
 *
 * @package Pines
 * @subpackage com_elfinderupload
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'elFinder Uploader';
$pines->uploader->load();
?>
<form class="pf-form" method="post" action="<?php echo htmlspecialchars(pines_url('com_elfinderupload', 'result')); ?>">
	<div class="pf-element pf-heading">
		<h3>File Uploading Test</h3>
	</div>
	<div class="pf-element">
		<label>
			<span class="pf-label">File</span>
			<input class="pf-field puploader" type="text" name="file" />
		</label>
	</div>
	<div class="pf-element pf-buttons">
		<input class="pf-button btn" type="submit" value="Submit" />
	</div>
</form>