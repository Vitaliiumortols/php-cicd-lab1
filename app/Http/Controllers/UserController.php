<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $users = User::withCount('orders')->with('cart:id,user_id,status')->orderBy('name');

        return $request->expectsJson()
            ? response()->json($users->get())
            : view('users.index', ['users' => $users->paginate(10)]);
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $this->validated($request);
        $data['active'] = $request->boolean('active');
        $user = User::create($data);

        return $request->expectsJson()
            ? response()->json($user, 201)
            : redirect()->route('users.index')->with('success', 'Користувача створено.');
    }

    public function show(User $user, Request $request): View|JsonResponse
    {
        $user->load(['cart.items.product', 'orders.items', 'orders.shipment']);

        return $request->expectsJson()
            ? response()->json($user)
            : view('users.show', ['user' => $user]);
    }

    public function edit(User $user): View
    {
        return view('users.edit', ['user' => $user]);
    }

    public function update(Request $request, User $user): RedirectResponse|JsonResponse
    {
        $data = $this->validated($request, $user);
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $data['active'] = $request->boolean('active');
        $user->update($data);

        return $request->expectsJson()
            ? response()->json($user)
            : redirect()->route('users.show', $user)->with('success', 'Користувача оновлено.');
    }

    public function destroy(User $user, Request $request): RedirectResponse|JsonResponse
    {
        if ($user->orders()->exists()) {
            $user->update(['active' => false]);
            $message = 'Користувача деактивовано, історію замовлень збережено.';

            return $request->expectsJson()
                ? response()->json(['status' => 'archived', 'message' => $message])
                : redirect()->route('users.index')->with('success', $message);
        }

        $user->delete();

        return $request->expectsJson()
            ? response()->json(['status' => 'deleted'])
            : redirect()->route('users.index')->with('success', 'Користувача видалено.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in(['buyer', 'manager', 'admin'])],
            'active' => ['nullable', 'boolean'],
        ]);
    }
}
