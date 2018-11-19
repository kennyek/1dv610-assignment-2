<?php

require_once 'models/AbstractUserCredential.php';
require_once 'models/CustomExceptions/PasswordHasTooFewCharactersException.php';
require_once 'models/CustomExceptions/PasswordIsEmptyException.php';

class Password extends AbstractUserCredential
{
    public function __construct(string $password = '')
    {
        $this->minimumLength = 6;
        parent::__construct($password, new PasswordIsEmptyException(), new PasswordHasTooFewCharactersException());
    }
}
