<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    protected $appends = [
        'total_quantity',
        'image_path',
        'total_quantity_by_unit_id'
    ];

    public function units()
    {
        return $this->belongsToMany(Unit::class)->withPivot('amount');
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function getTotalQuantityAttribute()
    {
        $product = Product::find($this->id);

        $total = array();
        foreach ($product->units as $unit) {
            $total[] = $unit->modifier * $unit->pivot->amount;
        }

        $total_quantity = array_sum($total);

        return $total_quantity;
    }

    public function getImagePathAttribute()
    {
        $var = empty($this->image->path) ? null : $this->image->path;

        return $var;
    }

    
    public function getTotalQuantityByUnitIdAttribute()
    {
        $currentURL = URL::full();
        $currentURL = parse_url($currentURL);

        if (isset($currentURL['query'])) {
            parse_str($currentURL['query'], $query);
            $unit_modifier = Unit::find($query['unit_id']);
            $TotalQuantityByUnitId = $this->getTotalQuantityAttribute() / $unit_modifier->modifier;
            return $TotalQuantityByUnitId;
        }

        return null;
    }

}