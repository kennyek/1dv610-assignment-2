<?php

/** Represents a user account. */
class User {
    /** @var string */
    private $username = '';

    /** @var string */
    private $password = '';

    /**
     * Creates a new User.
     *
     * @param string $username (optional) - The user's username. Set to an empty
     * string if not provided.
     * @param string $password (optional) - The user's password. Set to an empty
     * string if not provided.
     */
    public function __construct(string $username = '', string $password = '') {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Returns the username.
     *
     * @return string The User's username.
     */
    public function getUsername(): string {
        return $this->username;
    }

    /**
     * Returns the password.
     *
     * @return string The User's password.
     */
    public function getPassword(): string {
        return $this->password;
    }
}
