<?php

namespace App\Http\Controllers\Admin\Shopify;

use App\Http\Controllers\Controller;
use App\Services\ShopifyService;
use Illuminate\Support\Facades\Log;

class ShopifyController extends Controller
{
    protected $shopifyService;

    public function __construct(ShopifyService $shopifyService)
    {
        $this->shopifyService = $shopifyService;
    }
} 