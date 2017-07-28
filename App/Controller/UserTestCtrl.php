<?php
/**
 * Created by PhpStorm.
 * User: LaptopTCC
 * Date: 7/27/2017
 * Time: 9:12 AM
 */

namespace App\Controller;

use App\Controller\CoreCtrl;

class UserTestCtrl extends CoreCtrl
{
    public function userTest() {
        $this->requireAdmin();
        $this->twoColsLayout->setSideMenuActive('user-test')->render('UserTest/user-test.phtml');
//        $this->resp->setBody('<h1>Test</h1>');
//        $this->twoColsLayout->render('');
    }
}