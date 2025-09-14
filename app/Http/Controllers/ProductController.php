<?php

namespace App\Http\Controllers;

use App\DTOs\ProductDTO;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Interfaces\ProductServiceInterface;

class ProductController extends Controller
{
    public function __construct(
        private ProductServiceInterface $productService
    ) {}

    public function index()
    {
        $products = $this->productService->index();
        return $this->success(
            ProductResource::collection($products),
            'Mahsulotlar muvaffaqiyatli olindi'
        );
    }

    public function search()
    {
        $query = request()->query('q', '');
        $products = $this->productService->search($query);
        return $this->success(
            ProductResource::collection($products),
            'Mahsulotlar topildi'
        );
    }

    public function store(StoreProductRequest $request)
    {
        $productDTO = ProductDTO::fromArray($request->validated());
        $product = $this->productService->create($productDTO->toArray());

        return $this->success(
            new ProductResource($product),
            'Mahsulot muvaffaqiyatli yaratildi',
            201
        );
    }

    public function show(string $id)
    {
        $product = $this->productService->show($id);
        return $this->success(
            new ProductResource($product),
            'Mahsulot muvaffaqiyatli olindi'
        );
    }

    public function update(UpdateProductRequest $request, string $id)
    {
        $product = $this->productService->show($id);
        $productDTO = ProductDTO::fromArray($request->validated());
        $updatedProduct = $this->productService->update($product, $productDTO->toArray());

        return $this->success(
            new ProductResource($updatedProduct),
            'Mahsulot muvaffaqiyatli yangilandi'
        );
    }

    public function destroy(string $id)
    {
        $product = $this->productService->show($id);
        $this->productService->delete($product);

        return $this->success(
            [],
            'Mahsulot muvaffaqiyatli ochirildi'
        );
    }
}