<?php
// app/Models/PotentialCustomer.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PotentialCustomer extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'customer_name',
        'work_description',
        'phone',
        'whatsapp_link',
        'customer_classifications',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'customer_classifications' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * العلاقة مع الموظف
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class)->withDefault([
            'name' => 'غير محدد',
            'employee_id' => '-'
        ]);
    }

    /**
     * تكوين رابط الواتساب تلقائياً
     */
    public function generateWhatsappLink(): string
    {
        if (empty($this->phone)) {
            return '';
        }

        // تنظيف الرقم من الفواصل والمسافات والعلامات
        $cleanPhone = preg_replace('/[^0-9]/', '', $this->phone);
        
        // إضافة كود المملكة إذا لم يكن موجود
        if (!str_starts_with($cleanPhone, '966')) {
            if (str_starts_with($cleanPhone, '0')) {
                $cleanPhone = '966' . substr($cleanPhone, 1);
            } else {
                $cleanPhone = '966' . $cleanPhone;
            }
        }

        return "https://api.whatsapp.com/send/?phone={$cleanPhone}";
    }

    /**
     * حفظ الرابط عند تحديث الرقم
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->whatsapp_link = $model->generateWhatsappLink();
        });
    }

    /**
     * الحصول على أسماء التصنيفات المترجمة
     */
    public function getClassificationNamesAttribute(): array
    {
        if (empty($this->customer_classifications)) {
            return [];
        }

        return collect($this->customer_classifications)->map(function ($classification) {
            $classificationModel = PotentialCustomerClassification::where('name', $classification)->first();
            return $classificationModel ? $classificationModel->display_name : $classification;
        })->toArray();
    }

    /**
     * الحصول على التصنيفات مع الألوان
     */
    public function getClassificationsBadgesAttribute(): array
    {
        if (empty($this->customer_classifications)) {
            return [];
        }

        return collect($this->customer_classifications)->map(function ($classification) {
            $classificationModel = PotentialCustomerClassification::where('name', $classification)->first();
            return [
                'name' => $classification,
                'display_name' => $classificationModel ? $classificationModel->display_name : $classification,
                'color_class' => $classificationModel ? $classificationModel->color_class : 'bg-gray-100 text-gray-800'
            ];
        })->toArray();
    }

    /**
     * التحقق من وجود تصنيف معين
     */
    public function hasClassification(string $classification): bool
    {
        return in_array($classification, $this->customer_classifications ?? []);
    }

    /**
     * إضافة تصنيف جديد
     */
    public function addClassification(string $classification): void
    {
        $classifications = $this->customer_classifications ?? [];
        
        if (!in_array($classification, $classifications)) {
            $classifications[] = $classification;
            $this->customer_classifications = $classifications;
            $this->save();

            // تسجيل في التاريخ
            $this->logClassificationChange($classification, 'added');
        }
    }

    /**
     * إزالة تصنيف
     */
    public function removeClassification(string $classification): void
    {
        $classifications = $this->customer_classifications ?? [];
        
        if (($key = array_search($classification, $classifications)) !== false) {
            unset($classifications[$key]);
            $this->customer_classifications = array_values($classifications);
            $this->save();

            // تسجيل في التاريخ
            $this->logClassificationChange($classification, 'removed');
        }
    }

    /**
     * تحديث التصنيفات
     */
    public function updateClassifications(array $classifications): void
    {
        $oldClassifications = $this->customer_classifications ?? [];
        $this->customer_classifications = $classifications;
        $this->save();

        // تسجيل التغييرات
        $added = array_diff($classifications, $oldClassifications);
        $removed = array_diff($oldClassifications, $classifications);

        foreach ($added as $classification) {
            $this->logClassificationChange($classification, 'added');
        }

        foreach ($removed as $classification) {
            $this->logClassificationChange($classification, 'removed');
        }
    }

    /**
     * تسجيل تغيير التصنيف في التاريخ
     */
    private function logClassificationChange(string $classification, string $action): void
    {
        \DB::table('potential_customer_classification_history')->insert([
            'potential_customer_id' => $this->id,
            'employee_id' => auth('employee')->id() ?? auth('admin')->id(),
            'classification_name' => $classification,
            'action' => $action,
            'created_at' => now(),
        ]);
    }

    /**
     * scope للبحث
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('customer_name', 'like', '%' . $search . '%')
              ->orWhere('phone', 'like', '%' . $search . '%')
              ->orWhere('work_description', 'like', '%' . $search . '%');
        });
    }

    /**
     * scope للتصنيف
     */
    public function scopeWithClassification($query, $classification)
    {
        return $query->whereRaw('JSON_CONTAINS(customer_classifications, ?)', [json_encode($classification)]);
    }

    /**
     * scope للموظف الحالي
     */
    public function scopeForCurrentEmployee($query)
    {
        if (auth('employee')->check()) {
            return $query->where('employee_id', auth('employee')->id());
        }
        return $query;
    }

    /**
     * scope للموظفين النشطين
     */
    public function scopeWithActiveEmployees($query)
    {
        return $query->whereHas('employee', function ($q) {
            $q->where('status', 'active');
        });
    }

    /**
     * الحصول على إحصائيات التصنيفات
     */
    public static function getClassificationStats(int $employeeId = null): array
    {
        $query = self::query();
        
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $customers = $query->get();
        $stats = [];

        // الحصول على جميع التصنيفات المتاحة
        $allClassifications = PotentialCustomerClassification::orderBy('sort_order')->get();

        foreach ($allClassifications as $classification) {
            $count = $customers->filter(function ($customer) use ($classification) {
                return $customer->hasClassification($classification->name);
            })->count();

            $stats[$classification->name] = [
                'name' => $classification->name,
                'display_name' => $classification->display_name,
                'color_class' => $classification->color_class,
                'count' => $count
            ];
        }

        return $stats;
    }

    /**
     * الحصول على العملاء حسب التصنيف مع الإحصائيات
     */
    public static function getByClassificationWithStats(string $classification, int $employeeId = null)
    {
        $query = self::with('employee');

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        return $query->withClassification($classification)
                    ->orderBy('created_at', 'desc')
                    ->get();
    }
    public function customerChat()
{
    return $this->hasMany(CustomerChat::class, 'potential_customer_id');
}

public function activeChatWithEmployee($employeeId)
{
    return $this->customerChat()
                ->where('employee_id', $employeeId)
                ->whereIn('status', ['pending', 'active'])
                ->first();
}
}