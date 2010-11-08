<?php
/**
 * Search directories, returning JSON.
 *
 * @package Pines
 * @subpackage com_packager
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Zak Huber <zak@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_packager/newpackage') || !gatekeeper('com_packager/editpackage'))
	punt_user(null, pines_url('com_packager', 'glob'));

$pines->page->override = true;
$pines->page->override_doc(json_encode(glob(clean_filename($_REQUEST['q']).'*', GLOB_MARK)));

?>