<?php

namespace App\View\Setting;

abstract class Control {

    /** @var \SimpleXmlElement */
    protected $dom;

    function __construct(\SimpleXmlElement $dom) {
        $this->dom = $dom;
    }

    abstract function __toString();

    abstract function handleValue($userInput);
}
