<?php

declare(strict_types=1);

namespace App\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        return $this->success(['message' => 'hello']);
    }

    public function store()
    {
        return $this->success([
            'message' => 'post received',
            'method' => $_SERVER['REQUEST_METHOD'] ?? null,
        ], 'Data received');
    }

    public function destroy()
    {
        return $this->success([
            'message' => 'resource deleted',
            'method' => $_SERVER['REQUEST_METHOD'] ?? null,
        ]);
    }
}
