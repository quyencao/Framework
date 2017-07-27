<?php

namespace App\View\Layout;

use Lib\Layout;

class ContentOnlyLayout extends Layout {

    protected $title = 'Cores';
    protected $brand;

    public function themeUrl() {
        return url('/themes/nifty');
    }

    function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    function setBrand($brand) {
        $this->brand = $brand;
        return $this;
    }

    protected function renderLayout($content) {
        ?>

        <!DOCTYPE html>
        <html lang="vi">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link href="<?php echo $this->themeUrl() ?>/icon.ico" rel="shortcut icon">
                <title><?php echo $this->title ?></title>

                <!--Open Sans Font [ OPTIONAL ] -->
                <link href="<?php echo $this->themeUrl() ?>/css/font/font.css" rel="stylesheet">

                <!--Bootstrap Stylesheet [ REQUIRED ]-->
                <link href="<?php echo $this->themeUrl() ?>/css/bootstrap.min.css" rel="stylesheet">

                <!--Nifty Stylesheet [ REQUIRED ]-->
                <link href="<?php echo $this->themeUrl() ?>/css/nifty.min.css" rel="stylesheet">

                <!--Font Awesome [ OPTIONAL ]-->
                <link href="<?php echo $this->themeUrl() ?>/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">

                <!--Custom-->
                <link href="<?php echo $this->themeUrl() ?>/css/demo/nifty-demo.min.css" rel="stylesheet">

                <link href="<?php echo $this->themeUrl() ?>/css/style.css" rel="stylesheet">

                <!--jQuery [ REQUIRED ]-->
                <script src="<?php echo $this->themeUrl() ?>/js/jquery-2.1.1.min.js"></script>
            </head>

            <!--TIPS-->
            <!--You may remove all ID or Class names which contain "demo-", they are only used for demonstration. -->

            <body>
                <div id="container" class="cls-container">
        <?php echo $content ?>
                </div>
                <!--===================================================-->
                <!-- END OF CONTAINER -->



                <!--BootstrapJS [ RECOMMENDED ]-->
                <script src="<?php echo $this->themeUrl() ?>/js/bootstrap.min.js"></script>

                <!--Nifty Admin [ RECOMMENDED ]-->
                <script src="<?php echo $this->themeUrl() ?>/js/nifty.min.js"></script>


                <!--Switchery [ OPTIONAL ]-->
                <script src="<?php echo $this->themeUrl() ?>/plugins/switchery/switchery.min.js"></script>

                <script src="<?php echo $this->themeUrl() ?>/js/angular.min.js"></script>

                <!--Bootstrap Validator [ OPTIONAL ]-->
                <script src="<?php echo $this->themeUrl() ?>/plugins/bootstrap-validator/bootstrapValidator.min.js"></script>

                <script src="<?php echo url('/admin/config.js') ?>"></script>


                <script src="<?php echo $this->themeUrl() ?>/js/app.js"></script>
            </body>
        </html>

        <?php
    }

}
