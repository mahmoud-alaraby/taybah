<?php 
// app/Models/Permission.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'system_category',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * العلاقة مع الأدوار
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions')
                    ->withTimestamps();
    }

    /**
     * تجميع الصلاحيات حسب الفئة
     */
    public static function getGroupedPermissions(): array
    {
        return self::all()->groupBy('system_category')->toArray();
    }

    /**
     * الحصول على أسماء الفئات مترجمة
     */
    public function getCategoryNameAttribute(): string
    {
        $categories = [
            'financial' => 'الأنظمة المالية',
            'customers' => 'أنظمة العملاء',
            'operations' => 'الأنظمة التشغيلية',
            'production' => 'أنظمة الإنتاج',
            'design' => 'أنظمة التصميم',
            'communication' => 'أنظمة التواصل',
            'tasks' => 'إدارة المهام',
            'scheduling' => 'أنظمة الجدولة',
        ];

        return $categories[$this->system_category] ?? $this->system_category;
    }

    /**
     * scope للبحث
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('display_name', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%');
        });
    }

    /**
     * scope للفئة
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('system_category', $category);
    }
}

