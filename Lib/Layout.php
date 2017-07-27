<?php

namespace Lib;

abstract class Layout extends View {

    protected $js = array();
    protected $css = array();
    
    function __construct(MvcContext $context) {
        parent::__construct($context);
        $this->setTemplatesDirectory(BASE_DIR.'/App/View');
    }

    function render($template, $data = array()) {
        $content = parent::getOutput($template, $data);
        $this->renderLayout($content);
    }

    abstract function themeUrl();

    abstract protected function renderLayout($content);

    function addJs($arr) {
        if (!is_array($arr)) {
            $arr = array($arr);
        }
        $this->js = array_merge($this->js, $arr);
        return $this;
    }

    function addCss($arr) {
        if (!is_array($arr)) {
            $arr = array($arr);
        }
        $this->css = array_merge($this->css, $arr);
        return $this;
    }

}
