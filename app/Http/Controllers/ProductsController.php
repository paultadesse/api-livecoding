<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ProductsController extends Controller
{
    public function index()
    {
        $products = products::paginate(10);
        return response()->json(['data' => $products], 200);
    }

    public function show($id)
    {
        $product = Products::where('id', $id)->get();
        return response()->json(['data' => $product], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'code' => 'required',
            'photo' => 'required|mimes:png,jpg'
        ]);
        $photo = '';

        if (request()->hasFile('photo')) {
            Storage::delete('public/' . $photo);
            $photo = request()->file('photo')->store('products/photos', 'public');
        }
        $validated['photo'] = $photo;

        Products::create($validated);

        return response()->json('product created !', 201);
    }

    public function update(Request $request)
    {

        $validated = $request->validate();

        //delete existing photo for a that specific product (otherwise we might have a duplicate image)
        $photo = auth()->user()->photo ?? '';

        if (request()->hasFile('photo')) {
            Storage::delete('public/' . $photo);
            $photo = request()->file('photo')->store('products/photos', 'public');
        }
        $validated['photo'] = $photo;

        Products::updated($validated);

        return response()->json('product upadted !', 201);
    }

    public function destroy($id)
    {
        $product = Products::where('id', $id)->get();
        $product->delete();

        return response()->json('deleted', 201);
    }
}
