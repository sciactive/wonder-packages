<?php
/**
 * Run a benchmark.
 *
 * @package Components\entitytools
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $_ core */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_entitytools/benchmark') )
	punt_user(null, pines_url('com_entitytools', 'benchmark'));

set_time_limit(3600);

$benchmark->time_start = microtime(true);

// Creating 1000 entities...
$pass = true;
for ($i=0; $i<1000; $i++) {
	$entity = Entity::factory();
	$entity->addTag('com_entitytools', 'benchmark');
	$entity->name = "Entity Benchmark ".time();
	$entity->int = 1000;
	$entity->float = 10.5;
	$entity->null = null;
	$entity->array = array('string', 0, 1.5, null);
	$entity->object = (object) array('string' => 'string', 'int' => 0, 'float' => 1.5, 'null' => null);
	$pass = $entity->save() && $pass;
}
$benchmark->create[0] = $pass;
$benchmark->create[1] = microtime(true);

// Retrieving entities...
$entities = $_->nymph->getEntities(array('limit' => 1000), array('&', 'tag' => array('com_entitytools', 'benchmark')));
$benchmark->retrieve[0] = count($entities) == 1000;
$benchmark->retrieve[1] = microtime(true);

// Deleting entities...
$pass = true;
foreach ($entities as $cur_entity) {
	$pass = $pass && $cur_entity->delete();
}
$benchmark->delete[0] = $pass;
$benchmark->delete[1] = microtime(true);

$benchmark->time_end = microtime(true);

$_->page->ajax(json_encode($benchmark));