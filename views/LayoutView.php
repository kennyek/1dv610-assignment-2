<?php

require_once 'views/DateTimeView.php';

/** View for the layout of the web page. */
class LayoutView
{
    /** @var DateTimeView */
    private $dateTimeView;

    /** Creates a new LayoutView. */
    public function __construct()
    {
        $this->dateTimeView = new DateTimeView();
    }

    /**
     * Prints the HTML layout to the browser.
     *
     * @param bool $isLoggedIn - Whether the user is signed in or not.
     * @param string $loginViewHtml - Form for signing in or out.
     * @return void BUT writes to standard output!
     */
    public function render($isLoggedIn, string $loginViewHtml)
    {
        echo '
            <!DOCTYPE html>
            <html>

            <head>
            <meta charset="utf-8">
            <title>Login Example</title>
            </head>

            <body>
            <h1>Assignment 2</h1>

            ' . $this->renderNavigationLink() . '
            ' . $this->renderIsLoggedIn($isLoggedIn) . '

            <div class="container">
                ' . $loginViewHtml . '
                ' . $this->dateTimeView->show() . '
            </div>
            </body>

            </html>
        ';
    }

    /**
     * Creates a heading with a message of sign in status.
     *
     * @param bool $isLoggedIn - Whether or not the user is logged in.
     * @return string - HTML for a second level heading tag with sign in status.
     */
    private function renderIsLoggedIn($isLoggedIn)
    {
        if ($isLoggedIn) {
            return '<h2>Logged in</h2>';
        } else {
            return '<h2>Not logged in</h2>';
        }
    }

    private function renderNavigationLink()
    {
        $href = (!isset($_GET['register'])
            ? dirname($_SERVER['SCRIPT_NAME']) . '?register'
            : dirname($_SERVER['SCRIPT_NAME']));

        $content = (!isset($_GET['register'])
            ? 'Register a new user'
            : 'Back to login');

        return '<a href="' . $href . '">' . $content . '</a>';
    }
}
