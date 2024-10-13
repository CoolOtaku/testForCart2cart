<?php

namespace App\Http\Controllers;

use App\Services\AnimalFactory;
use Illuminate\Http\Request;
use Exception;

class AnimalController extends Controller
{
    public function index()
    {
        return view('animal');
    }

    public function sound(Request $request)
    {
        $type = $request->input('type', 'dog');

        try {
            $animal = AnimalFactory::createAnimal($type);
            return response()->json(['sound' => $animal->getSoundFile()]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}

