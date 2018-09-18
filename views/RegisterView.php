<?php

/** Views for the login and logout forms of the web page. */
class RegisterView
{
    private static $register = 'RegisterView::Register';
    private static $backToLogin = 'RegisterView::BackToLogin';
    private static $name = 'RegisterView::UserName';
    private static $password = 'RegisterView::Password';
    private static $repeatPassword = 'RegisterView::PasswordRepeat';
    private static $messageId = 'RegisterView::Message';

    /**
     * Generates HTML code on the output buffer for the register form.
     *
     * @param string $feedback (optional) - Output message.
     * @return string The HTML code for the login form.
     */
    public function generateRegisterFormHTML(string $feedback = '', string $name = ''): string
    {
        return '
			<form method="post">
				<fieldset>
                    <legend>Register a new user - Write username and password</legend>
                    <p id="' . self::$messageId . '">' . $feedback . '</p>

					<label for="' . self::$name . '">Username :</label>
                    <input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $name . '" />
                    <br />

					<label for="' . self::$password . '">Password :</label>
                    <input type="password" id="' . self::$password . '" name="' . self::$password . '" />
                    <br />
                    
                    <label for="' . self::$repeatPassword . '">Repeat password :</label>
                    <input type="password" id="' . self::$repeatPassword . '" name="' . self::$repeatPassword . '" />
                    <br />

					<input type="submit" name="' . self::$register . '" value="Register" />
				</fieldset>
            </form>
		';
    }
}
