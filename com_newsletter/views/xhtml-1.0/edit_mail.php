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

$page->head("<!-- Skin CSS file -->\n");
$page->head("<link rel=\"stylesheet\" type=\"text/css\" href=\"http://yui.yahooapis.com/2.7.0/build/assets/skins/sam/skin.css\">\n");
$page->head("<!-- Utility Dependencies -->\n");
$page->head("<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.7.0/build/yahoo-dom-event/yahoo-dom-event.js\"></script>\n");
$page->head("<script type=\"text/javascript\" src=\"http://yui.yahooapis.com/2.7.0/build/element/element-min.js\"></script>\n");
$page->head("<!-- Needed for Menus, Buttons and Overlays used in the Toolbar -->\n");
$page->head("<script src=\"http://yui.yahooapis.com/2.7.0/build/container/container_core-min.js\"></script>\n");
$page->head("<script src=\"http://yui.yahooapis.com/2.7.0/build/menu/menu-min.js\"></script>\n");
$page->head("<script src=\"http://yui.yahooapis.com/2.7.0/build/button/button-min.js\"></script>\n");
$page->head("<!-- Source file for Rich Text Editor-->\n");
$page->head("<script src=\"http://yui.yahooapis.com/2.7.0/build/editor/editor-min.js\"></script>\n");
$page->head("<script src=\"http://yui.yahooapis.com/2.7.0/build/connection/connection-min.js\"></script>\n");
$page->head("<script src=\"{$config->rela_location}components/com_newsletter/js/yui-image-uploader26.js\"></script>\n");
$page->head("<script type=\"text/javascript\">\n");
$page->head("var editor = new YAHOO.widget.Editor('data', {\n");
$page->head("	handleSubmit: true,\n");
$page->head("	dompath: true,\n");
$page->head("	animate: true\n");
$page->head("});\n");
$page->head("editor._defaultToolbar.titlebar = false;\n");
$page->head("editor._defaultToolbar.buttonType = 'advanced';\n");
$page->head("yuiImgUploader(editor, 'data', '".$config->template->url('com_newsletter', 'upload')."','image');\n");
$page->head("editor.render();\n");
$page->head("</script>\n");
?>
<div class="yui-skin-sam">
<form class="pform" enctype="multipart/form-data" name="editingmail" method="post" action="<?php echo $config->template->url($this->new_option, $this->new_action); ?>">
    <div class="element buttons" style="padding-left: 0;">
        <input class="button" type="submit" value="Save Mail" />
        <input class="button" type="button" onclick="window.location='<?php echo $config->template->url($this->close_option, $this->close_action); ?>';" value="Close" /> <small>(Closing will lose any unsaved changes!)</small>
    </div>
    <div class="element">
        <label><span class="label">Name</span>
        <input class="field" type="text" name="name" size="20" value="<?php echo htmlentities($this->mail->name); ?>" /></label>
    </div>
    <div class="element">
        <label><span class="label">Subject</span>
        <input class="field" type="text" name="subject" size="20" value="<?php echo htmlentities($this->mail->subject); ?>" /></label>
    </div>
    <div class="element heading">
        <h1>Message</h1>
    </div>
    <div class="element">
        <textarea class="field" rows="30" name="data" id="data" style="width: 99%;"><?php echo htmlentities($this->mail->message); ?></textarea>
    </div>
    <div class="element heading">
        <h1>Attachments</h1>
    </div>
    <div class="element">
        <span class="label">Current Attachments</span>
        <?php if ( !empty($this->mail->attachments) ) {
            echo '<div class="group">';
            foreach ($this->mail->attachments as $cur_attachment) { ?>
        <label><input class="field" type="checkbox" name="attach_<?php echo clean_checkbox($cur_attachment); ?>" checked="checked" /><?php echo htmlentities($cur_attachment); ?></label><br />
        <?php }
        echo '</div>';
        } ?>
    </div>
    <div class="element">
        <label><span class="label">Upload</span>
        <input class="field" name="attachment" type="file" /></label>
    </div>
    <div class="element buttons" style="padding-left: 0;">
        <input type="hidden" name="update" value="yes" />
        <input type="hidden" name="mail_id" value="<?php echo $this->mail->guid; ?>" />
        <input class="button" type="submit" value="Save Mail" />
        <input class="button" type="button" onclick="window.location='<?php echo $config->template->url($this->close_option, $this->close_action); ?>';" value="Close" /> <small>(Closing will lose any unsaved changes!)</small>
    </div>
</form>
</div>