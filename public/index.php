<?php 
use Framework\Router;

require_once '../helpers.php'; 
require_once __DIR__ . '/../vendor/autoload.php';

#Instantiate the router
$router = new Router();

#Get routes 
$routes = require_once basePath('routes.php');

#Get current URI and HTTP method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

#Route the request
$router->route($uri);

