<?php
require 'vendor/autoload.php';
$app = new \Slim\Slim();
$app->get('/items', function () use ($app) {
	// connect
	$m = new MongoClient();

	// select a database
	$db = $m->priorities;

	// select a collection (analogous to a relational database's table)
	$collection = $db->shopping_list;

	// find everything in the collection
	$cursor = $collection->find();

	// iterate through the results
	foreach ($cursor as $document) {
		//echo $document["name"] . "\n";
		d($document);
	}
});
$app->post('/item/:name', function ($name) use ($app) {
	// connect
	$m = new MongoClient();

	// select a database
	$db = $m->priorities;

	// select a collection (analogous to a relational database's table)
	$collection = $db->shopping_list;

	// add a record
	$document = array( "name" => $name );
	$collection->insert($document);

	/*
	// add another record, with a different "shape"
	$document = array( "title" => "XKCD", "online" => true );
	$collection->insert($document);
	*/
	$app->response()->status(201);
	echo "Created.";
});
$app->run();
?>
