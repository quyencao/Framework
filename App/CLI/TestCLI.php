<?php

namespace App\CLIs;

use Lib\CLI;

class TestCLI extends CLI {

    function index($args) {
        var_dump($args);
    }

}
