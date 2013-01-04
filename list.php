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

$app->get('/items(/:priority)', function ($priority = NULL) use ($app) {
	$collection = getDBCollection();
	if ($priority === NULL)
	{
		// find everything in the collection
		$cursor = $collection->find();
	}
	else
	{
		//Find only this priority or less (higher)
		$priority = (int)$priority;
		$cursor = $collection->find(array(
			"priority" => array(
				'$lte' => $priority
			)
		));
	}

	// iterate through the results
	if ($priority === NULL)
	{
		$documents = iterator_to_array($cursor);
	}
	else
	{
		$documents = array();
		$too_old = (time() - 432000); //5 days ago
		foreach ($cursor as $document) {
			//If this item was modified less then x days ago or is a p1...
			if ((int)$document['last_modified'] > $too_old || (int)$document['priority'] === 1)
			{
				$documents[] = $document;
			}
		}
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

$app->post('/remove/item/:id', function ($id) use ($app) {
	$collection = getDBCollection();
	$status = $collection->remove(array(
		"_id" => $id
	));
	$app->response()->status(200);
	echo "Removed item.";
});

$app->put('/item/:name(/:priority)', function ($name, $priority = 3) use ($app) {
	$collection = getDBCollection();
	$priority = (int)$priority;
	// add a record
	$document = array(
		"name" => $name,
		"last_modified" => time(),
		"priority" => $priority,
	);
	try
	{
		$status = $collection->insert($document);
	} catch (MongoCursorException $e)
	{
		$app->response()->status(409);
		echo "Not created. Duplicate item name: " . $name;
		return;
	}
	$app->response()->status(201);
	echo "Created " . $name . ".";
});

$app->post('/item/:id', function ($id) use ($app) {
	$collection = getDBCollection();
	// update a record
	$criteria = array(
		"_id" => new MongoId($id)
	);
	$document = array();
	foreach ($app->request()->params() as $key=>$val)
	{
		$document[$key] = $val;
	}
	unset($document['_id']);
	$document['last_modified'] = time();
	try
	{
		$status = $collection->update($criteria, $document);
	} catch (MongoCursorException $e)
	{
		$app->response()->status(500);
		echo "error message: ".$e->getMessage()."\n";
		echo "error code: ".$e->getCode()."\n";
		return;
	}
	$app->response()->status(202);
	echo "Updated.";
});

$app->run();
?>
