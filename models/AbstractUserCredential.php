<?php

abstract class AbstractUserCredential
{
    /** @var string */
    private $value;

    /** @var int */
    protected $minimumLength;

    public function __construct(string $value, Exception $missingValue, Exception $tooFewCharacters)
    {
        $this->throwExceptionOnInvalidValue($value, $missingValue, $tooFewCharacters);
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getMinimumLength(): int
    {
        return $this->minimumLength;
    }

    private function throwExceptionOnInvalidValue(string $value, Exception $missingValue, Exception $tooFewCharacters)
    {
        $length = $this->getTrimmedLengthOfValue($value);
        $this->throwExceptionOnMissingValue($length, $missingValue);
        $this->throwExceptionOnTooFewCharacters($length, $tooFewCharacters);
    }

    private function getTrimmedLengthOfValue(string $value): int
    {
        return strlen(trim($value));
    }

    private function throwExceptionOnMissingValue(int $length, Exception $missingValue)
    {
        if ($length === 0) {
            throw $missingValue;
        }
    }

    private function throwExceptionOnTooFewCharacters(int $length, Exception $tooFewCharacters)
    {
        if ($length < $this->minimumLength) {
            throw $tooFewCharacters;
        }
    }
}
