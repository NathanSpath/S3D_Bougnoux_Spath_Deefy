<?php

namespace iutnc\deefy\exception;

class AuthnException extends \Exception
{
    public function __construct(string $property) {
        parent::__construct($property);
    }

}