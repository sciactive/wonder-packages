<?php
/**
 * Display a product page.
 *
 * @package Components\shop
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if (!empty($_REQUEST['id'])) {
	$entity = com_sales_product::factory((int) $_REQUEST['id']);
} else {
	$entity = $_->nymph->getEntity(
			array('class' => com_sales_product),
			array('&',
				'tag' => array('com_sales', 'product'),
				'data' => array(
					array('enabled', true),
					array('alias', $_REQUEST['a'])
				)
			)
		);
}

if (!isset($entity->guid) || !$entity->enabled || !$entity->show_in_shop)
	throw new HttpClientException(null, 404);

// Page title.
if ($entity->title_use_name || !isset($entity->title))
	$title = format_content($entity->name);
else
	$title = format_content($entity->title);
switch ($entity->title_position) {
	case 'prepend':
	default:
		$_->page->title_pre("$title - ");
		break;
	case 'append':
		$_->page->title(" - $title");
		break;
	case 'replace':
		$_->page->title_set($title);
		break;
}

// Meta tags.
if ($entity->meta_tags) {
	$module = new module('com_shop', 'meta_tags', 'head');
	$module->entity = $entity;
}

$module = new module('com_shop', 'breadcrumb', 'breadcrumbs');
$module->entity = $entity;

$module = new module('com_shop', 'product', 'content');
$module->entity = $entity;