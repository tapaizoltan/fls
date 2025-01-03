<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

use App\Models\Brand;

class Product extends Model
{
    protected $guarded = [];
    use SoftDeletes;

    //protected $with = ['productmaincategory','productsubcategory', 'brand'];

    // public function brand()
    // {
    //     return $this->belongsTo(Brand::class, 'brand_id', 'id');
    // }

    // public function productmaincategory()
    // {
    //     return $this->belongsTo(Productmaincategory::class, 'productmaincategory_id', 'id');
    // }

    // public function productsubcategory()
    // {
    //     return $this->belongsTo(Productsubcategory::class, 'productsubcategory_id', 'id');
    // }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function supplier(): HasOneThrough
    {
        return $this->hasOneThrough(Supplier::class, Brand::class, 'supplier_id', 'id', );
    }

    public function productmaincategory()
    {
        return $this->belongsTo(Productmaincategory::class);
    }

    public function productsubcategory()
    {
        return $this->belongsTo(Productsubcategory::class);
    }

    public function productprices()
    {
        return $this->hasMany(Productprice::class);
    }

    public static function getGroupedProducts(): array
{
    return Brand::query()
        ->with(['products' => function ($query) {
            $query->select('id', 'width', 'height', 'structure', 'rim_diameter', 'brand_id', 'productmaincategory_id', 'productsubcategory_id')
                ->with(['productmaincategory:id,name', 'productsubcategory:id,name']); // Betöltjük a fő- és alkategóriát
        }])
        ->get(['id', 'name'])
        ->mapWithKeys(function ($brand) {
            return [
                $brand->name => $brand->products->mapWithKeys(function ($product) {
                    // Termék fő- és alkategória
                    $mainCategoryName = $product->productmaincategory->name ?? 'N/A';
                    $subCategoryName = $product->productsubcategory->name ?? 'N/A';
                    
                    // Az új név formázása
                    $formattedName = sprintf(
                        '%d/%d%s%d (%s/%s)', // Terméknév és kategóriák
                        $product->width,
                        $product->height,
                        strtoupper($product->structure),
                        $product->rim_diameter,
                        $mainCategoryName,
                        $subCategoryName
                    );
                    return [$product->id => $formattedName];
                }),
            ];
        })
        ->toArray();
}
}
