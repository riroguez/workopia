<?php 
require_once __DIR__ . '/../vendor/autoload.php';
use Framework\Router;
use Framework\Session;

Session::start();

require_once '../helpers.php'; 

#Instantiate the router
$router = new Router();

#Get routes 
$routes = require_once basePath('routes.php');

#Get current URI and HTTP method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

#Route the request
$router->route($uri);

