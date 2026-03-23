<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $session = session();
        $data['user'] = $session->get('user');

        return view('index', $data);
    }
}
