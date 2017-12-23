<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../../vendor/autoload.php';

//Slim DI Settings
$config['displayErrorDetails'] = true;
$config['db']['host']   = "localhost";
$config['db']['user']   = "ubuntu";
$config['db']['pass']   = "ubuntu";
$config['db']['dbname'] = "macktongbub";

$app = new \Slim\App(["settings" => $config]);
$container = $app->getContainer();

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

//API Routing
$app->get('/', function (Request $request, Response $response) {
 	return $response->withRedirect('http://ec2-13-125-96-30.ap-northeast-2.compute.amazonaws.com', 301);
});

$app->get('/beers', function (Request $request, Response $response) {
	$beer_mapper = new BeerMapper($this->db);
	$beers = $beer_mapper->getBeers();

	foreach ($beers as $beer){
		$response->getBody()->write(json_encode($beer->getEntities(), JSON_UNESCAPED_UNICODE));	
	}
	return $response;
});
	
$app->post('/beers', function (Request $request, Response $response) {
    $beer_data = $request->getParsedBody();
	#$response->getBody()->write($beer_data['image']);
	$beer = new BeerEntity($beer_data);
    $beer_mapper = new BeerMapper($this->db);
    $result = $beer_mapper->save($beer);

	$response->getBody()->write("Successfully saved data.\n");
	return $response;
});

$app->get('/sellers', function (Request $request, Response $response) {
    $response->getBody()->write("seller get");
	return $response;
});

$app->post('/sellers', function (Request $request, Response $response) {
    $response->getBody()->write("seller post");
	return $response;
});

$app->get('/breweries', function (Request $request, Response $response) {
    $response->getBody()->write("breweries get");
	return $response;
});

$app->post('/breweries', function (Request $request, Response $response) {
	$response->getBody()->write("breweries post");
	return $response;
});
		
$app->run();
?>
