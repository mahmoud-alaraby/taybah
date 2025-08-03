<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerResponseCategory extends Model
{
    use HasFactory;

    protected $table = 'customer_response_categories';

    protected $fillable = [
        'name',
        'icon', // إذا أردت دعم أيقونة التصنيف
    ];

    // علاقة مع الردود الجاهزة (واحد إلى متعدد)
    public function responses()
    {
        return $this->hasMany(CustomerResponse::class, 'category_id');
    }
}
