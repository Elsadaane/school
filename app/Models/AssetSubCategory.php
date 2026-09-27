<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetSubCategory extends Model
{
    use HasFactory;
    protected $guarded = [];

    // القسم الفرعي بينتمي لقسم رئيسي واحد
    public function category() { 
        return $this->belongsTo(AssetCategory::class, 'asset_category_id'); 
    }

    // القسم الفرعي بيحتوي على أصول كتير
    public function assets() { 
        return $this->hasMany(Asset::class); 
    }

}