<?php

namespace App\View\Layout;

use Lib\Layout;
use App\Model\UserEntity;
use Lib\Menu;

class DefaultLayout extends Layout {

    const HTML_REQUIRED = '<span class="required">&#10033;</span>';

    /** @var UserEntity */
    protected $user;
    protected $title = 'Cores';
    protected $brand = 'brand';
    protected $companyWebsite = 'www.companywebsite.com';
    protected $sideMenu;
    protected $themeConfig;
    protected $sideMenuActive;

    public function themeUrl() {
        return url('/themes/nifty');
    }

    function setSideMenuActive($id) {
        $this->sideMenuActive = $id;
        return $this;
    }

    function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    function setBrand($brand) {
        $this->brand = $brand;
        return $this;
    }

    function setCompanyWebsite($url) {
        $this->companyWebsite = $url;
        return $this;
    }

    function setUser($user) {
        $this->user = $user;
        return $this;
    }

    function setSideMenu(Menu $menu) {
        $this->sideMenu = $menu;
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

                <!--Animate.css [ OPTIONAL ]-->
                <link href="<?php echo $this->themeUrl() ?>/plugins/animate-css/animate.min.css" rel="stylesheet">

                <!--Bootstrap Validator [ OPTIONAL ]-->
                <link href="<?php echo $this->themeUrl() ?>/plugins/bootstrap-validator/bootstrapValidator.min.css" rel="stylesheet">

                <!--Custom-->

                <link href="<?php echo $this->themeUrl() ?>/css/style.css" rel="stylesheet">

                <!--SCRIPT-->
                <!--=================================================-->

                <!--jQuery [ REQUIRED ]-->
                <script src="<?php echo $this->themeUrl() ?>/js/jquery-2.1.1.min.js"></script>

        <?php
        foreach ($this->css as $css) {
            echo "\n<link href='$css' rel='stylesheet' type='text/css'>";
        }
        ?>
            </head>
            <body ng-app="ngApp">
                <div id="container" class="effect mainnav-lg">
                    <!--NAVBAR-->
                    <!--===================================================-->
                    <header id="navbar">
                        <div id="navbar-container" class="boxed">

                            <!--Brand logo & name-->
                            <!--================================-->
                            <div class="navbar-header">
                                <a href="<?php echo url() ?>" class="navbar-brand">
                                    <div class="brand-title">
                                        <span class="brand-text"><?php echo $this->brand ?></span>
                                    </div>
                                </a>
                            </div>
                            <!--================================-->
                            <!--End brand logo & name-->


                            <!--Navbar Dropdown-->
                            <!--================================-->
                            <div class="navbar-content clearfix">
                                <ul class="nav navbar-top-links pull-left">

                                    <!--Navigation toogle button-->
                                    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                                    <li class="tgl-menu-btn">
                                        <a class="mainnav-toggle" href="#">
                                            <i class="fa fa-navicon fa-lg"></i>
                                        </a>
                                    </li>
                                    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                                    <!--End Navigation toogle button-->
                                </ul>
                                <ul class="nav navbar-top-links pull-right">
                                    <!--User dropdown-->
                                    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                                    <li id="dropdown-user" class="dropdown">
                                        <a href="#" data-toggle="dropdown" class="dropdown-toggle text-right">
                                            <span class="pull-right">
                                                <img class="img-circle img-user media-object" src="<?php echo $this->themeUrl() ?>/img/av1.png" alt="Profile Picture">
                                            </span>
                                            <div class="username hidden-xs"><?php echo $this->user->fullName ?></div>
                                        </a>


                                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-right with-arrow panel-default">
                                            <!-- User dropdown menu -->
                                            <ul class="head-list">
                                                <li>
                                                    <a href="<?php echo url('/account/change-password') ?>">
                                                        <i class="fa fa-key fa-fw fa-lg"></i> Đổi mật khẩu
                                                    </a>
                                                </li>
                                            </ul>

                                            <!-- Dropdown footer -->
                                            <div class="pad-all text-right">
                                                <a href="<?php echo url('/account/login') ?>" class="btn btn-primary">
                                                    <i class="fa fa-sign-out fa-fw"></i> Đăng xuất
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                                    <!--End user dropdown-->
                                </ul>
                            </div>
                            <!--================================-->
                            <!--End Navbar Dropdown-->
                        </div>
                    </header>
                    <!--===================================================-->
                    <!--END NAVBAR-->

                    <div class="boxed">

                        <!--CONTENT CONTAINER-->
                        <!--===================================================-->
                        <div id="content-container">
                            <!--Page content-->
                            <!--===================================================-->
                            <div id="page-content">
        <?php echo $content ?>
                            </div>
                            <!--===================================================-->
                            <!--End page content-->


                        </div>
                        <!--===================================================-->
                        <!--END CONTENT CONTAINER-->



                        <!--MAIN NAVIGATION-->
                        <!--===================================================-->
                        <nav id="mainnav-container">
                            <div id="mainnav">
                                <!--Menu-->
                                <!--================================-->
                                <div id="mainnav-menu-wrap">
                                    <div class="nano">
                                        <div class="nano-content">
                                            <ul id="mainnav-menu" class="list-group">
                                                <!--Menu list item-->
        <?php foreach ($this->sideMenu->children as $k => $menu): ?>
            <?php
            $active = $this->sideMenuActive == $k ? 'active-link' : '';
            //tách thẻ <i> nếu có
            $matchCount = preg_match('/(<i[^>]*>[^<]*<\/i>)/si', $menu->label, $matches);
            if ($matchCount) {
                $menu->label = str_replace($matches[0], '', $menu->label);
            }
            ?>
                                                    <li class="<?php echo $active ?>">
                                                        <a href="<?php echo $menu->url ?>">
                                                    <?php if ($matchCount) echo $matches[0]; ?>
                                                            <span class="menu-title">
                                                    <?php echo $menu->label ?>
                                                            </span>
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <!--================================-->
                                <!--End menu-->

                            </div>
                        </nav>
                        <!--===================================================-->
                        <!--END MAIN NAVIGATION-->
                    </div>

                    <!-- FOOTER -->
                    <!--===================================================-->
                    <footer id="footer">
                        <!-- Visible when footer positions are static -->
                        <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
                        <div class="hide-fixed pull-right pad-rgt">Currently v2.2</div>



                        <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
                        <!-- Remove the class name "show-fixed" and "hide-fixed" to make the content always appears. -->
                        <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->

                        <p class="pad-lft">&#0169; <?php echo date_create()->format('Y') ?> <?php echo $this->companyWebsite ?></p>



                    </footer>
                    <!--===================================================-->
                    <!-- END FOOTER -->


                    <!-- SCROLL TOP BUTTON -->
                    <!--===================================================-->
                    <button id="scroll-top" class="btn"><i class="fa fa-chevron-up"></i></button>
                    <!--===================================================-->



                </div>
                <!--===================================================-->
                <!-- END OF CONTAINER -->

                <!--JAVASCRIPT-->
                <!--=================================================-->


                <!--BootstrapJS [ RECOMMENDED ]-->
                <script src="<?php echo $this->themeUrl() ?>/js/bootstrap.min.js"></script>

                <!--Nifty Admin [ RECOMMENDED ]-->
                <script src="<?php echo $this->themeUrl() ?>/js/nifty.min.js"></script>


                <!--Switchery [ OPTIONAL ]-->
                <script src="<?php echo $this->themeUrl() ?>/plugins/switchery/switchery.min.js"></script>

                <script src="<?php echo $this->themeUrl() ?>/js/angular.min.js"></script>

                <!--Bootstrap Validator [ OPTIONAL ]-->
                <script src="<?php echo $this->themeUrl() ?>/plugins/bootstrap-validator/bootstrapValidator.min.js"></script>
                
                <script src="<?php echo $this->themeUrl() ?>/js/ui-bootstrap-2.3.0.min.js"></script>

                <script src="<?php echo url('/admin/config.js') ?>"></script>


                <script src="<?php echo $this->themeUrl() ?>/js/app.js"></script>

        <?php
        foreach ($this->js as $js) {
            echo "\n<script src='$js'></script>";
        }
        ?>
            </body>
        </html>

        <?php
    }

}
