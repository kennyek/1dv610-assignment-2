<?php

class RegistrationExceptions extends Exception
{
    private $exceptions = [];

    public function addException(Exception $exception)
    {
        $this->exceptions[] = $exception;
    }

    public function getExceptions(): array
    {
        return $this->exceptions;
    }
}