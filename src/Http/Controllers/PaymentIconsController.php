<?php

namespace Rapidez\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaymentIconsController extends Controller
{
    public function __invoke(Request $request, string $icon)
    {
        $storagePath = Str::after($request->path(), 'storage/');

        if(!$this->storage()->exists($storagePath)) {
            $iconPath = resource_path('payment-icons/' . $icon);

            if(!file_exists($iconPath)) {
                // When the icons folder hasn't been published, fall back to here
                $iconPath = __DIR__ . '/../../../resources/payment-icons/' . $icon;
            }

            abort_unless(file_exists($iconPath), 404);
            $this->storage()->put($storagePath, file_get_contents($iconPath));
        }

        return $this->storage()->response($storagePath);
    }

    public function storage()
    {
        return Storage::disk(config('rapidez.paymenticons.disk'));
    }
}
