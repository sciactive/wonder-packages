<?php
/**
 * Displays page content.
 *
 * @package Components\content
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core *//* @var $this module */
defined('P_RUN') or die('Direct access prohibited');

$this->classes[] = 'content_page';
$this->classes[] = 'content_page_full';

if (!isset($this->entity))
	$this->entity = com_content_page::factory((int) $this->id);

if (!isset($this->entity->guid)) {
	echo 'No page is specified or the page is inaccessible.';
	return;
}

// Custom head code.
if ($this->entity->enable_custom_head && $_->config->com_content->custom_head) {
	$head = new module('system', 'null', 'head');
	$head->content($this->entity->custom_head);
}

if ($this->entity->get_option('show_title'))
	$this->title = '<span class="page_title">'.h($this->entity->name).'</span>';

if ($this->entity->get_option('show_author_info'))
	$this->note = '<span class="page_info"><span class="page_posted_by_text">Posted by </span><span class="page_author">'.h($this->entity->user->name).'</span><span class="page_posted_on_text"> on </span><span class="page_date">'.h(format_date($this->entity->cdate, 'date_long')).'<span class="page_period_text">.</span></span></span>';

if ($this->entity->content_tags && $_->config->com_content->tags_position == 'before') {
	echo '<div style="position: relative" class="page_tags">'.h($_->config->com_content->tags_text);
	$tag_pieces = array();
	foreach ($this->entity->content_tags as $cur_tag)
		$tag_pieces[] = '<a class="page_tag" href="'.h(pines_url('com_content', 'tag', array('a' => $cur_tag))).'">'.h($cur_tag).'</a>';
	echo implode(', ', $tag_pieces).'</div>';
}

if ($this->entity->get_option('show_intro')) {
	if ($_->config->com_content->wrap_pages)
		echo '<div style="position: relative;">';
	echo format_content($this->entity->intro);
	if ($_->config->com_content->wrap_pages)
		echo '</div>';
}

if ($_->config->com_content->wrap_pages)
	echo '<div style="position: relative;">';
echo format_content($this->entity->content);
if ($_->config->com_content->wrap_pages)
	echo '</div>';

if ($this->entity->content_tags && $_->config->com_content->tags_position == 'after') {
	echo '<div style="position: relative" class="page_tags">'.h($_->config->com_content->tags_text);
	$tag_pieces = array();
	foreach ($this->entity->content_tags as $cur_tag)
		$tag_pieces[] = '<a class="page_tag" href="'.h(pines_url('com_content', 'tag', array('a' => $cur_tag))).'">'.h($cur_tag).'</a>';
	echo implode(', ', $tag_pieces).'</div>';
}