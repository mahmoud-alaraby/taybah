<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * العلاقة مع الصلاحيات
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
                    ->withTimestamps();
    }

    /**
     * العلاقة مع الموظفين
     */
    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_roles')
                    ->withTimestamps()
                    ->withPivot('assigned_by', 'assigned_at');
    }

    /**
     * العلاقة مع تعيين الأدوار
     */
    public function employeeRoles(): HasMany
    {
        return $this->hasMany(EmployeeRole::class);
    }

    /**
     * التحقق من وجود صلاحية معينة
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('name', $permission)->exists();
    }

    /**
     * إضافة صلاحية إلى الدور
     */
    public function givePermission(Permission $permission): void
    {
        $this->permissions()->syncWithoutDetaching($permission);
    }

    /**
     * إزالة صلاحية من الدور
     */
    public function revokePermission(Permission $permission): void
    {
        $this->permissions()->detach($permission);
    }

    /**
     * مزامنة الصلاحيات
     */
    public function syncPermissions(array $permissions): void
    {
        $this->permissions()->sync($permissions);
    }

    /**
     * التحقق من النشاط
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * scope للأدوار النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * scope للبحث
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%');
        });
    }
}

