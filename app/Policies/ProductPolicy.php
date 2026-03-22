<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function view(?User $user, Product $product): bool
    {
        if ($product->status !== 'published') {
            return false;
        }

        return ! $product->published_at || $product->published_at->lte(now());
    }
}
