<?php
/**
 * Provides a form for the user to edit a mailing.
 *
 * @package Pines
 * @subpackage com_newsletter
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

$this->title = (is_null($this->entity->guid)) ? 'Editing New Mail' : 'Editing ['.htmlentities($this->entity->name).']';

$pines->page->head("<!-- Skin CSS file -->\n");
$pines->page->head("<link rel=\"stylesheet\" type=\"text/css\" href=\"http://yui.yahooapis.com/2.7.0/build/assets/skins/sam/skin.css\">\n");
$pines->page->head("<!-- Utility Dependencies -->\n");
$pines->page->head("<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.7.0/build/yahoo-dom-event/yahoo-dom-event.js\"></script>\n");
$pines->page->head("<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.7.0/build/element/element-min.js\"></script>\n");
$pines->page->head("<!-- Needed for Menus, Buttons and Overlays used in the Toolbar -->\n");
$pines->page->head("<script src=\"http://yui.yahooapis.com/2.7.0/build/container/container_core-min.js\"></script>\n");
$pines->page->head("<script src=\"http://yui.yahooapis.com/2.7.0/build/menu/menu-min.js\"></script>\n");
$pines->page->head("<script src=\"http://yui.yahooapis.com/2.7.0/build/button/button-min.js\"></script>\n");
$pines->page->head("<!-- Source file for Rich Text Editor-->\n");
$pines->page->head("<script src=\"http://yui.yahooapis.com/2.7.0/build/editor/editor-min.js\"></script>\n");
$pines->page->head("<script src=\"http://yui.yahooapis.com/2.7.0/build/connection/connection-min.js\"></script>\n");
$pines->page->head("<script src=\"{$pines->config->rela_location}components/com_newsletter/js/yui-image-uploader26.js\"></script>\n");
$pines->page->head("<script type=\"text/javascript\">\n");
$pines->page->head("var editor = new YAHOO.widget.Editor('data', {\n");
$pines->page->head("	handleSubmit: true,\n");
$pines->page->head("	dompath: true,\n");
$pines->page->head("	animate: true\n");
$pines->page->head("});\n");
$pines->page->head("editor._defaultToolbar.titlebar = false;\n");
$pines->page->head("editor._defaultToolbar.buttonType = 'advanced';\n");
$pines->page->head("yuiImgUploader(editor, 'data', '".pines_url('com_newsletter', 'upload')."','image');\n");
$pines->page->head("editor.render();\n");
$pines->page->head("</script>\n");
?>
<div class="yui-skin-sam">
<form class="pform" enctype="multipart/form-data" name="editingmail" method="post" action="<?php echo pines_url($this->new_option, $this->new_action); ?>">
	<?php if (isset($this->entity->guid)) { ?>
	<div class="date_info" style="float: right; text-align: right;">
	<?php if (isset($this->entity->uid)) { ?>
	<span>Created By: <span class="date"><?php echo $pines->user_manager->get_username($this->entity->uid); ?></span></span>
	<br />
	<?php } ?>
	<span>Created On: <span class="date"><?php echo date('Y-m-d', $this->entity->p_cdate); ?></span></span>
	<br />
	<span>Modified On: <span class="date"><?php echo date('Y-m-d', $this->entity->p_mdate); ?></span></span>
	</div>
	<?php } ?>
	<div class="element buttons" style="padding-left: 0;">
		<input class="button ui-state-default ui-priority-primary ui-corner-all" type="submit" value="Save Mail" />
		<input class="button ui-state-default ui-priority-secondary ui-corner-all" type="button" onclick="window.location='<?php echo pines_url($this->close_option, $this->close_action); ?>';" value="Close" /> <small>(Closing will lose any unsaved changes!)</small>
	</div>
	<div class="element">
		<label><span class="label">Name</span>
		<input class="field ui-widget-content" type="text" name="name" size="24" value="<?php echo htmlentities($this->entity->name); ?>" /></label>
	</div>
	<div class="element">
		<label><span class="label">Subject</span>
		<input class="field ui-widget-content" type="text" name="subject" size="24" value="<?php echo htmlentities($this->entity->subject); ?>" /></label>
	</div>
	<div class="element heading">
		<h1>Message</h1>
	</div>
	<div class="element">
		<textarea rows="3" cols="35" class="field ui-widget-content" name="data" id="data" style="width: 99%;"><?php echo htmlentities($this->entity->message); ?></textarea>
	</div>
	<div class="element heading">
		<h1>Attachments</h1>
	</div>
	<div class="element">
		<span class="label">Current Attachments</span>
		<?php if ( !empty($this->entity->attachments) ) {
			echo '<div class="group">';
			foreach ($this->entity->attachments as $cur_attachment) { ?>
		<label><input class="field ui-widget-content" type="checkbox" name="attach_<?php echo clean_checkbox($cur_attachment); ?>" checked="checked" /><?php echo htmlentities($cur_attachment); ?></label><br />
		<?php }
		echo '</div>';
		} ?>
	</div>
	<div class="element">
		<label><span class="label">Upload</span>
		<input class="field ui-widget-content" name="attachment" type="file" /></label>
	</div>
	<div class="element buttons" style="padding-left: 0;">
		<input type="hidden" name="update" value="yes" />
		<input type="hidden" name="mail_id" value="<?php echo $this->entity->guid; ?>" />
		<input class="button ui-state-default ui-priority-primary ui-corner-all" type="submit" value="Save Mail" />
		<input class="button ui-state-default ui-priority-secondary ui-corner-all" type="button" onclick="window.location='<?php echo pines_url($this->close_option, $this->close_action); ?>';" value="Close" /> <small>(Closing will lose any unsaved changes!)</small>
	</div>
</form>
</div>