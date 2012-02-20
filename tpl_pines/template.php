<?php
/**
 * Main page of the Pines template.
 *
 * The page which is output to the user is built using this file.
 *
 * @package Pines
 * @subpackage tpl_pines
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');
// Experimental AJAX code.
if ($pines->config->tpl_pines->ajax && ($_REQUEST['tpl_pines_ajax'] == 1 && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
	$return = array(
		'notices' => $pines->page->get_notice(),
		'errors' => $pines->page->get_error(),
		'main_menu' => $pines->page->render_modules('main_menu', 'module_head'),
		'pos_head' => $pines->page->render_modules('head', 'module_head'),
		'pos_top' => $pines->page->render_modules('top', 'module_header'),
		'pos_header' => $pines->page->render_modules('header', 'module_header'),
		'pos_header_right' => $pines->page->render_modules('header_right', 'module_header_right'),
		'pos_pre_content' => $pines->page->render_modules('pre_content', 'module_header'),
		'pos_breadcrumbs' => $pines->page->render_modules('breadcrumbs'),
		'pos_content_top_left' => $pines->page->render_modules('content_top_left'),
		'pos_content_top_right' => $pines->page->render_modules('content_top_right'),
		'pos_content' => $pines->page->render_modules('content', 'module_content'),
		'pos_content_bottom_left' => $pines->page->render_modules('content_bottom_left'),
		'pos_content_bottom_right' => $pines->page->render_modules('content_bottom_right'),
		'pos_post_content' => $pines->page->render_modules('post_content', 'module_header'),
		'pos_left' => $pines->page->render_modules('left'),
		'pos_right' => $pines->page->render_modules('right', 'module_right'),
		'pos_footer' => $pines->page->render_modules('footer', 'module_header'),
		'pos_bottom' => $pines->page->render_modules('bottom', 'module_header')
	);
	echo json_encode($return);
	return;
}
header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html <?php echo in_array('font', $pines->config->tpl_pines->fancy_style) ? ' id="fancy_font"' : ''; ?>>
<head>
	<meta charset="utf-8" />
	<title><?php echo htmlspecialchars($pines->page->get_title()); ?></title>
	<link rel="icon" type="image/vnd.microsoft.icon" href="<?php echo htmlspecialchars($pines->config->location); ?>favicon.ico" />
	<meta name="HandheldFriendly" content="true" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<link href="<?php echo htmlspecialchars($pines->config->location); ?>templates/<?php echo htmlspecialchars($pines->current_template); ?>/css/pines.css" media="all" rel="stylesheet" type="text/css" />
	<style type="text/css">.page-width {width: auto;<?php echo $pines->config->template->width === 0 ? '' : ' max-width: '.(int) $pines->config->template->width.'px;'; ?>}</style>
	<link href="<?php echo htmlspecialchars($pines->config->location); ?>templates/<?php echo htmlspecialchars($pines->current_template); ?>/css/print.css" media="print" rel="stylesheet" type="text/css" />

	<link href="<?php echo htmlspecialchars($pines->config->location); ?>templates/<?php echo htmlspecialchars($pines->current_template); ?>/css/dropdown/dropdown.css" media="all" rel="stylesheet" type="text/css" />
	<link href="<?php echo htmlspecialchars($pines->config->location); ?>templates/<?php echo htmlspecialchars($pines->current_template); ?>/css/dropdown/dropdown.vertical.css" media="all" rel="stylesheet" type="text/css" />
	<link href="<?php echo htmlspecialchars($pines->config->location); ?>templates/<?php echo htmlspecialchars($pines->current_template); ?>/css/dropdown/themes/jqueryui/jqueryui.css" media="all" rel="stylesheet" type="text/css" />

	<script type="text/javascript" src="<?php echo htmlspecialchars($pines->config->rela_location); ?>system/includes/js.php"></script>
	<?php if ($pines->config->tpl_pines->menu_delay) { ?>
	<script type="text/javascript">
		pines.tpl_pines_menu_delay = true;</script>
	<?php } ?>
	<script type="text/javascript" src="<?php echo htmlspecialchars($pines->config->location); ?>templates/<?php echo htmlspecialchars($pines->current_template); ?>/js/template.js"></script>
	<?php if ($pines->config->tpl_pines->ajax) { ?>
	<script type="text/javascript" src="<?php echo htmlspecialchars($pines->config->location); ?>templates/<?php echo htmlspecialchars($pines->current_template); ?>/js/ajax.js"></script>
	<?php } ?>

	<!--[if lt IE 7]>
	<script type="text/javascript" src="<?php echo htmlspecialchars($pines->config->location); ?>templates/<?php echo htmlspecialchars($pines->current_template); ?>/js/jquery/jquery.dropdown.js"></script>
	<![endif]-->

	<?php echo $pines->page->render_modules('head', 'module_head'); ?>
</head>
<body class="ui-widget ui-widget-content<?php echo in_array('shadows', $pines->config->tpl_pines->fancy_style) ? ' shadows' : ''; ?>">
	<div id="top"><?php
		echo $pines->page->render_modules('top', 'module_header');
		$error = $pines->page->get_error();
		$notice = $pines->page->get_notice();
		if ( $error || $notice ) { ?>
		<script type="text/javascript">
			pines(function(){
				<?php
				if ( $error ) { foreach ($error as $cur_item) {
					echo 'pines.error('.json_encode(htmlspecialchars($cur_item)).", \"Error\");\n";
				} }
				if ( $notice ) { foreach ($notice as $cur_item) {
					echo 'pines.notice('.json_encode(htmlspecialchars($cur_item)).", \"Notice\");\n";
				} }
				?>
			});
		</script>
		<?php
		}
	?></div>
	<div id="header" class="ui-widget-header">
		<div class="container-fluid page-width centered">
			<div class="row-fluid">
				<div class="span4">
					<div id="page_title">
						<a href="<?php echo htmlspecialchars($pines->config->full_location); ?>">
							<?php if ($pines->config->tpl_pines->use_header_image) { ?>
							<img src="<?php echo htmlspecialchars($pines->config->tpl_pines->header_image); ?>" alt="<?php echo htmlspecialchars($pines->config->page_title); ?>" />
							<?php } else { ?>
							<span><?php echo htmlspecialchars($pines->config->page_title); ?></span>
							<?php } ?>
						</a>
					</div>
				</div>
				<div id="header_position" class="span4">
					<?php echo $pines->page->render_modules('header', 'module_header'); ?>
				</div>
				<div id="header_right" class="span4">
					<?php echo $pines->page->render_modules('header_right', 'module_header_right'); ?>
				</div>
			</div>
		</div>
		<div class="container-fluid page-width centered">
			<div class="row-fluid">
				<div class="span12" class="menuwrap ui-widget-content">
					<div id="main_menu"<?php echo $pines->config->tpl_pines->center_menu ? ' class="centered"' : ''; ?>><?php echo $pines->page->render_modules('main_menu', 'module_head'); ?></div>
				</div>
			</div>
		</div>
	</div>
	<div class="container-fluid page-width centered">
		<div class="row-fluid">
			<div id="pre_content" class="span12"><?php echo $pines->page->render_modules('pre_content', 'module_header'); ?></div>
		</div>
	</div>
	<div id="column_container">
		<div class="container-fluid page-width centered">
			<div class="row-fluid">
				<?php if (in_array($pines->config->tpl_pines->variant, array('default', 'twocol-sideleft'))) { ?>
				<div id="left" class="span2">
					<?php echo $pines->page->render_modules('left', 'module_left'); ?>
					<?php if ($pines->config->tpl_pines->variant == 'twocol-sideleft') { echo $pines->page->render_modules('right', 'module_left'); } ?>&nbsp;
				</div>
				<?php } ?>
				<div class="<?php echo $pines->config->tpl_pines->variant == 'full-page' ? 'span12' : ($pines->config->tpl_pines->variant == 'default' ? 'span8' : 'span10'); ?>">
					<div id="content_container">
						<div id="breadcrumbs"><?php echo $pines->page->render_modules('breadcrumbs', 'module_header'); ?></div>
						<div class="row-fluid">
							<div id="content_top_left" class="span6"><?php echo $pines->page->render_modules('content_top_left'); ?></div>
							<div id="content_top_right" class="span6"><?php echo $pines->page->render_modules('content_top_right'); ?></div>
						</div>
						<div id="content"><?php echo $pines->page->render_modules('content', 'module_content'); ?></div>
						<div class="row-fluid">
							<div id="content_bottom_left" class="span6"><?php echo $pines->page->render_modules('content_bottom_left'); ?></div>
							<div id="content_bottom_right" class="span6"><?php echo $pines->page->render_modules('content_bottom_right'); ?></div>
						</div>
					</div>
				</div>
				<?php if (in_array($pines->config->tpl_pines->variant, array('default', 'twocol-sideright'))) { ?>
				<div id="right" class="span2">
					<?php if ($pines->config->tpl_pines->variant == 'twocol-sideright') { echo $pines->page->render_modules('left', 'module_right'); } ?>
					<?php echo $pines->page->render_modules('right', 'module_right'); ?>&nbsp;
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<div class="container-fluid page-width centered">
		<div class="row-fluid">
			<div id="post_content" class="span12"><?php echo $pines->page->render_modules('post_content', 'module_header'); ?></div>
		</div>
	</div>
	<div id="footer" class="ui-widget-header">
		<div class="container-fluid page-width centered">
			<div class="row-fluid">
				<div class="span12">
					<div id="footer_position"><?php echo $pines->page->render_modules('footer', 'module_header'); ?></div>
					<p id="copyright"><?php echo htmlspecialchars($pines->config->copyright_notice, ENT_COMPAT, '', false); ?></p>
				</div>
			</div>
		</div>
	</div>
	<div id="bottom"><?php echo $pines->page->render_modules('bottom', 'module_header'); ?></div>
</body>
</html>