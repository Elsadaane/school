<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    use HasFactory;
    protected $guarded = []; // للسماح بالحفظ المباشر

    // القسم الرئيسي بيحتوي على أقسام فرعية كتير
    public function subCategories() { 
        return $this->hasMany(AssetSubCategory::class); 
    }

    // القسم الرئيسي بيحتوي على أصول كتير
    public function assets() { 
        return $this->hasMany(Asset::class); 
    }
}