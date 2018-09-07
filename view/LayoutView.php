<?php

class LayoutView
{
    /**
     * Prints the HTML layout to the browser.
     *
     * @param bool $isLoggedIn - Whether the user is signed in or not.
     * @param LoginView $loginView - Form for signing in or out.
     * @param DateTimeView $dateTimeView - A paragraph displaying a time stamp.
     * @return void
     */
    public function render($isLoggedIn, LoginView $loginView, DateTimeView $dateTimeView)
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

            ' . $this->renderIsLoggedIn($isLoggedIn) . '

            <div class="container">
                ' . $loginView->response() . '

                ' . $dateTimeView->show() . '
            </div>
            </body>
        
            </html>
        ';
    }

    /**
     * Creates a heading with a message of sign in status.
     *
     * @param bool $isLoggedIn - Whether or not the user is logged in.
     * @return string - A second level heading tag with sign in status.
     */
    private function renderIsLoggedIn($isLoggedIn)
    {
        if ($isLoggedIn) {
            return '<h2>Logged in</h2>';
        } else {
            return '<h2>Not logged in</h2>';
        }
    }
}
