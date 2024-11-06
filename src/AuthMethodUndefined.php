<?php

namespace W0s1nsk1\TelescopeElasticsearchDriver;
use Exception;
use Throwable;

class AuthMethodUndefined extends Exception {
    public function __construct($message = "Authorization method is undefined", $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}