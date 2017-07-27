<?php

use Lib\MvcContext;

$routes[] = new MvcContext(array('/', '/admin(/)'), 'GET', "App\\Controller\HomeCtrl", 'index');

$routes[] = new MvcContext('/admin/config.js', 'GET', "App\\Controller\HomeCtrl", 'configJS');

$routes[] = new MvcContext('/admin/user', 'GET', "App\\Controller\UserCtrl", 'index');
$routes[] = new MvcContext('/admin/user/import', 'GET,POST', "App\\Controller\UserCtrl", 'importUser');
$routes[] = new MvcContext('/admin/group', 'GET', "App\\Controller\UserCtrl", 'group');


$routes[] = new MvcContext('/admin/application', 'GET', "App\\Controller\SettingCtrl", 'application');
$routes[] = new MvcContext('/admin/setting', 'GET', "App\\Controller\SettingCtrl", 'setting');
$routes[] = new MvcContext('/admin/setting/update', 'POST', "App\\Controller\SettingCtrl", 'update');


$routes[] = new MvcContext('/rest/department/move', 'PUT', "App\\Controller\Rest\UserCtrl", 'moveDepartments');
$routes[] = new MvcContext('/rest/department/:id', 'GET', "App\\Controller\Rest\UserCtrl", 'getDepartment');
$routes[] = new MvcContext('/rest/department/:id', 'PUT', "App\\Controller\Rest\UserCtrl", 'updateDepartment');
$routes[] = new MvcContext('/rest/department', 'DELETE', "App\\Controller\Rest\UserCtrl", 'deleteDepartments');

$routes[] = new MvcContext('/rest/group', 'GET', "App\\Controller\Rest\UserCtrl", 'getGroups');
$routes[] = new MvcContext('/rest/group/:id/user', 'GET', "App\\Controller\Rest\UserCtrl", 'getGroupUsers');
$routes[] = new MvcContext('/rest/group/:id', 'PUT', "App\\Controller\Rest\UserCtrl", 'updateGroup');
$routes[] = new MvcContext('/rest/group', 'DELETE', "App\\Controller\Rest\UserCtrl", 'deleteGroups');

$routes[] = new MvcContext('/rest/basePermission', 'GET', "App\\Controller\Rest\UserCtrl", 'getBasePermissions');

$routes[] = new MvcContext('/rest/user/search', 'GET', "App\\Controller\Rest\UserCtrl", 'search');
$routes[] = new MvcContext('/rest/user/move', 'PUT', "App\\Controller\Rest\UserCtrl", 'moveUsers');
$routes[] = new MvcContext('/rest/user/checkUniqueAccount', 'GET', "App\\Controller\Rest\UserCtrl", 'checkUniqueAccount');
$routes[] = new MvcContext('/rest/user/:id', 'PUT', "App\\Controller\Rest\UserCtrl", 'updateUser');
$routes[] = new MvcContext('/rest/user', 'DELETE', "App\\Controller\Rest\UserCtrl", 'deleteUsers');

$routes[] = new MvcContext('/account/change-password', 'GET,POST', "App\\Controller\AccountCtrl", 'changePassword');
$routes[] = new MvcContext('/account/login', 'GET,POST', "App\\Controller\AccountCtrl", 'index');

$routes[] = new MvcContext('/article', 'GET', "App\\Controller\\MainCtrl", 'index');
$routes[] = new MvcContext('/test', 'GET', "App\\Controller\\MainCtrl", 'test');
$routes[] = new MvcContext('/all', 'GET', "App\\Controller\\MainCtrl", 'allad');

require_once ('routes-test.php');