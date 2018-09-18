<?php

class Cookie
{
    /**
     * Validates provided credentials against the database. Throws exception if
     * there is no match.
     * 
     * TODO: Refactor to smaller functions.
     *
     * @param $username - The username to check for in the database.
     * @param $password - The password to match against the username in the
     * database.
     * @throws Exception Wrong information in cookies
     * @return void
     */
    public static function retrieveCookieFromDatabase(string $username, string $password)
    {
        $wrongInformationInCookies = 'Wrong information in cookies';

        $databaseConnection = new DatabaseConnection();
        $connection = $databaseConnection->getConnection();

        $escapedUsername = $connection->real_escape_string($username);
        $escapedPassword = $connection->real_escape_string($password);
        
        $query =
            "SELECT * FROM cookies " .
            "WHERE username LIKE ? " .
            "AND password LIKE ?";

        $preparedStatement = $connection->prepare($query);
        $preparedStatement->bind_param('ss', $escapedUsername, $escapedPassword);
        $preparedStatement->execute();

        $result = $preparedStatement->get_result();
        $fetchedCookieRow = $result->fetch_assoc();

        $preparedStatement->close();

        if (empty($fetchedCookieRow)) {
            throw new Exception($wrongInformationInCookies);
        }
    }

    /**
     * Inserts a cookie to the database.
     * 
     * TODO: Perhaps add some exception handling.
     *
     * @param string $username - The username to be put in the database. Should
     * match an existing user in the database.
     * @param string $password - The cookie password to be put in the database.
     * @return void
     */
    public static function insertCookieIntoDatabase(string $username, string $password)
    {
        $databaseConnection = new DatabaseConnection();
        $connection = $databaseConnection->getConnection();
        
        $browser = $_SERVER['HTTP_USER_AGENT'];
        $escapedUsername = $connection->real_escape_string($username);
        $escapedPassword = $connection->real_escape_string($password);
        $escapedBrowser = $connection->real_escape_string($browser);

        $query =
            "INSERT INTO cookies (username, password, browser) " .
            "VALUES (?, ?, ?)";

        $preparedStatement = $connection->prepare($query);
        $preparedStatement->bind_param('sss', $escapedUsername, $escapedPassword, $escapedBrowser);
        $preparedStatement->execute();

        $preparedStatement->close();
    }
}
