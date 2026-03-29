<?php

namespace App\Controllers;

class WebSocketTest extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'WebSocket Connection Test',
            'websocket_port' => 8080, // Change this if your WebSocket server uses a different port
            'user_id' => session()->get('user')['id'] ?? 1,
            'user_role' => session()->get('user')['role'] ?? 'buyer'
        ];
        
        return view('websocket_test', $data);
    }
    
    public function status()
    {
        // Check if WebSocket server is running
        $port = $this->request->getGet('port') ?? 8080;
        $connection = @fsockopen('localhost', $port, $errno, $errstr, 2);
        
        if ($connection) {
            fclose($connection);
            return $this->response->setJSON([
                'status' => 'online',
                'port' => $port,
                'message' => 'WebSocket server is running'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'offline',
                'port' => $port,
                'message' => 'WebSocket server is not running on port ' . $port
            ]);
        }
    }
}