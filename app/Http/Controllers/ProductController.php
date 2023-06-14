<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;




class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();
        return $products;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        if($request->input('picture')){
            File::move(storage_path('tmp/uploads/'. basename($request->input('picture'))), public_path('uploads/products/'. basename($request->input('picture'))));
            $path =  'uploads/products/'.basename($request->input('picture'));
            $request['picture'] = $path;
        }else{
            $request['picture'] = null;
        }


            $prdt = Product::create($request->only('name','description','picture','categorie'));

            if($prdt){
                return $prdt;
            }
                return false;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        if($request->input('picture')){
            File::move(storage_path('tmp/uploads/'. basename($request->input('picture'))), public_path('uploads/products/'. basename($request->input('picture'))));
            $path =  'uploads/products/'.basename($request->input('picture'));
            $request['picture'] = $path;
            $product->update($request->only('name','description','picture','categorie'));
        }else{
            $product->update($request->except('picture'));
        }

        if($product){
            return $product;
        }
            return false;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $p = $product->delete();
        if($p){
            return true;
        }
        return false;
    }

    public function storeMedia(Request $request)
    {

        // Validates file size
        if (request()->has('size')) {
            $this->validate(request(), [
                'file' => 'max:' . request()->input('size') * 1024,
            ]);
        }

        // If width or height is preset - we are validating it as an image
        if (request()->has('width') || request()->has('height')) {
            $this->validate(request(), [
                'file' => sprintf(
                    'image|dimensions:max_width=%s,max_height=%s',
                    request()->input('width', 1),
                    request()->input('height', 1)
                ),
            ]);
        }

        $path = storage_path('tmp'.DIRECTORY_SEPARATOR.'uploads');

        try {
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
        } catch (\Exception $e) {
        }

        $file = $request->file('file');

        $name = time() .'.' . $file->getClientOriginalName();
        $file->move($path, $name);
        return $name;

    }
}
