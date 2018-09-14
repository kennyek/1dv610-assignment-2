<?php

require_once 'models/DatabaseConnection.php';
require_once 'models/User.php';
require_once 'views/LayoutView.php';
require_once 'views/LoginView.php';

/**
 * Handles the login logic, such as checking logged in status and handle
 * requests for the login/logout page.
 */
class LoginController
{
    private static $name = 'LoginView::UserName';
    private static $password = 'LoginView::Password';

    /** @var LoginView */
    private $layoutView;

    /** @var LoginView */
    private $loginView;

    /** Creates a new LoginController. */
    public function __construct()
    {
        $this->layoutView = new LayoutView();
        $this->loginView = new LoginView();
    }

    /**
     * Creates HTTP response.
     *
     * @return void BUT writes to standard output and cookies!
     */
    public function handleResponse()
    {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                return $this->httpPostResponse();
            case 'GET':
            default:
                return $this->httpGetResponse();
        }
    }

    /**
     * Checks whether the user is logged in or not.
     * 
     * TODO: Refactor to make easier to read.
     *
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        $username = null;
        $password = null;

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $username = (!empty($_POST[self::$name])
                    ? $_POST[self::$name]
                    : ''
                );
                $password = (!empty($_POST[self::$password])
                    ? $_POST[self::$password]
                    : ''
                );
                break;
            case 'GET':
                $account = (!empty($_SESSION['account'])
                    ? $_SESSION['account']
                    : new User()
                );
                $username = $account->getUsername();
                $password = $account->getPassword();
                break;
            default:
                $username = '';
                $password = '';
                break;
        }

        $isAccountDetailsPresent = !(empty($username) || empty($password));

        return ($isAccountDetailsPresent &&
            $this->isProvidedAccountDetailsInDatabase($username, $password));
    }

    /**
     * Renders a login view in response to a HTTP GET request.
     *
     * @return void BUT writes to standard output and cookies!
     */
    private function httpGetResponse()
    {
        $isLoggedIn = $this->isLoggedIn();
        $loginViewHtml = empty($_SESSION['account'])
            ? $this->loginView->generateLoginFormHTML()
            : $this->loginView->generateLogoutButtonHTML();

        $this->layoutView->render($isLoggedIn, $loginViewHtml);
    }

    /**
     * Creates HTTP response for HTTP POST requests.
     * 
     * TODO: Refactor to make more readable.
     *
     * @return void BUT writes to standard output and cookies!
     */
    private function httpPostResponse()
    {
        $missingUsernameFeedback = 'Username is missing';
        $missingPasswordFeedback = 'Password is missing';
        $incorrectAccountDetailsFeedback = 'Wrong name or password';
        $welcomeFeedback = 'Welcome';

        $username = $this->retrieveValueFromPostData(self::$name);
        $password = $this->retrieveValueFromPostData(self::$password);

        $feedback = (empty($username)
            ? $missingUsernameFeedback
            : (empty($password)
                ? $missingPasswordFeedback
                : null
            ));

        if (!empty($feedback)) {
            $loginViewHtml = $this->loginView->generateLoginFormHTML($feedback, $username);
            return $this->layoutView->render(false, $loginViewHtml);
        }

        $user = new User($username, $password);
        $isUserInDatabase = $this->isProvidedAccountDetailsInDatabase($user);

        if ($isUserInDatabase) {
            $_SESSION['user'] = $user;
        } else {
            $loginViewHtml = $this->loginView->generateLoginFormHTML($incorrectAccountDetailsFeedback, $username);
            return $this->layoutView->render(false, $loginViewHtml);
        }

        $loginViewHtml = $this->loginView->generateLogoutButtonHTML($welcomeFeedback);
        return $this->layoutView->render(true, $loginViewHtml);
    }

    /**
     * Checks if a user exists in the database.
     *
     * @param User $user - The user to check for in the database.
     * @return bool Whether the user exists in the database or not.
     */
    private function isProvidedAccountDetailsInDatabase(User $user): bool
    {
        $databaseConnection = new DatabaseConnection();
        return $databaseConnection->isUserInDatabase($user);
    }

    /**
     * Returns the value from the POST request data with the provided key, or an
     * empty string if the the key does not exist on the POST request data.
     *
     * @param string $postKey - The key to search in the POST request data.
     * @return string The value of the POST data with the provided key.
     */
    private function retrieveValueFromPostData(string $postKey): string
    {
        return $_POST[$postKey] ?? '';
    }
}
