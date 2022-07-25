<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        return view('products.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {


    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $variants = Variant::all();
        return view('products.edit', compact('variants','product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {

// description	""
// product_image	[]
// product_variant	[…]
// 0	{…}
// option	1
// tags	[]
// product_variant_prices	[]
// sku	""
// title	""




        // title	"adfasafdfa"
        // sku	"dfasdf"
        // description	null
        try{
            DB::beginTransaction();
            $product->update([
                'title'=> $request->input('title'),
                'sku'=> $request->input('sku'),
                'description'=> $request->input('description'),
            ]);
            $product_variants = json_decode($request->input('product_variant'));
            $product_variant_prices = json_decode($request->input('product_variant_prices'));
            // return $product_variants;
            $variants = [];
            foreach($product_variants as $pro_variant){
                $variant = Variant::find($pro_variant->option);
                $variantData = [
                    'variant'  => $pro_variant->tags,
                   'variant_id'  => $variant->id,
                  'product_id'  => $product->id
                ];
                array_push($variants,$variant);
                ProductVariant::create($variantData);

            }
            foreach($product_variant_prices as $key=>$price){
                if($key < 3 ){
                    $variantPricesData = [
                        'product_variant_one'   => $variants[0]->id,
                        'product_variant_two'   => $variants[1]->id,
                        'product_variant_three'   => $variants[2]->id,
                        'price'   => $price->price,
                        'stock'   => $price->stock,
                        'product_id'   => $product->id,
                    ];
                    ProductVariantPrice::create($variantPricesData);
                }
            }
            $product_images = json_decode($request->input('product_image'));
            foreach($product_images as $product_image){
                $filename = time().$product_image->upload->filename;
                $imageData = $product_image->dataURL;
                list($type, $imageData) = explode(';', $imageData);
                list(, $imageData)      = explode(',', $imageData);
                $imageData = base64_decode($imageData);
                file_put_contents(public_path('images/'.$filename), $imageData);
                 $product_image_data = [
                    'product_id' => $product->id,
                    'file_path'  => $filename,
                    'thumbnail'  =>0,
                 ];
                ProductImage::create($product_image_data);
            }

            // product_variant	[ {…}, {…}, {…} ]
            // 0	Object { option: 1, tags: "dfadfaf" }
            // option	1
            // tags	"dfadfaf"
            // 1	Object { option: 2, tags: "35" }
            // 2	Object { option: 6, tags: "sdfasfasd" }
            // product_variant_prices	[]



            // return $input;
            // $product->($input)->save();
            DB::commit();
            return response()->json(['message'=>"Record successfully Updated"]);
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['message'=>$e->getMessage()],500);
        }



        // return $product;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
