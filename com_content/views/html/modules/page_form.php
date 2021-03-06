<?php
/**
 * Provides a form for the user to choose a page.
 *
 * @package Components\content
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$pages = $_->nymph->getEntities(array('class' => com_content_page), array('&', 'tag' => array('com_content', 'page')));
$_->nymph->sort($pages, 'name');
?>
<div class="pf-form">
	<div class="pf-element">
		<label><span class="pf-label">Page</span>
			<select class="pf-field form-control" name="id">
				<?php foreach ($pages as $cur_page) { ?>
				<option value="<?php e($cur_page->guid); ?>"<?php echo $this->id == "$cur_page->guid" ? ' selected="selected"' : ''; ?>><?php e($cur_page->name); ?></option>
				<?php } ?>
			</select></label>
	</div>
</div>