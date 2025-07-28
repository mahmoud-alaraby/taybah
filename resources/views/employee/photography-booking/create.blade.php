{{-- resources/views/admin/photography-booking/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'إضافة حجز جديد')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <div class="card shadow-sm">
                <div class="card-header bg-taiba text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-plus-circle"></i>
                            إضافة حجز جديد
                        </h5>
                        <a href="{{ route('admin.photography-booking.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-right"></i>
                            <span class="d-none d-sm-inline">العودة للقائمة</span>
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('admin.photography-booking.store') }}" method="POST" novalidate>
                        @csrf
                        
                        <!-- معلومات أساسية -->
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h6 class="text-taiba">
                                    <i class="fas fa-info-circle"></i>
                                    المعلومات الأساسية
                                </h6>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="status" class="form-label required">
                                        <i class="fas fa-flag text-muted"></i>
                                        حالة العمل
                                    </label>
                                    <select name="status" id="status" 
                                            class="form-select @error('status') is-invalid @enderror" required>
                                        <option value="in_progress" {{ old('status', 'in_progress') == 'in_progress' ? 'selected' : '' }}>
                                            جاري العمل
                                        </option>
                                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>
                                            تم الانتهاء
                                        </option>
                                        <option value="bad_debt" {{ old('status') == 'bad_debt' ? 'selected' : '' }}>
                                            ديون معدومة
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="client_name" class="form-label required">
                                        <i class="fas fa-user text-muted"></i>
                                        اسم العميل
                                    </label>
                                    <input type="text" name="client_name" id="client_name" 
                                           class="form-control @error('client_name') is-invalid @enderror" 
                                           value="{{ old('client_name') }}" required
                                           placeholder="الاسم الكامل للعميل">
                                    @error('client_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="client_phone" class="form-label">
                                        <i class="fas fa-phone text-muted"></i>
                                        رقم الجوال
                                    </label>
                                    <input type="tel" name="client_phone" id="client_phone" 
                                           class="form-control @error('client_phone') is-invalid @enderror" 
                                           value="{{ old('client_phone') }}"
                                           placeholder="05xxxxxxxx">
                                    @error('client_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="assigned_person_id" class="form-label">
                                        <i class="fas fa-user-tie text-muted"></i>
                                        المسؤول عن التنفيذ
                                    </label>
                                    <select name="assigned_person_id" id="assigned_person_id" 
                                            class="form-select @error('assigned_person_id') is-invalid @enderror">
                                        <option value="">اختر المسؤول</option>
                                        @if(isset($employees))
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee->id }}" 
                                                        {{ old('assigned_person_id') == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->name }} ({{ $employee->employee_id }})
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('assigned_person_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- وصف العمل والاتفاق -->
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h6 class="text-success">
                                    <i class="fas fa-file-alt"></i>
                                    وصف العمل والاتفاق
                                </h6>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="work_description" class="form-label required">
                                        <i class="fas fa-align-left text-muted"></i>
                                        وصف العمل
                                    </label>
                                    <textarea name="work_description" id="work_description" rows="4" 
                                              class="form-control @error('work_description') is-invalid @enderror"
                                              placeholder="اكتب وصفاً تفصيلياً للعمل المطلوب" required>{{ old('work_description') }}</textarea>
                                    @error('work_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="agreement" class="form-label">
                                        <i class="fas fa-handshake text-muted"></i>
                                        الاتفاق
                                    </label>
                                    <textarea name="agreement" id="agreement" rows="3" 
                                              class="form-control @error('agreement') is-invalid @enderror"
                                              placeholder="تفاصيل الاتفاق مع العميل">{{ old('agreement') }}</textarea>
                                    @error('agreement')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="work_notes" class="form-label">
                                        <i class="fas fa-sticky-note text-muted"></i>
                                        ملاحظات العمل
                                    </label>
                                    <textarea name="work_notes" id="work_notes" rows="3" 
                                              class="form-control @error('work_notes') is-invalid @enderror"
                                              placeholder="أي ملاحظات إضافية">{{ old('work_notes') }}</textarea>
                                    @error('work_notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- جدولة السيشنات -->
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h6 class="text-info">
                                    <i class="fas fa-calendar-alt"></i>
                                    جدولة السيشنات
                                </h6>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="first_session" class="form-label">
                                        <i class="fas fa-calendar-plus text-muted"></i>
                                        أول سيشن
                                    </label>
                                    <input type="date" name="first_session" id="first_session" 
                                           class="form-control @error('first_session') is-invalid @enderror" 
                                           value="{{ old('first_session') }}">
                                    @error('first_session')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="last_session" class="form-label">
                                        <i class="fas fa-calendar-minus text-muted"></i>
                                        آخر سيشن
                                    </label>
                                    <input type="date" name="last_session" id="last_session" 
                                           class="form-control @error('last_session') is-invalid @enderror" 
                                           value="{{ old('last_session') }}">
                                    @error('last_session')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="sessions_count" class="form-label">
                                        <i class="fas fa-list-ol text-muted"></i>
                                        عدد السيشنات
                                    </label>
                                    <input type="number" name="sessions_count" id="sessions_count" 
                                           class="form-control @error('sessions_count') is-invalid @enderror" 
                                           value="{{ old('sessions_count', 1) }}" min="1">
                                    @error('sessions_count')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- جدولة المونتاج والتسليم -->
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h6 class="text-warning">
                                    <i class="fas fa-video"></i>
                                    جدولة المونتاج والتسليم
                                </h6>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="montage_start" class="form-label">
                                        <i class="fas fa-play text-muted"></i>
                                        بداية المونتاج
                                    </label>
                                    <input type="date" name="montage_start" id="montage_start" 
                                           class="form-control @error('montage_start') is-invalid @enderror" 
                                           value="{{ old('montage_start') }}">
                                    @error('montage_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="initial_delivery" class="form-label">
                                        <i class="fas fa-clock text-muted"></i>
                                        تسليم أولي
                                    </label>
                                    <input type="date" name="initial_delivery" id="initial_delivery" 
                                           class="form-control @error('initial_delivery') is-invalid @enderror" 
                                           value="{{ old('initial_delivery') }}">
                                    @error('initial_delivery')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="final_delivery" class="form-label">
                                        <i class="fas fa-check text-muted"></i>
                                        تسليم نهائي
                                    </label>
                                    <input type="date" name="final_delivery" id="final_delivery" 
                                           class="form-control @error('final_delivery') is-invalid @enderror" 
                                           value="{{ old('final_delivery') }}">
                                    @error('final_delivery')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- معلومات إضافية -->
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h6 class="text-secondary">
                                    <i class="fas fa-cogs"></i>
                                    معلومات إضافية (اختيارية)
                                </h6>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="booking_date" class="form-label">
                                        <i class="fas fa-calendar text-muted"></i>
                                        تاريخ الحجز
                                    </label>
                                    <input type="date" name="booking_date" id="booking_date" 
                                           class="form-control @error('booking_date') is-invalid @enderror" 
                                           value="{{ old('booking_date', date('Y-m-d')) }}">
                                    @error('booking_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="booking_time" class="form-label">
                                        <i class="fas fa-clock text-muted"></i>
                                        وقت الحجز
                                    </label>
                                    <input type="time" name="booking_time" id="booking_time" 
                                           class="form-control @error('booking_time') is-invalid @enderror" 
                                           value="{{ old('booking_time') }}">
                                    @error('booking_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="duration_hours" class="form-label">
                                        <i class="fas fa-hourglass-half text-muted"></i>
                                        مدة الحجز (ساعة)
                                    </label>
                                    <input type="number" name="duration_hours" id="duration_hours" 
                                           class="form-control @error('duration_hours') is-invalid @enderror" 
                                           value="{{ old('duration_hours', 1) }}" min="1" max="24">
                                    @error('duration_hours')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="location" class="form-label">
                                        <i class="fas fa-map-marker-alt text-muted"></i>
                                        الموقع
                                    </label>
                                    <input type="text" name="location" id="location" 
                                           class="form-control @error('location') is-invalid @enderror" 
                                           value="{{ old('location') }}"
                                           placeholder="مكان تنفيذ الحجز">
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="notes" class="form-label">
                                        <i class="fas fa-sticky-note text-muted"></i>
                                        ملاحظات إضافية
                                    </label>
                                    <textarea name="notes" id="notes" rows="3" 
                                              class="form-control @error('notes') is-invalid @enderror"
                                              placeholder="أي ملاحظات أو تعليمات خاصة">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- أزرار الإجراءات -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex flex-column flex-sm-row gap-2 justify-content-end">
                                    <a href="{{ route('admin.photography-booking.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i>
                                        إلغاء
                                    </a>
                                    <button type="submit" class="btn btn-taiba">
                                        <i class="fas fa-save"></i>
                                        حفظ الحجز
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --taiba-50: #fef2f2;
    --taiba-100: #fee2e2;
    --taiba-500: #dc143c;
    --taiba-600: #b91c1c;
    --taiba-700: #991b1b;
    --border-radius: 12px;
    --transition: all 0.3s ease;
}

.required::after {
    content: " *";
    color: var(--taiba-500);
}

.form-label {
    font-weight: 600;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.form-label i {
    margin-right: 0.5rem;
}

.card {
    border: none;
    border-radius: var(--border-radius);
}

.card-header {
    border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
}

.section-card {
    background: var(--taiba-50);
    border: 1px solid var(--taiba-100);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    margin-bottom: 1rem;
}

.section-header {
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--taiba-100);
}

.section-header h6 {
    margin: 0;
    font-weight: 700;
}

.form-control, .form-select {
    border: 2px solid var(--taiba-100);
    border-radius: 8px;
    transition: var(--transition);
    padding: 0.75rem;
}

.form-control:focus, .form-select:focus {
    border-color: var(--taiba-500);
    box-shadow: 0 0 0 0.2rem rgba(220, 20, 60, 0.25);
}

.btn {
    border-radius: 8px;
    font-weight: 600;
    transition: var(--transition);
    padding: 0.75rem 1.5rem;
}

.btn-taiba {
    background: linear-gradient(135deg, var(--taiba-500) 0%, var(--taiba-600) 100%);
    border: none;
    color: white;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

@media (max-width: 576px) {
    .card-body {
        padding: 1rem;
    }
    
    .section-card {
        padding: 1rem;
    }
    
    .btn {
        font-size: 0.9rem;
    }
    
    .form-label {
        font-size: 0.85rem;
    }
    
    h6 {
        font-size: 1rem;
    }
}

.invalid-feedback {
    display: block;
}
</style>
@endsection
