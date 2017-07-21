<?php
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Phalcon\Di\FactoryDefault;
use Phalcon\Http\Response;
use Phalcon\Loader;
use Phalcon\Mvc\Micro;
// Use Loader() to autoload our model

//registramos el directorio models
$loader = new Loader();
$loader->registerDirs([
	'models',
])->register();

$di = new FactoryDefault();

// conexion a la bd
$di->set(
	'db',
	function () {
		return new PdoMysql(
			[
				'host' => 'localhost',
				'username' => 'root',
				'password' => '',
				'dbname' => 'example',
			]
		);
	}
);

// Crear y pasar DI para la aplicacion

$app = new Phalcon\Mvc\Micro($di);

// listar todos los robot
$app->get(
	'/api/robots',
	function () use ($app) {
		$robots = Robots::find("soft_delete = 0");

		foreach ($robots as $robot) {
			$data[] = [
				'id' => $robot->id,
				'name' => $robot->name,
				'type' => $robot->type,

			];
		}
		echo json_encode($data);

	}
);

// insert inuevo robot
$app->post(
	'/api/robots',
	function () use ($app) {
		$roboto = $app->request->getJsonRawBody();

		$robots = new Robots();
		$robots->name = $roboto->name;
		$robots->type = $roboto->type;
		$robots->year = $roboto->year;

		// crea una repuesta
		$response = new Response();

		if ($robots->save()) {
			// chequea si la insercion fue exitosa

			//  HTTP status
			$response->setStatusCode(201, 'Created');

			$response->setJsonContent(
				[
					'robots' => 'OK',
					'data' => $robot,
				]
			);
		} else {
			// si no fue exitosa manda un estatusn HTTP
			$response->setStatusCode(409, 'Conflict');

			// enviar error a el cliente
			$errors = [];

			foreach ($robots->getMessages() as $message) {
				$errors[] = $message->getMessage();
			}

			$response->setJsonContent(
				[
					'status' => 'ERROR',
					'messages' => $errors,
				]
			);
		}

		return $response;
	}
);
// actualiza por el id
$app->put(
	'/api/robots/{id:[0-9]+}',
	function ($id) use ($app) {
		$roboto = $app->request->getJsonRawBody();

		$robot = Robots::findFirstByid($id);
		$robot->name = $roboto->name;
		$robot->type = $roboto->type;
		$robot->year = $roboto->year;

		// crea una repuesta
		$response = new Response();

		// chequea si la insercion es exitosa
		if ($robot->save()) {
			$response->setJsonContent(
				[
					'status' => 'OK',
				]
			);
		} else {
			// envia repuesta negativa
			$response->setStatusCode(409, 'Conflict');

			$errors = [];

			foreach ($robot->getMessages() as $message) {
				$errors[] = $message->getMessage();
			}

			$response->setJsonContent(
				[
					'status' => 'ERROR',
					'messages' => $errors,
				]
			);
		}

		return $response;
	});

// eliminar robot por id
$app->delete(
	'/api/robots/{id:[0-9]+}',
	function ($id) use ($app) {

		$robot = Robots::findFirstByid($id);
		$robot->soft_delete = 1;

		// crea una repuesta
		$response = new Response();

		// chequea si  es exitosa
		if ($robot->save()) {
			$response->setJsonContent(
				[
					'status' => 'OK',
				]
			);
		} else {
			// Change the HTTP status
			$response->setStatusCode(409, 'Conflict');

			$errors = [];

			foreach ($status->getMessages() as $message) {
				$errors[] = $message->getMessage();
			}

			$response->setJsonContent(
				[
					'status' => 'ERROR',
					'messages' => $errors,
				]
			);
		}

		return $response;
	}
);
$app->handle();
?>