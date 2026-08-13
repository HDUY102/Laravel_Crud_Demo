<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;

class ProductRepository extends BaseRepository
{
    protected function getModelClass(): string
    {
        return Product::class;
    }
}