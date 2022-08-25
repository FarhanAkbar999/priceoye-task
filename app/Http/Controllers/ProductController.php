<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $product = DB::table('products')
            ->join('categories', 'products.category_id' , '=', 'categories.id' )
            ->select('products.*', 'categories.c_name')
            ->get();


        // $product = DB::table('products')->get();
        return view('admin.product.index', compact('product') );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category = DB::table('categories')->get();

        return view('admin.product.create', compact('category') );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = array();
        $data['name'] = $request->name  ;
        $data['category_id'] = $request->category_id ;
        $data['price'] = $request->price ;
        $data['status'] = $request->status ;
        $data['createdBy'] = Auth::id();
        $data['short_description'] = $request->short_description ;
        $data['description'] = $request->description ;


        if ($request->hasFile('image')) {
            $dims = getimagesize($request->image);
            $width = $dims[0];
            $height = $dims[1];
            $name = uniqid() . '-' . $width . '-' . $height . '.' . $request->file('image')->extension();
            $path = public_path('uploads/products/');
            $file = $request->file('image');
            if ($file->move($path, $name)) {
                $data['image'] = $name;
            }
        }
        // dd($data);
        DB::table('products')->insert($data);
        return redirect()->route('products.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = DB::table('products')->where('id', $id)->first();
        $category = DB::table('categories')->get();

        return view('admin.product.edit', compact('product', 'category') );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = array();
        $data['name'] = $request->name  ;
        $data['category_id'] = $request->category_id ;
        $data['price'] = $request->price ;
        $data['status'] = $request->status ;
        $data['updatedBy'] = Auth::id();
        $data['short_description'] = $request->short_description ;
        $data['description'] = $request->description ;


        if ($request->hasFile('image')) {
            $dims = getimagesize($request->image);
            $width = $dims[0];
            $height = $dims[1];
            $name = hexdec(uniqid()) . '-' . $width . '-' . $height . '.' . $request->file('image')->extension();
            $path = public_path('uploads/products/');

            $file = $request->file('image');

            unlink(public_path('uploads/products/'.$request->current_image));
            
            if ($file->move($path, $name)) {
                $data['image'] = $name;
            }
        }
        // dd($data);
        DB::table('products')->where('id', $id)->update($data);
        return redirect()->route('products.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = DB::table('products')->where('id', $id)->first();
        if ($product->image) {
            unlink(public_path('uploads/products/'.$product->image));
        }

       
            
        
        DB::table('products')->where('id', $id)->delete();
        return redirect()->back();
    }
}
