<?php 
namespace App\controllers;

class ErrorController
{
    /**
     * 404 not found error
     *
     * @param string $message
     * @return void
     */
    public static function notFount($message = 'Reosurce not found')
    {
        http_response_code(404);
        loadView('error', [
            'status' => 404,
            'message' => $message
        ]);
    }

    /**
     * 403 unauthorized error
     *
     * @param string $message
     * @return void
     */
    public static function unauthorized($message = 'You are not authorized to view this resourse')
    {
        http_response_code(403);
        loadView('error', [
            'status' => 403,
            'message' => $message
        ]);
    }

}#end class