<?php

namespace Core;

class Response {
    public $content;
    public $headers;

    public function __construct() {
        $this->headers = [];
    }

    public function setContent($content) {
        $this->content = $content;
    }

    public function addHeader($header) {
        $this->headers[] = $header;
    }

    public function send() {
        foreach ($this->headers as $header) {
            header($header);
        }
        echo $this->content;
    }

    public function setRedirect(string $path) {
        
        $this->addHeader("Location: $path");

    }
}