<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    /**
     * Display the services index page
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Services/Index');
    }
}