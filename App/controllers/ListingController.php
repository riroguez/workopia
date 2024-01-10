<?php

namespace App\controllers;

use Framework\Database;
use Framework\Session;
use Framework\Validation;
use Framework\Authorization;
use Framework\Middleware\Authorized;

class ListingController
{
    protected $db;

    public function __construct()
    {
        $config = require_once basePath('config/db.php');
        $this->db = new Database($config);
    }

    /**
     * Show all listing
     *
     * @return void
     */
    public function index()
    {
        $listings = $this->db->query("SELECT * FROM listings ORDER BY created_at DESC")
            ->fetchAll();


        loadView('listings/index', [
            'listings' => $listings
        ]);
    }

    /**
     * Show the create listing form
     * 
     *
     * @return void
     */
    public function create()
    {
        loadView('listings/create');
    }

    /**
     * Show a single list
     *
     * @param array $params
     * @return void
     */
    public function show($params)
    {
        $id = $params['id'] ?? '';

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query("SELECT * FROM listings WHERE id = :id", $params)->fetch();
        if (!$listing) {
            ErrorController::notFount('Listing not found');
            return;
        }

        loadView('listings/show', [
            'listing' => $listing
        ]);
    }

    /**
     * Store data in database
     * 
     * @return void
     */
    public function store()
    {
        $allowedFields = ['title', 'description', 'salary', 'tags', 'company', 'address', 'city', 'state', 'phone', 'email', 'requirements', 'benefits'];

        $newListingData = array_intersect_key($_POST, array_flip($allowedFields));

        $newListingData['user_id'] = Session::get('user')['id'];

        $newListingData = array_map('sanitize', $newListingData);

        $requiredFields = ['title', 'description', 'email', 'city', 'state', 'salary'];

        $errors = [];

        foreach ($requiredFields as $field) {
            if (empty($newListingData[$field]) || !Validation::string($newListingData[$field])) {
                $errors[$field] = ucfirst($field) . ' is requered';
            }
        }

        if (!empty($errors)) {
            //reload view with
            loadView('listings/create', [
                'errors' => $errors,
                'listing' => $newListingData
            ]);
        } else {
            //Submit data
            $fields = [];

            foreach ($newListingData as $field => $value) {
                $fields[] = $field;
            }

            $fields = implode(', ', $fields);

            $values = [];
            foreach ($newListingData as $field => $value) {
                #convert empty  string to null
                if ($value == '') {
                    $newListingData[$field] == null;
                }
                $values[] = ':' . $field;
            }

            $values = implode(', ', $values);

            $query = "INSERT INTO  listings ({$fields}) VALUES ({$values})";
            $this->db->query($query, $newListingData);

            Session::setFlashMessage('success_message', 'Listings create successfully');

            redirec('/listings');
        }
    }

    /**
     * Delete a listing
     * 
     * @param array $params
     * @return void
     */
    function destroy($params)
    {
        $id = $params['id'];
        $params = [
            'id' => $id
        ];
        $listing = $this->db->query("SELECT * FROM listings WHERE id = :id", $params)->fetch();

        #Check if listing exists
        if (!$listing) {
            ErrorController::notFount('Listing not fount');
            return;
        }

        if(!Authorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message', 'Yout are not authorizwd to delete this listing');
            return redirec('/listings/' . $listing->id);
        }


        $this->db->query("DELETE FROM listings WHERE id = :id", $params);

        #Set flash message
        Session::setFlashMessage('success_message', 'Listings delete successfully');

        redirec('/listings');
    }

    /**
     * Show the listing edit form
     *
     * @param array $params
     * @return void
     */
    public function edit($params)
    {
        $id = $params['id'] ?? '';

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query("SELECT * FROM listings WHERE id = :id", $params)->fetch();
        if (!$listing) {
            ErrorController::notFount('Listing not found');
            return;
        }

        if(!Authorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message', 'Yout are not authorizwd to update this listing');
            return redirec('/listings/' . $listing->id);
        }

        loadView('listings/edit', [
            'listing' => $listing
        ]);
    }

    /**
     * Update a listing
     * 
     * @param array $params
     * @return void
     */
    public function update($params)
    { 
        $id = $params['id'] ?? '';

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query("SELECT * FROM listings WHERE id = :id", $params)->fetch();
        if (!$listing) {
            ErrorController::notFount('Listing not found');
            return;
        }

        #Authorized
        if(!Authorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message', 'Yout are not authorizwd to update this listing');
            return redirec('/listings/' . $listing->id);
        }

        $allowedFields = ['title', 'description', 'salary', 'tags', 'company', 'address', 'city', 'state', 'phone', 'email', 'requirements', 'benefits'];

        $updateValues = [];

        $updateValues = array_intersect_key($_POST, array_flip($allowedFields));

        $updateValues = array_map('sanitize', $updateValues);

        $requiredFields = ['title', 'description', 'salary', 'email', 'city', 'state'];

        $errors = [];

        foreach($requiredFields as $field) {
            if(empty($updateValues[$field]) || !Validation::string($updateValues[$field])) {
                $errors[$field] = ucfirst($field) . ' is required';
            }
        }

        if(!empty($errors)) {
            loadView('listings/edit', [
                'listing' => $listing,
                'errors' => $errors 
            ]);
            exit;
        } else {
            #Submit to database
            $updateFields = [];

            foreach(array_keys($updateValues) as $field) {
                $updateFields[] = "{$field} = :{$field}";
            } 

            $updateFields = implode(', ', $updateFields);

            $updateQuery = "UPDATE listings SET {$updateFields} WHERE id = :id";
            
            $updateValues['id'] = $id;
            $this->db->query($updateQuery, $updateValues);

            Session::setFlashMessage('success_message', 'Listings update successfully');

            redirec('/listings/' . $id);
            
        }
    }

    /**
     * Search listing by keywords/location
     * 
     * @return void
     */
    public function search()
    {
        $keywords = isset($_GET['keywords']) ? trim($_GET['keywords']) : '';
        $location = isset($_GET['location']) ? trim($_GET['location']) : '';

        $query = "SELECT * FROM listings WHERE CONCAT(title, description, tags) LIKE :keywords AND (city LIKE :location)";
        
        $params = [
            'keywords' => "%{$keywords}%",
            'location' => "%{$location}%"
        ];
 
        $listings = $this->db->query($query, $params)->fetchAll();

        loadView('listings/index', [
            'listings' => $listings,
            'keywords' => $keywords,
            'location' => $location
        ]);

    }

}#end class