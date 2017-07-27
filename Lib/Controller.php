<?php

namespace Lib;

abstract class Controller {

    /** @var MvcContext */
    protected $context;

    /** @var \Slim\Http\Request */
    protected $req;

    /** @var \Slim\Http\Response */
    protected $resp;

    /** @var \Lib\Session */
    protected $session;

    /** @var \App\Model\StorageMapper */
    protected $storageMapper;

    /** @var \App\View\Layout\NoLayout */
    protected $noLayout;

    function __construct(MvcContext $context) {
        $this->context = $context;
        $this->req = $context->app->slim->request;
        $this->resp = $context->app->slim->response;
        $this->session = new \Lib\Session($context);

        $this->init();
    }

    /** run after __construct, tobe overrided */
    protected function init() {
        $this->noLayout = new \App\View\Layout\NoLayout($this->context);
        $this->noLayout->setTemplatesDirectory(BASE_DIR . '/App/View');
    }

    protected function escape($str) {
        $str = stripslashes($str);
        $arr_search = array('&', '<', '>', '"', "'");
        $arr_replace = array('&amp;', '&lt;', '&gt;', '&#34;', '&#39;');
        $str = str_replace($arr_search, $arr_replace, $str);

        return $str;
    }

    protected function getCookie($name) {
        return call_user_func(array($this->context->app->slim, 'getCookie'), $name);
    }

    protected function setCookie($name, $value) {
        return call_user_func(array($this->context->app->slim, 'setCookie'), $name, $value);
    }
    
    function input($key = null, $default = null)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input)
        {
            $input = $_REQUEST;
        }
        if ($key === null)
        {
            return $input;
        }
        return isset($input[$key]) ? $input[$key] : $default;
    }

}
