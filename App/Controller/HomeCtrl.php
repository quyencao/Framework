<?php

namespace App\Controller;

use Lib\Controller;

class HomeCtrl extends CoreCtrl {

    function index() {
        $this->resp->redirect(url('/admin/user'));
    }

    function configJS() {
        $config = json_encode(array(
            'siteUrl' => url()
        ));

        $this->resp->setBody("var CONFIG = $config;");
    }

}
