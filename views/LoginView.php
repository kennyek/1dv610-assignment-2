<?php

/** Views for the login and logout forms of the web page. */
class LoginView
{
    private static $login = 'LoginView::Login';
    private static $logout = 'LoginView::Logout';
    private static $name = 'LoginView::UserName';
    private static $password = 'LoginView::Password';
    private static $keep = 'LoginView::KeepMeLoggedIn';
    private static $messageId = 'LoginView::Message';

    /**
     * Generates HTML code on the output buffer for the logout button.
     *
     * @param string $feedback (optional) - Output message.
     * @param string $name (optional) - Name of user.
     * @return string The HTML code for the login form.
     */
    public function generateLoginFormHTML(string $feedback = '', string $name = ''): string
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
     * @param string $feedback (optional) - String output message.
     * @return string The HTML code for the logout form.
     */
    public function generateLogoutButtonHTML(string $feedback = ''): string
    {
        return '
			<form method="post">
				<p id="' . self::$messageId . '">' . $feedback . '</p>
				<input type="submit" name="' . self::$logout . '" value="logout" />
			</form>
		';
    }
}
