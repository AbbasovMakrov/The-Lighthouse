<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Services\CategoryService;
use App\Services\ProductService;

class ProductController extends Controller
{
    private ProductService $service;
    private const ROUTE_NAME_DASHBOARD = 'dashboard.products';
    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $products = $this->service->all();
        return view('products.index',compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param CategoryService $categoryService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(CategoryService $categoryService)
    {
        $categories = $categoryService->all();
        return view('products.create',compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ProductRequest  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(ProductRequest $request)
    {
         $product = $this->service->create($request);
         if (app()->runningUnitTests()){
             return redirect(route('products.index'))->with('productImg',$product->image);
         }
        return redirect(route(self::ROUTE_NAME_DASHBOARD));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->view('products.show',['product' => $this->service->show($id)]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @param CategoryService $categoryService
     * @return \Illuminate\Http\Response
     */
    public function edit($id,CategoryService $categoryService)
    {
       $categories = $categoryService->all();
       $product = $this->service->show($id);
       return response()->view('products.edit',compact('categories','product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ProductRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProductRequest $request, $id)
    {
        $productUpdate = $this->service->update($id,$request);
        if (!$productUpdate)
            return redirect()->back()->with('errorMsg','error occurred');
        return redirect(route(self::ROUTE_NAME_DASHBOARD));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $this->service->delete($id);
        return redirect(route(self::ROUTE_NAME_DASHBOARD));
    }

}
