<?php

namespace Core;


class Request {
    private $url;
    private $method;
    private $parameter;

    public function __construct($url, $method, $parameter = []) {
        $this->url = $url;
        $this->method = $method;
        $this->parameter = $parameter;
    }

    public function getUrl() {
        return $this->url;
    }

    public function getMethod() {
        return $this->method;
    }

    public function getParameters() {
        return $this->parameter;
    }
}
