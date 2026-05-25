<?php
// app/Controllers/SocketController.php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SocketController extends BaseController
{
    private function sendToSocket($endpoint, $data)
    {
        $ch = curl_init('http://localhost:3000/' . $endpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        $result = curl_exec($ch);
        curl_close($ch);
    }

    public function updatePropertyNotify()
    {
        $input = $this->request->getJSON();
        $this->sendToSocket('update-property', $input);
        return $this->response->setJSON(['success' => true]);
    }

    public function deletePropertyNotify()
    {
        $input = $this->request->getJSON();
        $this->sendToSocket('delete-property', $input);
        return $this->response->setJSON(['success' => true]);
    }

    public function archivePropertyNotify()
    {
        $input = $this->request->getJSON();
        $this->sendToSocket('archive-property', $input);
        return $this->response->setJSON(['success' => true]);
    }

    public function unarchivePropertyNotify()
    {
        $input = $this->request->getJSON();
        $this->sendToSocket('unarchive-property', $input);
        return $this->response->setJSON(['success' => true]);
    }
}