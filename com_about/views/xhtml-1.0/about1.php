<?php
/**
 * Displays user defined "about" information.
 *
 * @package Pines
 * @subpackage com_about
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');
$this->title = "About {$pines->config->option_title} (Powered by {$pines->config->program_title})";
?>
<p><?php echo $pines->config->com_about->description; ?></p>