<?php
// app/Models/PotentialCustomerClassification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PotentialCustomerClassification extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'color_class',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * scope للتصنيفات النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * scope مرتبة حسب الترتيب
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * الحصول على جميع التصنيفات كـ options للـ select
     */
    public static function getOptions(): array
    {
        return self::active()->ordered()->pluck('display_name', 'name')->toArray();
    }

    /**
     * الحصول على التصنيفات مع الألوان
     */
    public static function getWithColors(): array
    {
        return self::active()->ordered()->get()->map(function ($classification) {
            return [
                'name' => $classification->name,
                'display_name' => $classification->display_name,
                'color_class' => $classification->color_class,
                'description' => $classification->description
            ];
        })->toArray();
    }

    /**
     * التحقق من النشاط
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}