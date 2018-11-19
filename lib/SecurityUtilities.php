<?php

class SecurityUtilities
{
    private static $unsafeCharacters = ['<', '>', '/'];

    public static function createRandomStringOfLength(int $length = 30): string
    {
        $characters =
            'ABCDEFGHIJKLMNOPQRSTUVWXYZ' .
            'abcdefghijklmnopqrstuvwxyz' .
            '0123456789';

        $randomString = '';
        $minIndex = 0;
        $maxIndex = strlen($characters) - 1;

        for ($i = $minIndex; $i < $length; $i++) {
            $randomIndex = rand($minIndex, $maxIndex);
            $randomCharacter = substr($characters, $randomIndex, 1);
            $randomString .= $randomCharacter;
        }

        return $randomString;
    }

    public static function encryptString(string $unencryptedString): string
    {
        $algorithm = PASSWORD_BCRYPT;
        $options = ['cost' => 10];
        $hash = password_hash($unencryptedString, $algorithm, $options);

        // If password_hash fails, call this function recursively to fix
        $hash = $hash ?? encryptString($unencryptedString);

        return $hash;
    }

    public static function hasUnsafeCharacters(string $toAnalyze)
    {
        $hasUnsafeCharacters = false;

        foreach (self::$unsafeCharacters as $character) {
            if (strpos($toAnalyze, $character) !== false) {
                $hasUnsafeCharacters = true;
                break;
            }
        }
        
        return $hasUnsafeCharacters;
    }

    public static function removeUnsafeCharactersFromString(string $unsafe): string
    {
        return strip_tags($unsafe);
    }
}
