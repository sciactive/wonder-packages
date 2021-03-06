<?php
/**
 * Connector for the elFinder file manager.
 *
 * @package Components\elfinder
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_elfinder/finder') && !gatekeeper('com_elfinder/finderself') )
	punt_user(null, pines_url('com_elfinder', 'finder'));

error_reporting(0); // Set E_ALL for debuging

/**
 * Simple function to demonstrate how to control file access using "accessControl" callback.
 * This method will disable accessing files/folders starting from  '.' (dot)
 *
 * @param  string  $attr  attribute name (read|write|locked|hidden)
 * @param  string  $path  file path relative to volume root directory started with directory separator
 * @return bool
 **/
function com_elfinder__access($attr, $path, $data, $volume) {
	global $_;
	if ($_->config->com_elfinder->dot_files) {
		return ($attr == 'read' || $attr == 'write');
	} else {
		return strpos(basename($path), '.') === 0   // if file/folder begins with '.' (dot)
			? !($attr == 'read' || $attr == 'write')  // set read+write to false, other (locked+hidden) set to true
			: ($attr == 'read' || $attr == 'write');  // else set read+write to true, locked+hidden to false
	}
}

$opts = array(
	'roots' => array(
		array(
			'driver' => 'LocalFileSystem',
			'path' => $_->config->com_elfinder->root,
			'alias' => $_->config->com_elfinder->root_alias,
			'URL' => $_->config->com_elfinder->root_url,
			'fileMode' => $_->config->com_elfinder->file_mode,
			'dirMode' => $_->config->com_elfinder->dir_mode,
			'tmbPath' => $_->config->com_elfinder->tmb_dir,
			'tmbCleanProb' => $_->config->com_elfinder->tmb_clean_prob,
			'tmbSize' => $_->config->com_elfinder->tmb_size,
			'tmbPathMode' => 0700,
			'dateFormat' => $_->config->com_elfinder->date_format,
			'timeFormat' => $_->config->com_elfinder->time_format,
			'defaults' => array(
				'read' => $_->config->com_elfinder->default_read,
				'write' => $_->config->com_elfinder->default_write
			),
			'disabled' => empty($_->config->com_elfinder->disabled) ? array() : $_->config->com_elfinder->disabled,
			'accessControl' => 'com_elfinder__access'
		)
	),
);

if (!empty($_REQUEST['start_path']))
	$opts['roots'][0]['startPath'] = $_REQUEST['start_path'];

if ($_->config->com_elfinder->upload_check) {
	$opts['roots'][0]['uploadAllow'] = $_->config->com_elfinder->upload_allow;
	$opts['roots'][0]['uploadDeny'] = $_->config->com_elfinder->upload_deny;
	$opts['roots'][0]['uploadOrder'] = $_->config->com_elfinder->upload_order;
}
if (isset($_SESSION['user']) && file_exists($_->config->com_elfinder->root . $_->config->com_elfinder->own_root)) {
	if (!gatekeeper('com_elfinder/finder')) {
		$opts['roots'][0]['path'] .= $_->config->com_elfinder->own_root . $_SESSION['user']->guid . '/';
		$opts['roots'][0]['URL'] .= $_->config->com_elfinder->own_root . $_SESSION['user']->guid . '/';
		$opts['roots'][0]['alias'] = $_->config->com_elfinder->own_root_alias;
		if (!file_exists($opts['roots'][0]['path']))
			mkdir($opts['roots'][0]['path']);
	} else {
		$opts['roots'][1] = $opts['roots'][0];
		$opts['roots'][1]['path'] .= $_->config->com_elfinder->own_root . $_SESSION['user']->guid . '/';
		$opts['roots'][1]['URL'] .= $_->config->com_elfinder->own_root . $_SESSION['user']->guid . '/';
		$opts['roots'][1]['alias'] = $_->config->com_elfinder->own_root_alias;
		if (!file_exists($opts['roots'][1]['path']))
			mkdir($opts['roots'][1]['path']);
	}
}

$connector = new elFinderConnector(new elFinder($opts));
$connector->run();