<?php
/**
 * Tests the file uploader.
 *
 * @package Components\elfinderupload
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'elFinder Uploader';
$_->uploader->load();
?>
<form class="pf-form" method="post" action="<?php e(pines_url('com_elfinderupload', 'result')); ?>">
	<div class="pf-element pf-heading">
		<h3>File Uploading Test</h3>
	</div>
	<div class="pf-element">
		<label>
			<span class="pf-label">File</span>
			<input class="pf-field form-control puploader" type="text" name="file" />
		</label>
	</div>
	<div class="pf-element">
		<label>
			<span class="pf-label">Temp File</span>
			<span class="pf-note">A temp file uploader only lets you upload to a temporary folder.</span>
			<input class="pf-field form-control puploader puploader-temp" type="text" name="tmpfile" />
		</label>
	</div>
	<div class="pf-element">
		<label>
			<span class="pf-label">Allow Folders</span>
			<input class="pf-field form-control puploader puploader-folders" type="text" name="folder" />
		</label>
	</div>
	<div class="pf-element">
		<label>
			<span class="pf-label">Files</span>
			<input class="pf-field form-control puploader puploader-multiple" type="text" name="files" />
		</label>
	</div>
	<div class="pf-element pf-buttons">
		<input class="pf-button btn btn-default" type="submit" value="Submit" />
	</div>
</form>