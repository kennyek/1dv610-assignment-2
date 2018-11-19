<?php

require_once 'models/AbstractUserCredential.php';
require_once 'models/CustomExceptions/UsernameHasTooFewCharactersException.php';
require_once 'models/CustomExceptions/UsernameIsEmptyException.php';

class Username extends AbstractUserCredential
{
    public function __construct(string $username = '')
    {
        $this->minimumLength = 3;
        parent::__construct($username, new UsernameIsEmptyException(), new UsernameHasTooFewCharactersException());
        
        $this->throwExceptionOnUnsafeCharacters($username);
    }

    private function throwExceptionOnUnsafeCharacters(string $username)
    {
        if (SecurityUtilities::hasUnsafeCharacters($username)) {
            throw new UsernameHasInvalidCharactersException();
        }
    }
}
