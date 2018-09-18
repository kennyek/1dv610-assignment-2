<?php

/** Set of static methods to use for security purposes. */
class SecurityUtilities
{
    /**
     * Creates a random string of a specific lenght.
     *
     * @param int $length (optional) - The length of the string. Defaults to 30.
     * @return string A random string of a specific length.
     */
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

    /**
     * Creates an encrypted version of a string. Can be used to encrypt
     * sensitive data such as passwords. Use password_verify to check the plain
     * string against the encrypted string.
     *
     * @param string $unencryptedString - The string to encrypt.
     * @return string An encrypted version of the provided string.
     */
    public static function encryptString(string $unencryptedString): string
    {
        $algorithm = PASSWORD_BCRYPT;
        $options = ['cost' => 10];
        $hash = password_hash($unencryptedString, $algorithm, $options);

        // If password_hash fails, call this function recursively to fix
        $hash = $hash ?? encryptString($unencryptedString);

        return $hash;
    }
}
