<?php
/**
 * Save a package to the repository.
 *
 * @package Components\repository
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_repository/newpackage') )
	punt_user(null, pines_url('com_repository', 'listpackages'));

$files = explode('//', $_REQUEST['package']);

if (!$files) {
	pines_error('Error uploading package(s).');
	pines_redirect(pines_url('com_repository', 'listpackages'));
	return;
}

foreach ($files as $cur_file) {
	$package_filename = $_->uploader->temp($cur_file);
	if (!$package_filename) {
		pines_error('Error getting package '.$cur_file);
		continue;
	}

	$package = new slim;
	if (!$package->read($package_filename)) {
		pines_error('Error reading package.');
		continue;
	}

	// Check that the package is valid.
	if (
			empty($package->ext['package']) ||
			empty($package->ext['name']) ||
			empty($package->ext['author']) ||
			empty($package->ext['version'])
		) {
		pines_notice('Package is not valid. Name, author, and version are all required.');
		continue;
	}

	if (preg_match('/[^a-z0-9_-]/', $package->ext['package'])) {
		pines_notice('Package names can only contain lowercase letters, numbers, underscore, and dash.');
		continue;
	}

	if (preg_match('/(^[_-]|[_-]$)/', $package->ext['package'])) {
		pines_notice('Package names must begin with a letter or number.');
		continue;
	}

	if (!in_array($package->ext['type'], array('component', 'template', 'system', 'meta'))) {
		pines_notice('Only component, template, system, and meta package types are accepted.');
		continue;
	}

	// Check the existing packages for name collisions.
	$cur_index = $_->com_repository->get_index();
	if (array_key_exists($package->ext['package'], $cur_index) && $cur_index[$package->ext['package']]['publisher'] != $_SESSION['user']->username) {
		pines_notice('A component by that name already exists in the repository.');
		continue;
	}

	// Check that the files aren't dangerous.
	$files = $package->get_current_files();
	// Also check for a _MEDIA dir.
	$has_media = false;
	foreach ($files as $cur_file) {
		$has_media = $has_media || $cur_file['path'] == '_MEDIA/';
		if (!is_clean_filename($cur_file['path'])) {
			pines_notice('Package contains dangerous files.');
			continue 2;
		}
	}
	if (in_array($package->ext['type'], array('component', 'template'))) {
		$component = ($package->ext['type'] == 'component' ? preg_replace('/^(com_[a-z0-9]+\/)?.*$/', '$1', $files[0]['path']) : preg_replace('/^(tpl_[a-z0-9]+\/)?.*$/', '$1', $files[0]['path']));
		if (empty($component)) {
			pines_notice('Component/template package contains outside files.');
			continue;
		}
		if ($component != $package->ext['package'].'/') {
			pines_notice('Component/template package is not named correctly.');
			continue;
		}
		foreach ($files as $cur_file) {
			if (strpos($cur_file['path'], $component) !== 0 && strpos($cur_file['path'], '_MEDIA/') !== 0) {
				pines_notice('Component/template package contains outside files.');
				continue 2;
			}
		}
	}

	if ($package->ext['screens'] && count($package->ext['screens']) > 10) {
		pines_notice('Maximum 10 screen shots allowed.');
		continue;
	}

	// Move package into repository.
	$dir = clean_filename($_->config->com_repository->repository_path.$_SESSION['user']->guid.'/'.$package->ext['package'].'/'.$package->ext['version'].'/');
	$filename = $dir.clean_filename("{$package->ext['package']}-{$package->ext['version']}.slm");
	$sig_filename = $dir.clean_filename("{$package->ext['package']}-{$package->ext['version']}.sig");
	$md5_filename = $dir.clean_filename("{$package->ext['package']}-{$package->ext['version']}.md5");
	if (file_exists($sig_filename) && !unlink($sig_filename)) {
		pines_error('Old signature file couldn\'t be removed.');
		continue;
	}
	if (file_exists($md5_filename) && !unlink($md5_filename)) {
		pines_error('Old MD5 file couldn\'t be removed.');
		continue;
	}

	if (!file_exists($dir) && !mkdir($dir, 0700, true)) {
		pines_error('Error creating user directory.');
		continue;
	}

	if ($has_media) {
		// Extract the media directory.
		$package->working_directory = $dir;
		$package->extract('_MEDIA/', true, '/^_MEDIA\/.*\//');
		$media = glob("{$dir}/_MEDIA/*");
		foreach ($media as $cur_media) {
			if (!chmod($cur_media, 0600)) {
				unlink($cur_media);
				continue;
			}
			if (filesize($cur_media) > 307200) {
				// Max size 300KB.
				pines_notice('Max media filesize is 300KB. Please remove media bigger than 300KB.');
				unlink($cur_media);
				continue 2;
			}
			$image = new Imagick;
			if (!$image->readImage($cur_media)) {
				pines_notice('Couldn\'t read media "'.basename($cur_media).'". Please only upload images.');
				unlink($cur_media);
				continue 2;
			}
		}
		if (count($media) >= 11) {
			pines_notice('Maximum of 11 media files allowed. 10 screenshots and 1 icon.');
			foreach ($media as $cur_media)
				unlink($cur_media);
			continue;
		}
	}

	$md5 = md5_file($package_filename);
	if (!$md5) {
		pines_error('Couldn\'t create MD5 sum for package.');
		continue;
	}
	if (!rename($package_filename, $filename)) {
		pines_error('Error moving package into repository.');
		continue;
	}
	if (!file_put_contents($md5_filename, $md5)) {
		pines_error('Error writing MD5 sum to file.');
		continue;
	}

	pines_notice('Saved package ['.$package->ext['package'].']. Now you should refresh your index to see it.');
}

pines_redirect(pines_url('com_repository', 'listpackages'));