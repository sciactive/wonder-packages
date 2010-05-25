<?php
/**
 * Show the results of the file uploader test.
 *
 * @package Pines
 * @subpackage com_elfinderupload
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_elfinder/finder') )
	punt_user('You don\'t have necessary permission.', pines_url('com_elfinderupload', 'test'));

$module = new module('com_elfinderupload', 'result', 'content');
$module->file = $_REQUEST['file'];

?>