<?php
/**
 * An email sent to verify a user's new email address.
 *
 * @package Components\user
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');
$this->title = '#to_first_name#, Please verify your new email address for #system_name#.';
?>
Hi #to_name#,<br />
<br />
We've received a request to change your email address at <a href="#site_link#" target="_blank">#site_link#</a>.
Please verify your new email by clicking on the following link:<br />
<br />
<a href="#verify_link#" target="_blank">#verify_link#</a><br />
<br />
Once you verify this email address, we will no longer send emails to
#old_email#. If you didn't make this request, you can ignore this message.<br />
<br />
Regards,<br />
#system_name#