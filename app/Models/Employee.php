<?php
// app/Models/Employee.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'employee_id',
        'department',
        'position',
        'salary',
        'hire_date',
        'status',
        'avatar',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'hire_date' => 'date',
            'salary' => 'decimal:2',
            'password' => 'hashed',
        ];
    }

    /**
     * العلاقة مع الأدوار
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'employee_roles')
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
     * الحصول على جميع الصلاحيات من خلال الأدوار
     */
    public function permissions()
    {
        return $this->roles()->with('permissions')->get()
                    ->pluck('permissions')->flatten()->unique('id');
    }

    /**
     * التحقق من وجود صلاحية معينة
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->contains('name', $permission);
    }

    /**
     * التحقق من وجود دور معين
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->contains('name', $roleName);
    }

    /**
     * التحقق من النشاط
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * الحصول على رابط الصورة الشخصية
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/avatars/' . $this->avatar);
        }
        return asset('assets/images/default-avatar.png');
    }

    /**
     * الحصول على اسم القسم مترجم
     */
    public function getDepartmentNameAttribute(): string
    {
        $departments = [
            'financial' => 'الشؤون المالية',
            'customers' => 'خدمة العملاء',
            'operations' => 'العمليات',
            'production' => 'الإنتاج',
            'design' => 'التصميم',
            'communication' => 'التواصل',
            'tasks' => 'إدارة المهام',
            'scheduling' => 'الجدولة',
        ];

        return $departments[$this->department] ?? $this->department;
    }

    /**
     * scope للموظفين النشطين
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
              ->orWhere('email', 'like', '%' . $search . '%')
              ->orWhere('employee_id', 'like', '%' . $search . '%')
              ->orWhere('department', 'like', '%' . $search . '%')
              ->orWhere('position', 'like', '%' . $search . '%');
        });
    }
}