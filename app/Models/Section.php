<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
class Section extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    public $translatedAttributes = ['name', 'description'];

    protected $fillable = ['order', 'design_type'];

    public function categories() {
        return $this->belongsToMany(Category::class, 'category_section');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_section')
            ->websiteVisible();
    }
}
