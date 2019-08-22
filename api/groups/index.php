<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once '../vendor/autoload.php';
require_once '../../db.php';

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$container = new \Slim\Container;
$app = new \Slim\App($container);

$container = $app->getContainer();
$container['con'] = function ($container) {
	$con = new pdo_db();
	return $con;
};

# list groups
$app->get('/list', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	
	$groups = $con->getData("SELECT * FROM groups");	

    return $response->withJson($groups);

});

# add group
$app->post('/add', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "groups";

	require_once '../../classes.php';
	
	$data = $request->getParsedBody();
	
	$privileges = [];
	if (isset($data['privileges'])) {
		
		$arrayHex = new ArrayHex();
			
		$privileges = $arrayHex->toHex(json_encode($data['privileges']));
		$data['group']['privileges'] = $privileges;
		
	};
	
	unset($data['group']['id']);
	$con->insertData($data['group']);

});

# view group
$app->get('/view/{id}', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "groups";

	$group = $con->get(array("id"=>$args['id']));
	
    return $response->withJson($group[0]);

});

# update group
$app->put('/update', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "groups";

	require_once '../../classes.php';

	$data = $request->getParsedBody();

	$privileges = [];
	if (isset($data['privileges'])) {

		$arrayHex = new ArrayHex();

		$privileges = $arrayHex->toHex(json_encode($data['privileges']));
		$data['group']['privileges'] = $privileges;

	};

	$con->updateData($data['group'],'id');

});

# delete group
$app->delete('/delete/{id}', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "groups";
	
	$group = array("id"=>$args['id']);

	$con->deleteData($group);

});

# privileges
$app->get('/privileges/{id}', function (Request $request, Response $response, array $args) {

	$con = $this->con;
	$con->table = "groups";

	require_once '../../system_privileges.php';
	require_once '../../classes.php';	
	
	$group_privileges = $con->get(array("id"=>$args['id']),["privileges"]);

	if (count($group_privileges)) {
		if ($group_privileges[0]['privileges']!=NULL) {

			$privileges_obj = new privileges(system_privileges,$group_privileges[0]['privileges'],null);
			$privileges = $privileges_obj->getPrivileges();

		} else {
			
			$privileges = system_privileges;		
			
		}
	} else {

		$privileges = system_privileges;	

	}

    return $response->withJson($privileges);

});

$app->get('/privileges/special/{id}', function(Request $request, Response $response, array $args) {
	
	$con = $this->con;
	$con->table = "groups";	
	$group_privileges = $con->get(array("id"=>$args['id']),["privileges"]);

	require_once '../../system_privileges.php';
	require_once '../../classes.php';	

	$con->table = "users";	
	$user_privileges = $con->get(array("id"=>$args['id']),["privileges"]);

	if (count($user_privileges)) {
		if ($user_privileges[0]['privileges']!=NULL) {

			$privileges_obj = new privileges(system_privileges,$user_privileges[0]['privileges'],[]);
			$privileges = $privileges_obj->getPrivileges();

		} else {
			
			$privileges = system_privileges;		
			
		}
	} else {

		$privileges = system_privileges;	

	}

    return $response->withJson($privileges);	
	
});

$app->run();

?>