<?php

require_once 'lib/SecurityUtilities.php';
require_once 'models/Cookie.php';
require_once 'models/DatabaseConnection.php';
require_once 'models/User.php';
require_once 'views/LayoutView.php';
require_once 'views/LoginView.php';
require_once 'views/RegisterView.php';

/**
 * Handles the login logic, such as checking logged in status and handle
 * requests for the login/logout page.
 *
 * TODO: Maybe the instance should not hold an instance of the views as fields,
 * but instanciate in the methods. Re-evaluate!
 *
 * TODO: Must remove hard coded logic such as the static variables.
 * 
 * TODO: Refactor everything.
 */
class LoginController
{
    // TODO: This is cheating. Get from view in future.
    private static $name = 'LoginView::UserName';
    private static $password = 'LoginView::Password';
    private static $cookieName = 'LoginView::CookieName';
    private static $cookiePassword = 'LoginView::CookiePassword';
    private static $login = 'LoginView::Login';
    private static $logout = 'LoginView::Logout';
    private static $keep = 'LoginView::KeepMeLoggedIn';
    private static $register = 'RegisterView::Register';
    private static $registerName = 'RegisterView::UserName';
    private static $registerPassword = 'RegisterView::Password';
    private static $registerPasswordRepeat = 'RegisterView::PasswordRepeat';

    /** @var LoginView */
    private $layoutView;

    /** @var LoginView */
    private $loginView;

    /** @var RegisterView */
    private $registerView;

    /** Creates a new LoginController. */
    public function __construct()
    {
        $this->layoutView = new LayoutView();
        $this->loginView = new LoginView();
        $this->registerView = new RegisterView();
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

    private function httpGetResponse()
    {
        if (isset($_GET['register'])) {
            return $this->httpGetRegisterResponse();
        }
        
        return $this->httpGetRootResponse();
    }

    private function httpGetRegisterResponse()
    {
        $registerViewHtml = $this->registerView->generateRegisterFormHTML();
        return $this->layoutView->render(false, $registerViewHtml);
    }

    /**
     * Renders a login view in response to a HTTP GET request.
     * 
     * TODO: Refactor.
     *
     * @return void BUT writes to standard output and cookies!
     */
    private function httpGetRootResponse()
    {
        $welcomeWithCookieFeedback = 'Welcome back with cookie';
        $isLoggedIn = $this->isLoggedIn();
        $isSessionActive =
            !empty($_SESSION['user']) &&
            ($_SESSION['user']['browser'] === $_SERVER['HTTP_USER_AGENT']);
        $isCookiesSet = !empty($_COOKIE[self::$cookieName]) && !empty($_COOKIE[self::$cookiePassword]);

        if ($isSessionActive) {
            $loginViewHtml = $this->loginView->generateLogoutButtonHTML();
        } else if ($isCookiesSet) {
            try {
                $username = $_COOKIE[self::$cookieName];
                $password = $_COOKIE[self::$cookiePassword];
                Cookie::retrieveCookieFromDatabase($username, $password);

                $user = new User($username, $password);

                $_SESSION['user'] = [
                    'username' => $user->getUsername(),
                    'password' => $user->getPassword(),
                    'browser' => $_SERVER['HTTP_USER_AGENT']
                ];

                $loginViewHtml = $this->loginView->generateLogoutButtonHTML($welcomeWithCookieFeedback);
            } catch (Exception $exception) {
                $loginViewHtml = $this->loginView->generateLoginFormHTML($exception->getMessage());
            }
        } else {
            $loginViewHtml = $this->loginView->generateLoginFormHTML();
        }
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
            'browser' => $_SERVER['HTTP_USER_AGENT']
        ];

        if (isset($_POST[self::$keep])) {
            $randomString = SecurityUtilities::createRandomStringOfLength(30);

            setcookie(self::$cookieName, $username, time() + 3600);
            setcookie(self::$cookiePassword, $randomString, time() + 3600);

            $cookie = Cookie::insertCookieIntoDatabase($username, $randomString);

            $welcomeFeedback = 'Welcome and you will be remembered';
        }

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
        $logoutMessage = '';

        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
            $logoutMessage = 'Bye bye!';
        }

        if (isset($_COOKIE[self::$cookieName])) {
            unset($_COOKIE[self::$cookieName]);
            unset($_COOKIE[self::$cookiePassword]);
        }

        $loginViewHtml = $this->loginView->generateLoginFormHTML($logoutMessage);
        return $this->layoutView->render(false, $loginViewHtml);
    }

    private function httpPostRegisterResponse()
    {
        $feedback = '';

        $username = (isset($_POST[self::$registerName])
            ? $_POST[self::$registerName]
            : '');
        $password = (isset($_POST[self::$registerPassword])
            ? $_POST[self::$registerPassword]
            : '');

        $passwordRepeat = (isset($_POST[self::$registerPasswordRepeat])
            ? $_POST[self::$registerPasswordRepeat]
            : '');

        try {
            User::insertUserIntoDatabase($username, $password, $passwordRepeat);
        } catch (Exception $exception) {
            $feedback = $exception->getMessage();
        }

        $registerViewHtml = $this->registerView->generateRegisterFormHTML($feedback, $username);
        return $this->layoutView->render(false, $registerViewHtml);
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
        if (isset($_POST[self::$register])) {
            return $this->httpPostRegisterResponse();
        } else if (isset($_POST[self::$logout])) {
            return $this->httpPostLogoutResponse();
        } else {
            return $this->httpPostLoginResponse();
        }
    }

    /**
     * Checks whether the user is logged in or not.
     *
     * TODO: Refactor to make easier to read.
     * TODO: Remove nestled try-catch
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
                $isSessionActive =
                    !empty($_SESSION['user']) &&
                    ($_SESSION['user']['browser'] === $_SERVER['HTTP_USER_AGENT']);
                $isCookiesSet = !empty($_COOKIE[self::$cookieName]) && !empty($_COOKIE[self::$cookieName]);

                if ($isSessionActive) {
                    $user = $_SESSION['user'];
                    $username = $user['username'];
                    $password = $user['password'];
                } else if ($isCookiesSet) {
                    $username = $_COOKIE[self::$cookieName];
                    $password = $_COOKIE[self::$cookiePassword];
                }
                
                break;
        }

        try {
            $user = new User($username, $password);
        } catch (Exception $exception) {
            try {
                Cookie::retrieveCookieFromDatabase($username, $password);
            } catch (Exception $exception) {
                return false;
            }
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
