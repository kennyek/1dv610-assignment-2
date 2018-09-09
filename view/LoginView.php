<?php

require_once 'config/environment.php';

class LoginView
{
    private static $login = 'LoginView::Login';
    private static $logout = 'LoginView::Logout';
    private static $name = 'LoginView::UserName';
    private static $password = 'LoginView::Password';
    private static $cookieName = 'LoginView::CookieName';
    private static $cookiePassword = 'LoginView::CookiePassword';
    private static $keep = 'LoginView::KeepMeLoggedIn';
    private static $messageId = 'LoginView::Message';

    /**
     * Checks whether the user is logged in or not.
     * 
     * TODO: Make independent on POST request.
     *
     * @return boolean Whether the user is logged in or not.
     */
    public function checkIfLoggedIn()
    {
        if (
            $_SERVER['REQUEST_METHOD'] !== 'POST' ||
            empty($_POST[self::$password]) ||
            empty($_POST[self::$name])
        ) {
            return false;
        }

        return $this->isProvidedAccountDetailsInDatabase();
    }

    /**
     * Creates HTTP response.
     *
     * Should be called after a login attempt has been determined.
     * 
     * TODO: Refactor some code for readability.
     *
     * @return void BUT writes to standard output and cookies!
     */
    public function response()
    {
        $missingUsernameFeedback = 'Username is missing';
        $missingPasswordFeedback = 'Password is missing';
        $incorrectAccountDetailsFeedback = 'Wrong name or password';
        $welcomeFeedback = 'Welcome';
        $feedback = null;
        $name = null;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->generateLoginFormHTML();
        }

        if (empty($_POST[self::$password])) {
            $feedback = $missingPasswordFeedback;
        }

        if (empty($_POST[self::$name])) {
            $feedback = $missingUsernameFeedback;
        } else {
            $name = $_POST[self::$name];
        }

        $everyFieldIsFilledIn = empty($feedback);

        if (!$everyFieldIsFilledIn) {
            return $this->generateLoginFormHTML($feedback, $name);
        }

        $isDatabaseMatch = $this->isProvidedAccountDetailsInDatabase();

        if (!$isDatabaseMatch) {
            return $this->generateLoginFormHTML($incorrectAccountDetailsFeedback, $name);
        }

        return $this->generateLogoutButtonHTML($welcomeFeedback);
    }

    /**
     * Creates a connection to the database and returns it. If there is an error
     * while connecting, the page renders the error message.
     *
     * @return object MySQL link identifier.
     */
    private function createDatabaseConnection()
    {
        $dbName = Environment::DB_NAME;
        $dbHost = Environment::DB_HOST;
        $dbUser = Environment::DB_USERNAME;
        $dbPassword = Environment::DB_PASSWORD;

        $dbConnection = mysqli_connect($dbHost, $dbUser, $dbPassword, $dbName);

        if (!$dbConnection) {
            die("Connection failed: " . mysqli_connect_error());
        }

        return $dbConnection;
    }

    /**
     * Generates HTML code on the output buffer for the logout button.
     *
     * @param string $feedback (optional) - Output message.
     * @param string $name (optional) - Name of user.
     * @return void BUT writes to standard output!
     */
    private function generateLoginFormHTML($feedback = '', $name = '')
    {
        return '
			<form method="post">
				<fieldset>
					<legend>Login - enter Username and password</legend>
					<p id="' . self::$messageId . '">' . $feedback . '</p>

					<label for="' . self::$name . '">Username :</label>
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $name . '" />

					<label for="' . self::$password . '">Password :</label>
					<input type="password" id="' . self::$password . '" name="' . self::$password . '" />

					<label for="' . self::$keep . '">Keep me logged in  :</label>
					<input type="checkbox" id="' . self::$keep . '" name="' . self::$keep . '" />

					<input type="submit" name="' . self::$login . '" value="login" />
				</fieldset>
			</form>
		';
    }

    /**
     * Generates HTML code on the output buffer for the logout button.
     *
     * @param string $feedback - String output message.
     * @return void BUT writes to standard output!
     */
    private function generateLogoutButtonHTML($feedback)
    {
        return '
			<form method="post">
				<p id="' . self::$messageId . '">' . $feedback . '</p>
				<input type="submit" name="' . self::$logout . '" value="logout" />
			</form>
		';
    }

    /**
     * Extracts the username from a GET request.
     *
     * TODO: CREATE GET-FUNCTIONS TO FETCH REQUEST VARIABLES
     *
     * @return void
     */
    private function getRequestUserName()
    {
        //RETURN REQUEST VARIABLE: USERNAME
    }

    /**
     * Check whether the submitted credentials has a match in the database.
     *
     * @return boolean True if submitted credentials has a match in the database.
     */
    private function isProvidedAccountDetailsInDatabase()
    {
        $dbConnection = $this->createDatabaseConnection();

        // Clean query without the ability to execute.
        $query = sprintf("SELECT * FROM users WHERE username LIKE '%s' AND password LIKE '%s'",
            mysqli_real_escape_string($dbConnection, $_POST[self::$name]),
            mysqli_real_escape_string($dbConnection, $_POST[self::$password])
        );

        $queryResult = mysqli_query($dbConnection, $query);

        if (!$queryResult) {
            die('Query Failed' . mysqli_error());
        }

        return (bool) mysqli_fetch_assoc($queryResult);
    }
}
