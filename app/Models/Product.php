<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\{UploadMedia2, UploadVideoTrait};
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
class Product extends Model implements TranslatableContract {
    use HasFactory, UploadMedia2, UploadVideoTrait, Translatable;
    protected $table = 'products';
    protected $fillable = [
        'slug',
        'type_id',
        'category_id',
        'brand_id',
        'price',
        'price_before_discount',
        'stock',
        'sku',
        'featured',
        'status',
        'published_at',
        'client_number',
        'service_type',
        // Shop2TopUp offer id (column name in DB is itemID)
        'itemID',

        'erp_id'
        
        
    ];
    protected $with = ['translations'];

    public $translatedAttributes = [
        'name',
        'description',
        'short_description',
        'meta_title',
        'meta_description'
    ];

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function galleries()
    {
        return $this->morphMany(Gallery::class, 'galleriable');
    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function sections()
    {
        return $this->belongsToMany(Section::class, 'product_section');
    }

    /*public function categories() {
        return $this->belongsToMany(Category::class, 'category_product');
    }*/

    public function videos()
    {
        return $this->hasMany(ProductVideo::class);
    }

    public function diamondCodes()
    {
        return $this->hasMany(DiamondCode::class, 'product_id');
    }

    public function manualPaymentRequests()
    {
        return $this->hasMany(ManualPaymentRequest::class, 'product_id');
    }
    
    public function getImageUrl()
{

    return asset('public/uploads/product/68f671923ab1a.jpg');
}

}
