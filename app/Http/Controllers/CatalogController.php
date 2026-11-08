<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class CatalogController extends Controller
{
    public function info(): JsonResponse
    {
        return response()->json([
            'store' => 'Інтернет-магазин iPhone',
            'student' => 'Томенко Віталій Ігорович',
            'group' => 'КН-42',
        ]);
    }
}
