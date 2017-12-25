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

$container['upload_directory'] = __DIR__ ."/../images/";

//API Routing
$app->get('/', function (Request $request, Response $response) {
 	return $response->withRedirect('http://ec2-13-125-96-30.ap-northeast-2.compute.amazonaws.com', 301);
});

$app->get('/beers', function (Request $request, Response $response) {
	$beer_mapper = new BeerMapper($this->db);
	
	$params = $request->getQueryParams();
	if (count($params) == 0){
		$beers = $beer_mapper->getBeers();
	}
	else{
		$beers = $beer_mapper->getBeerByDetail($params);
	}
	$response->getBody()->write(json_encode($beers, JSON_UNESCAPED_UNICODE));
	return $response;
});
	
$app->post('/beers', function (Request $request, Response $response) {
    $beer_data = $request->getParsedBody();
	
	$beer = new BeerEntity($beer_data);
    $beer_mapper = new BeerMapper($this->db);
    $result = $beer_mapper->save($beer);
	
	$response->getBody()->write(json_encode($result, JSON_UNESCAPED_UNICODE));
	return $response;
});

$app->post('/beers/images', function (Request $request, Response $response) {
    $directory = $this->get('upload_directory') . "beers/";

    $uploadedFiles = $request->getUploadedFiles();
	$filename = $request->getParam("name");

    // handle single input with single file upload
    $uploadedFile = $uploadedFiles['image_file'];
    if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
        $uploadedFile->moveTo($directory . $filename);
    }

	return $response;
});

$app->put('/beers/{beer_id}', function (Request $request, Response $response) {
	$beer_id = $request->getAttribute('beer_id');
    $beer_data = $request->getParsedBody();

    $beer = new BeerEntity($beer_data);
    $beer_mapper = new BeerMapper($this->db);
    $result = $beer_mapper->modify($beer_id, $beer);

    $response->getBody()->write(json_encode($result, JSON_UNESCAPED_UNICODE));
    return $response;
});

$app->get('/beers/comments/{beer_id}', function (Request $request, Response $response) {
    $beer_id = $request->getAttribute('beer_id');
    $beer_comments_mapper = new BeerCommentsMapper($this->db);
	$result = $beer_comments_mapper->getBeerCommentsById($beer_id);
    $response->getBody()->write(json_encode($result, JSON_UNESCAPED_UNICODE));

    return $response;
});

$app->post('/beers/comments/{beer_id}', function (Request $request, Response $response) {
	$beer_comment_data = $request->getParsedBody();
	$beer_id = $request->getAttribute('beer_id');
	$beer_comment_data['beer_id'] = $beer_id;
	$beer_comments_mapper = new BeerCommentsMapper($this->db);
	//echo json_encode($beer_comment_data);
   	$response->getBody()->write($beer_comments_mapper->save($beer_comment_data));

    return $response;
});

$app->delete('/beers/comments/{beer_id}', function (Request $request, Response $response) {
    $beer_comment_data = $request->getParsedBody();
    $beer_id = $request->getAttribute('beer_id');
	$id = $beer_comment_data['id'];
    $pass = $beer_comment_data['password'];
	
	#return $beer_id . " " . $id . " " . $pass;

    $beer_comments_mapper = new BeerCommentsMapper($this->db);
    $response->getBody()->write($beer_comments_mapper->del($beer_id, $id, $pass));

    return $response;
});

$app->get('/beers/seller/{seller_id}', function (Request $request, Response $response) {
    $seller_id = $request->getAttribute('seller_id');
    $sfb_mapper = new SellerForBeerMapper($this->db);
    $result = $sfb_mapper->getBeerBySeller($seller_id);
    $response->getBody()->write(json_encode($result, JSON_UNESCAPED_UNICODE));

    return $response;
});

$app->get('/sellers/beer/{beer_id}', function (Request $request, Response $response) {
    $beer_id = $request->getAttribute('beer_id');
    $sfb_mapper = new SellerForBeerMapper($this->db);
    $result = $sfb_mapper->getSellerByBeer($beer_id);
    $response->getBody()->write(json_encode($result, JSON_UNESCAPED_UNICODE));

    return $response;
});

$app->get('/sellers', function (Request $request, Response $response) {
    $seller_mapper = new SellerMapper($this->db);

    $params = $request->getQueryParams();
    if (count($params) == 0){
        $sellers = $seller_mapper->getSellers();
    }
    else{
        $sellers = $seller_mapper->getSellerByDetail($params);
    }
    $response->getBody()->write(json_encode($sellers, JSON_UNESCAPED_UNICODE));
    return $response;
});

$app->post('/sellers', function (Request $request, Response $response) {
	$seller = $request->getParsedBody();
    $seller_mapper = new SellerMapper($this->db);
    $result = $seller_mapper->save($seller);

    $response->getBody()->write(json_encode($result, JSON_UNESCAPED_UNICODE));
    return $response;
});

$app->post('/sellers/images', function (Request $request, Response $response) {
    $directory = $this->get('upload_directory') . "sellers/";

    $uploadedFiles = $request->getUploadedFiles();
    $filename = $request->getParam("name");

    // handle single input with single file upload
    $uploadedFile = $uploadedFiles['image_file'];
    if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
        $uploadedFile->moveTo($directory . $filename);
    }

    return $response;
});

$app->put('/sellers/{seller_id}', function (Request $request, Response $response) {
    $seller_id = $request->getAttribute('seller_id');
    $seller = $request->getParsedBody();

    $seller_mapper = new SellerMapper($this->db);
    $result = $seller_mapper->modify($seller_id, $seller);

    $response->getBody()->write(json_encode($result, JSON_UNESCAPED_UNICODE));
    return $response;
});


$app->get('/sellers/comments/{seller_id}', function (Request $request, Response $response) {
    $seller_id = $request->getAttribute('seller_id');
    $seller_comments_mapper = new SellerCommentsMapper($this->db);
    $result = $seller_comments_mapper->getSellerCommentsById($seller_id);
    $response->getBody()->write(json_encode($result, JSON_UNESCAPED_UNICODE));

    return $response;
});

$app->post('/sellers/comments/{seller_id}', function (Request $request, Response $response) {
    $seller_comment_data = $request->getParsedBody();
    $seller_id = $request->getAttribute('seller_id');
    $seller_comment_data['seller_id'] = $seller_id;
    $seller_comments_mapper = new SellerCommentsMapper($this->db);
    //echo json_encode($seller_comment_data);
    $response->getBody()->write($seller_comments_mapper->save($seller_comment_data));

    return $response;
});


$app->delete('/sellers/comments/{seller_id}', function (Request $request, Response $response) {
    $seller_comment_data = $request->getParsedBody();
    $seller_id = $request->getAttribute('seller_id');
    $id = $seller_comment_data['id'];
    $pass = $seller_comment_data['password'];

    #return $seller_id . " " . $id . " " . $pass;

    $seller_comments_mapper = new SellerCommentsMapper($this->db);
    $response->getBody()->write($seller_comments_mapper->del($seller_id, $id, $pass));

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
