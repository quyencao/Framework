<?php

use Lib\MvcContext;

$routes[] = new MvcContext('/admin/test', 'GET', "App\\Controller\\UserTestCtrl", 'userTest');
$routes[] = new MvcContext('/rest/userTest/search', 'GET', "App\\Controller\Rest\UserTestCtrl", 'getUsersTest');
$routes[] = new MvcContext('/rest/userTest', 'DELETE', "App\\Controller\Rest\UserTestCtrl", 'deleteUsersTest');
$routes[] = new MvcContext('/rest/userTest/:id', 'PUT', "App\\Controller\Rest\UserTestCtrl", 'updateUsersTest');

//$routes[] = new MvcContext('/rest/test/dept/:id', 'GET', "App\\Controller\Rest\UserTestCtrl", 'getDepartment');
//$routes[] = new MvcContext('/admin/test2', 'GET', "App\\Controller\\Rest\\UserTestCtrl", 'getUsersTest');