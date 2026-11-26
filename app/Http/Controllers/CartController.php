<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $carts = Cart::with('user:id,name,email')->withCount('items')->latest('id');

        return $request->expectsJson()
            ? response()->json($carts->get())
            : view('carts.index', ['carts' => $carts->paginate(10)]);
    }

    public function create(): View
    {
        return view('carts.create', ['users' => User::doesntHave('cart')->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $cart = Cart::create($this->validated($request));

        return $request->expectsJson()
            ? response()->json($cart, 201)
            : redirect()->route('carts.show', $cart)->with('success', 'Кошик створено.');
    }

    public function show(Cart $cart, Request $request): View|JsonResponse
    {
        $cart->load(['user:id,name,email', 'items.product.tags']);

        if ($request->expectsJson()) {
            return response()->json($cart);
        }

        return view('carts.show', [
            'cart' => $cart,
            'products' => Product::where('active', true)->orderBy('model_name')->get(),
        ]);
    }

    public function edit(Cart $cart): View
    {
        return view('carts.edit', [
            'cart' => $cart,
            'users' => User::whereDoesntHave('cart')->orWhere('id', $cart->user_id)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Cart $cart): RedirectResponse|JsonResponse
    {
        $cart->update($this->validated($request, $cart));

        return $request->expectsJson()
            ? response()->json($cart)
            : redirect()->route('carts.show', $cart)->with('success', 'Кошик оновлено.');
    }

    public function destroy(Cart $cart, Request $request): RedirectResponse|JsonResponse
    {
        $cart->delete();

        return $request->expectsJson()
            ? response()->json(['status' => 'deleted'])
            : redirect()->route('carts.index')->with('success', 'Кошик видалено.');
    }

    public function addItem(Request $request, Cart $cart): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        if ($data['quantity'] > $product->availableQty()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Недостатньо товару на складі.'], 422)
                : back()->withErrors(['quantity' => 'Недостатньо товару на складі.'])->withInput();
        }

        $item = $cart->items()->updateOrCreate(
            ['product_id' => $data['product_id']],
            ['quantity' => $data['quantity']],
        );

        return $request->expectsJson()
            ? response()->json($item->load('product'), 201)
            : back()->with('success', 'Позицію кошика збережено.');
    }

    public function removeItem(Cart $cart, CartItem $cartItem, Request $request): RedirectResponse|JsonResponse
    {
        abort_unless($cartItem->cart_id === $cart->id, 404);
        $cartItem->delete();

        return $request->expectsJson()
            ? response()->json(['status' => 'deleted'])
            : back()->with('success', 'Позицію видалено з кошика.');
    }

    private function validated(Request $request, ?Cart $cart = null): array
    {
        return $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id', Rule::unique('carts')->ignore($cart)],
            'status' => ['required', Rule::in(['active', 'converted', 'abandoned'])],
        ]);
    }
}
