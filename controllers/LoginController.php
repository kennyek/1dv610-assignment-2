<?php

require_once 'models/DatabaseConnection.php';
require_once 'models/User.php';
require_once 'views/LayoutView.php';
require_once 'views/LoginView.php';

/**
 * Handles the login logic, such as checking logged in status and handle
 * requests for the login/logout page.
 *
 * TODO: Maybe the instance should not hold an instance of the views as fields,
 * but instanciate in the methods. Re-evaluate!
 *
 * TODO: Must remove hard coded logic such as the static variables.
 */
class LoginController
{
    private static $name = 'LoginView::UserName';
    private static $password = 'LoginView::Password';
    private static $login = 'LoginView::Login';
    private static $logout = 'LoginView::Logout';

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
     * Creates HTTP response and writes it to standard output and cookies.
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
     * Renders a login view in response to a HTTP GET request.
     *
     * @return void BUT writes to standard output and cookies!
     */
    private function httpGetResponse()
    {
        $isLoggedIn = $this->isLoggedIn();
        $loginViewHtml = empty($_SESSION['user'])
        ? $this->loginView->generateLoginFormHTML()
        : $this->loginView->generateLogoutButtonHTML();

        $this->layoutView->render($isLoggedIn, $loginViewHtml);
    }

    /**
     * Renders a login view in response to a HTTP POST login request.
     * 
     * TODO: Refactor and remove nested if-statement.
     *
     * @return void BUT writes to standard output and cookies!
     */
    private function httpPostLoginResponse()
    {
        $welcomeFeedback = 'Welcome';

        $username = $this->retrieveValueFromPostData(self::$name);
        $password = $this->retrieveValueFromPostData(self::$password);

        if (isset($_SESSION['user'])) {
            $user = $_SESSION['user'];
            if ($user['username'] === $username && $user['password'] === $password) {
                $loginViewHtml = $this->loginView->generateLogoutButtonHTML();
                return $this->layoutView->render(true, $loginViewHtml);
            }
        }

        try {
            $user = new User($username, $password);
        } catch (Exception $exception) {
            $loginViewHtml = $this->loginView->generateLoginFormHTML($exception->getMessage(), $username);
            return $this->layoutView->render(false, $loginViewHtml);
        }

        $_SESSION['user'] = [
            'username' => $user->getUsername(),
            'password' => $user->getPassword(),
        ];

        $loginViewHtml = $this->loginView->generateLogoutButtonHTML($welcomeFeedback);
        return $this->layoutView->render(true, $loginViewHtml);
    }

    /**
     * Renders a login view in response to a HTTP POST logout request.
     *
     * @return void BUT writes to standard output and cookies!
     */
    private function httpPostLogoutResponse()
    {
        $loginViewHtml = $this->loginView->generateLoginFormHTML('Bye bye!');
        return $this->layoutView->render(false, $loginViewHtml);
    }

    /**
     * Renders a login view in response to a HTTP POST request.
     *
     * TODO: Refactor to make more readable.
     *
     * @return void BUT writes to standard output and cookies!
     */
    private function httpPostResponse()
    {
        if (!empty($_POST[self::$logout])) {
            return $this->httpPostLogoutResponse();
        } else {
            return $this->httpPostLoginResponse();
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
        $username = '';
        $password = '';

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $username = $_POST[self::$name] ?? '';
                $password = $_POST[self::$password] ?? '';

                break;
            case 'GET':
                if (empty($_SESSION['user'])) {
                    break;
                }

                $user = $_SESSION['user'];
                $username = $user['username'];
                $password = $user['password'];

                break;
        }

        try {
            $user = new User($username, $password);
        } catch (Exception $exception) {
            return false;
        }

        return true;
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
