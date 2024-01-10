<?php

namespace Framework\Middleware;

use Framework\Session;

class Authorized
{
    /**
     * Check if user is authenticated
     * 
     * @return bool
     */
    public function isAuthenticated()
    {
        return Session::has('user');
    }

    /**
     * Handle the user's request
     * 
     * @param string $role
     * @return bool
     */
    public function handle($role)
    {
        if($role == 'guest' && $this->isAuthenticated()) {
            return redirec('/');
        } else if($role === 'auth' && !$this->isAuthenticated()) {
            return redirec('/auth/login');
        }
    }

}#end class