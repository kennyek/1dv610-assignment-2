<?php

require_once 'config/environment.php';

/**
 * A connection to the database. Can be used to check user credentials against
 * the database.
 */
class DatabaseConnection
{
    /** @var mysqli */
    private $connection = null;

    /** Creates a new connection to the database. */
    public function __construct()
    {
        $this->connection = $this->createConnection();
    }

    /**
     * Checks if a user exists in the database.
     *
     * @param User $user - The user to check for in the database.
     * @return bool Whether the user exists in the database or not.
     */
    public function isUserInDatabase(User $user): bool
    {
        $escapedUsername = $this->connection->real_escape_string($user->getUsername());
        $escapedPassword = $this->connection->real_escape_string($user->getPassword());
        $query = 
            "SELECT * FROM users " .
            "WHERE username LIKE ? " .
            "AND password LIKE ?";

        $preparedStatement = $this->connection->prepare($query);
        $preparedStatement->bind_param('ss', $escapedUsername, $escapedPassword);
        $preparedStatement->execute();

        $result = $preparedStatement->get_result();
        $fetchedUserRow = $result->fetch_assoc();
        
        $preparedStatement->close();

        return !empty($fetchedUserRow);
    }

    /**
     * Creates a connection to the database and returns it.
     *
     * @return mysqli The database connection.
     */
    private function createConnection(): mysqli
    {
        $host = $_ENV['DB_HOST'];
        $username = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];
        $name = $_ENV['DB_NAME'];

        $connection = new mysqli($host, $username, $password, $name);

        if ($connection->connect_error) {
            $exitMessage = ($_ENV['ENVIRONMENT'] === 'development'
                ? ('Connection failed: ' . mysqli_connect_error())
                : 'Error connecting to database.'
            );

            exit($exitMessage);
        }

        return $connection;
    }
}
