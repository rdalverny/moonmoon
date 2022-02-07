<?php
 /**
  * Authentication-related functions.
  *
  * In moonmoon, the authentication was made using a cookie containing the hash
  * of the password of the user. To avoid its bruteforce in the scenario of
  * an attack leading to the stealing of the authentication cookie (XSS), this
  * mechanism has been replaced by a more "classic" one, based on the PHP sessions.
  * In addition, the bcrypt algorithm is now used as hashing function.
  *
  * @license BSD
  */
class Authentication
{
    protected ?string $username = null;
    protected ?string $password = null;

    /**
     * @var string Path to the file containing administrator's credentials
     */
    public string $file = '';

    public function __construct(string $auth_file = '')
    {
        session_start();
        $this->file = $auth_file;
        $this->readCredentials();
    }

    /**
     * Redirect the user somewhere if the authentication cookie is invalid.
     *
     * @param string $location Page to redirect to if not authenticated
     */
    public function redirectIfNotAuthenticated(string $location = 'login.php') : void
    {
        if (!self::isAuthenticated()) {
            redirect($location);
        }
    }

    /**
     * Load the credentials from the file.
     *
     * @see Authentication::$file
     */
    public function readCredentials() : void
    {
        if (empty($this->username) || empty($this->password)) {
            include $this->file;
            $this->username = $login;
            $this->password = $password;
        }
    }

    /**
     * Is the password still in the old format?
     *
     * MD5 hashes length is 32 characters. This hashing function was
     * used in the last versions of moonmoon but we try to assure backward
     * compatibility for all the installations.
     */
    protected function isOldFormat() : bool
    {
        return strlen($this->password) === 32;
    }

    /**
     * Try to authenticate the user using the provided password.
     */
    public function login(string $providedPassword) : bool
    {
        $valid = false;

        // If the hash is still in the old format, replace it by
        // the same password but stored using bcrypt (obviously, we can do
        // it only if the provided password is the right one).
        if ($this->isOldFormat()) {
            if (hash_equals(md5($providedPassword), $this->password)) {
                $this->changePassword($providedPassword);
                $valid = true;
            }
        }

        // New authentication using bcrypt.
        if (password_verify($providedPassword, $this->password)) {
            $valid = true;
        }

        // If the user has been authenticated using one of the two means,
        // keep the username inside the session.
        if ($valid) {
            $_SESSION['user'] = $this->username;
        }

        return $valid;
    }

    /**
     * Change the password of the current user.
     */
    public function changePassword(string $new) : int
    {
        $out = sprintf('<?php $login="admin"; $password=\'%s\';?>', password_hash($new, PASSWORD_BCRYPT));
        return file_put_contents($this->file, $out);
    }

    /**
     * Is the user authenticated?
     *
     * @return boolean
     */
    public function isAuthenticated() : bool
    {
        return !empty($_SESSION['user']);
    }

    /**
     * Destroy the current session and redirect the user to the login page.
     *
     * The related session file will be removed.
     */
    public function logout() : void
    {
        session_destroy();
        session_regenerate_id(true);
        redirect('login.php');
        die();
    }
}
