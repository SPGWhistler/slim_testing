<?php
require 'vendor/autoload.php';

function getDBCollection()
{
	static $collection = NULL;
	if (!isset($collection))
	{
		// connect
		$m = new MongoClient();
		// select a database
		$db = $m->priorities;
		// select a collection (analogous to a relational database's table)
		$collection = $db->shopping_list;
	}
	return $collection;
}

$app = new \Slim\Slim();

$app->get('/items', function () use ($app) {
	$collection = getDBCollection();
	// find everything in the collection
	$cursor = $collection->find();

	// iterate through the results
	$documents = array();
	foreach ($cursor as $document) {
		//echo $document["name"] . "\n";
		//print_r($document);
		//d($document);
		$documents[] = $document;
	}
	echo json_encode($documents);
	$res = $app->response();
	$res['Content-Type'] = 'application/json';
	$res->status(200);
});

$app->post('/remove/items', function () use ($app) {
	$collection = getDBCollection();
	$collection->drop();
	$app->response()->status(200);
	echo "Removed all items.";
});

$app->post('/item/:name(/:priority)', function ($name, $priority = 3) use ($app) {
	$collection = getDBCollection();
	$priority = (int)$priority;
	// add a record
	$document = array(
		"name" => $name,
		"last_modified" => date('U'),
		"priority" => $priority,
	);
	$collection->insert($document);
	$app->response()->status(201);
	echo "Created " . $name . ".";
});

$app->run();
?>
