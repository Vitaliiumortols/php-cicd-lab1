<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(User::withCount('orders')->get());
    }

    public function show(User $user): JsonResponse
    {
        return response()->json($user->load(['cart.items.product', 'orders.items', 'orders.shipment']));
    }
}
