<?php
namespace App\controllers;

use Framework\Database;
use Framework\Validation;
use Framework\Session;

class UserController
{
    protected $db;

    public function __construct()
    {
        $config = require_once basePath('config/db.php');
        $this->db = new Database($config);
    }

    /**
     * Show the login page
     * 
     * @return void
     */
    public function login()
    {
        loadView('users/login');
    }

     /**
     * Show the register page
     * 
     * @return void
     */
    public function create()
    {
        loadView('users/create');
    }

    /**
     * Store user in database
     * 
     * @return void
     */
    public function store()
    {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $city = $_POST['city'];
        $state = $_POST['state'];
        $password = $_POST['password'];
        $assword_confirmation = $_POST['password_confirmation'];

        $errors = [];
        
        #validation
        if(!Validation::email($email)) {
            $errors['email'] = 'Please enter a valid email address';
        }
        if(!Validation::string($name, 3, 50)) {
            $errors['name'] = 'Name must be between 3 and 50 characters';
        }
        if(!Validation::string($password, 6, 50)) {
            $errors['password'] = 'Password must be at least 6 characters';
        }
        if(!Validation::match($password, $assword_confirmation)) {
            $errors['assword_confirmation'] = 'Password do not match';
        }

        if(!empty($errors)) {
            loadView('users/create', [
                'errors' => $errors,
                'user' => [
                    'name' => $name,
                    'email' => $email,
                    'city' => $city,
                    'state' => $state
                ]
            ]);
            exit;
        }

        #Check if email exists
        $params = [
            'email' => $_POST['email']
        ];
        $user = $this->db->query("SELECT * FROM users WHERE email = :email", $params)->fetch();
        if($user) {
            $errors['email'] = 'The email already exists';
            loadView('users/create', [
                'errors' => $errors
            ]);
            exit;
        }
        #Create  user acount
        $params = [
            'name' => $name,
            'email' => $email,
            'city' => $city,
            'state' => $state,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];

        $set = implode(', ', array_keys($params));
        $placeholders = ':' . implode(', :', array_keys($params));
        
        $this->db->query("INSERT INTO users ({$set}) VALUES ({$placeholders})", $params);
        
        redirec('/');
    }

    /**
     * Logout a user and kill session
     * 
     * @return void
     */
    public static function logout()
    {
        Session::clearAll('user');

        $params = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 86400, $params['path'], $params['domain']);
        redirec('/');
    }

    /**
     * Authenticate a user with emial and password
     * 
     * @return void
     */
    public function authenticate()
    {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $errors = [];

        #Validation
        if(!Validation::email($email)) {
            $errors['email'] = 'Please enter a valid email';
        }
        if(!Validation::string($password, 6, 50)) {
            $errors['password'] = 'Password must be at least 6 characters';
        }

        #check for errors
        if(!empty($errors)) {
            loadView('users/login', [
                'errors' => $errors,
            ]);
            exit;
        }

        #check for email
        $params = [
            'email' => $email
        ];

        $user = $this->db->query("SELECT * FROM users WHERE email = :email", $params)->fetch();
        
        if(!$user) {
            $errors['email'] = 'Incorrect credentials';
            loadView('users/login', [
                'errors' => $errors,
            ]);
            exit;
        }

        #Check if password is correct 
        if(!password_verify($password, $user->password)) {
            $errors['password'] = 'Incorrect password';
            loadView('users/login', [
                'errors' => $errors,
            ]);
            exit;
        } 

        Session::set('user', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'city' => $user->city,
            'state' => $user->state
        ]);

        redirec('/');
    }

}#end class