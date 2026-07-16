<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\Landing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class LandingController extends Controller
{
    public function index()
    {
        // Ambil data setting pertama
        $setting = Setting::first();

        return Inertia::render('Landing', [
            'setting' => $setting,
        ]);
    }
}
