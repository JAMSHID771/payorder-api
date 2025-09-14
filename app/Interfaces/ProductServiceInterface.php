<?php

namespace App\Interfaces;

use App\Models\Product;

interface ProductServiceInterface
{
    public function index();

    public function show($id);

    public function create($data);

    public function update(Product $product, $data);

    public function delete(Product $product);

    public function search($query);
}
