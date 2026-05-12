<?php

declare(strict_types=1);

namespace App\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        return json_encode(['response' => 'hello']);
    }

    public function store()
    {
        return json_encode([
            'response' => 'post received',
            'method' => $_SERVER['REQUEST_METHOD'] ?? null,
        ]);
    }

    public function destroy()
    {
        return json_encode([
            'response' => 'resource deleted',
            'method' => $_SERVER['REQUEST_METHOD'] ?? null,
        ]);
    }
}
