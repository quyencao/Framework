<?php

define('BASE_DIR', dirname(__DIR__));

require_once BASE_DIR . '/Lib/Slim/Slim.php';
require_once BASE_DIR . '/Lib/Fn.php';

//autoload
require_once BASE_DIR . '/Lib/autoload.php';

//khoi tao ung dung
$application = new \Lib\Bootstrap();

