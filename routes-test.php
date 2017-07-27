<?php

use Lib\MvcContext;

$routes[] = new MvcContext('/admin/test', 'GET', "App\\Controller\\UserTestCtrl", 'userTest');
$routes[] = new MvcContext('/rest/test', 'GET', "App\\Controller\Rest\UserTestCtrl", 'getUsersTest');
$routes[] = new MvcContext('/rest/test', 'DELETE', "App\\Controller\Rest\UserTestCtrl", 'deleteUsersTest');
$routes[] = new MvcContext('/rest/test/:id', 'PUT', "App\\Controller\Rest\UserTestCtrl", 'updateUsersTest');
//$routes[] = new MvcContext('/admin/test2', 'GET', "App\\Controller\\Rest\\UserTestCtrl", 'getUsersTest');