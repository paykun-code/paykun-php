<?php

namespace Paykun\Checkout\Errors;
require_once ('Error.php');
class ValidationException extends Error
{
    protected $field = null;

    public function __construct($message, $code, $field = null)
{
    parent::__construct($message, $code);

    $this->field = $field;
}

    public function getField()
    {
        return $this->field;
    }
}