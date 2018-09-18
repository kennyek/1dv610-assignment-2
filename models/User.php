<?php

require_once 'models/DatabaseConnection.php';

/** Represents a user account. */
class User
{
    /** @var string */
    private $username = '';

    /** @var string */
    private $password = '';

    /**
     * Fetches a User from the database.
     *
     * @param string $username (optional) - The user's username. Set to an empty
     * string if not provided.
     * @param string $password (optional) - The user's password. Set to an empty
     * string if not provided.
     * @throws Exception Username is missing
     * @throws Exception Password is missing
     * @throws Exception Wrong name or password
     */
    public function __construct(string $username = '', string $password = '')
    {
        if (empty($username)) {
            throw new Exception('Username is missing');
        } else if (empty($password)) {
            throw new Exception('Password is missing');
        }

        try {
            $this->retrieveUserFromDatabase($username, $password);
        } catch (Exception $exception) {
            throw new Exception('Wrong name or password');
        }

        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Returns the username.
     *
     * @return string The User's username.
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Returns the password.
     *
     * @return string The User's password.
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Validates provided credentials against the database. Throws exception if
     * there is no match.
     * 
     * TODO: Fix handles to many things.
     *
     * @param $username - The username to check for in the database.
     * @param $password - The password to match against the username in the
     * database.
     * @throws Exception No such user in database
     * @return void
     */
    private function retrieveUserFromDatabase(string $username, string $password)
    {
        $databaseConnection = new DatabaseConnection();
        $connection = $databaseConnection->getConnection();

        $escapedUsername = $connection->real_escape_string($username);
        $escapedPassword = $connection->real_escape_string($password);

        $query =
            "SELECT * FROM users " .
            "WHERE username LIKE ?";

        $preparedStatement = $connection->prepare($query);
        $preparedStatement->bind_param('s', $escapedUsername);
        $preparedStatement->execute();

        $result = $preparedStatement->get_result();
        $fetchedUserRow = $result->fetch_assoc();

        $preparedStatement->close();

        $noSuchUserFeedback = 'No such user in database';

        if (empty($fetchedUserRow)) {
            throw new Exception($noSuchUserFeedback);
        }

        $hashedPassword = $fetchedUserRow['password'];
        $correctPassword = password_verify($escapedPassword, $hashedPassword);

        if ($correctPassword) {
            return;
        }

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
            throw new Exception($noSuchUserFeedback);
        }

        $this->username = $fetchedCookieRow['username'];
        $this->password = $fetchedCookieRow['password'];
    }
}
