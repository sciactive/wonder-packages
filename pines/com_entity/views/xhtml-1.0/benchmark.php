<?php
/**
 * Runs a benchmark of the entity manager.
 *
 * @package Pines
 * @subpackage com_entity
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html
 * @author Hunter Perrin <hunter@sciactive.com>
 * @copyright Hunter Perrin
 * @link http://sciactive.com/
 *
 * @todo Finish the benchmarking utility.
 */
defined('P_RUN') or die('Direct access prohibited');
$this->title = 'Entity Manager Benchmark';
?>
<form class="pform" method="post" action="<?php echo pines_url('com_entity', 'benchmark'); ?>">
	<div class="element heading">
		<p>
			This entity manager benchmark will create, retrieve and delete
			1,000,000 entities and display the timing results here. It may take
			a <strong>VERY</strong> long time to complete. Are you sure you want
			to proceed?
		</p>
	</div>
	<div class="element buttons">
		<input type="hidden" name="sure" value="yes" />
		<input class="button ui-state-default ui-priority-primary ui-corner-all" type="submit" value="Yes, proceed." />
	</div>
</form>