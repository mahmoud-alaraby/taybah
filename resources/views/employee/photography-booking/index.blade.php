{{-- resources/views/employee/photography-booking/index.blade.php --}}
@extends('employee.layouts.app')

@section('title', 'حجوزاتي')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 flex items-center">
                <svg class="w-8 h-8 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                حجوزاتي
            </h1>
            <p class="text-gray-600 mt-1">إدارة جميع حجوزاتي المخصصة لي</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
            <!-- إجمالي الحجوزات -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">إجمالي الحجوزات</p>
                        <p class="text-2xl font-bold">{{ $bookings->total() }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>

            <!-- جاري العمل -->
            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">جاري العمل</p>
                        <p class="text-2xl font-bold">{{ $bookings->where('status', 'in_progress')->count() }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- مكتملة -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">مكتملة</p>
                        <p class="text-2xl font-bold">{{ $bookings->where('status', 'completed')->count() }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- ديون معدومة -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl p-4 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">ديون معدومة</p>
                        <p class="text-2xl font-bold">{{ $bookings->where('status', 'bad_debt')->count() }}</p>
                    </div>
                    <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Filters -->
    <div class="filter-section mb-4">
        <div class="filter-card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-filter me-2"></i>
                    فلاتر البحث والتصفية
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('employee.photography-booking.index') }}" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <!-- الحالة -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">
                                <i class="fas fa-flag text-taiba me-1"></i>
                                الحالة
                            </label>
                            <select name="status" class="form-select">
                                <option value="">جميع الحالات</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>جاري العمل</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>تم الانتهاء</option>
                                <option value="bad_debt" {{ request('status') == 'bad_debt' ? 'selected' : '' }}>ديون معدومة</option>
                            </select>
                        </div>

                        <!-- اسم العميل -->
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">
                                <i class="fas fa-user text-taiba me-1"></i>
                                اسم العميل
                            </label>
                            <input type="text" name="client_name" class="form-control" 
                                   value="{{ request('client_name') }}" placeholder="ابحث بالاسم">
                        </div>

                        <!-- من تاريخ -->
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label">
                                <i class="fas fa-calendar-alt text-taiba me-1"></i>
                                من تاريخ
                            </label>
                            <input type="date" name="date_from" class="form-control" 
                                   value="{{ request('date_from') }}">
                        </div>

                        <!-- إلى تاريخ -->
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label">
                                <i class="fas fa-calendar-check text-taiba me-1"></i>
                                إلى تاريخ
                            </label>
                            <input type="date" name="date_to" class="form-control" 
                                   value="{{ request('date_to') }}">
                        </div>

                        <!-- أزرار الإجراءات -->
                        <div class="col-lg-2 col-md-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-taiba flex-fill">
                                    <i class="fas fa-search me-1"></i>
                                    بحث
                                </button>
                                <a href="{{ route('employee.photography-booking.index') }}" 
                                   class="btn btn-light-taiba flex-fill">
                                    <i class="fas fa-times me-1"></i>
                                    إلغاء
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Table -->
    <div class="data-table-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    قائمة الحجوزات
                </h5>
                <span class="badge bg-white text-taiba border">
                    إجمالي: {{ isset($bookings) ? $bookings->total() : 0 }} حجز
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            @if(isset($bookings) && $bookings->count() > 0)
                <!-- إضافة wrapper للإسكرول الأفقي -->
                <div class="table-wrapper">
                    <div class="table-responsive">
                        <table class="table ultra-compact-table">
                            <thead>
                                <tr>
                                    <th class="sticky-column">#</th>
                                    <th>اسم العميل</th>
                                    <th>وصف العمل</th>
                                    <th>رقم الجوال</th>
                                    <th>أول سيشن</th>
                                    <th>آخر سيشن</th>
                                    <th>عدد السيشنات</th>
                                    <th>بداية المونتاج</th>
                                    <th>تسليم أولي</th>
                                    <th>تسليم نهائي</th>
                                    <th>الحالة</th>
                                    <th class="sticky-actions text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                    <tr>
                                        <td class="booking-id sticky-column">{{ $booking->id }}</td>
                                        <td>
                                            <div class="client-info">
                                                <strong>{{ Str::limit($booking->client_name, 12) }}</strong>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="work-description">
                                                {{ Str::limit($booking->work_description, 20) }}
                                                @if($booking->work_notes)
                                                    <br><small class="text-muted">{{ Str::limit($booking->work_notes, 15) }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($booking->client_phone)
                                                <a href="tel:{{ $booking->client_phone }}" class="text-decoration-none">
                                                    <i class="fas fa-phone text-success me-1"></i>
                                                    {{ $booking->client_phone }}
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($booking->first_session)
                                                <span class="fw-semibold">{{ $booking->first_session->format('m-d') }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($booking->last_session)
                                                <span class="fw-semibold">{{ $booking->last_session->format('m-d') }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info text-white fw-bold">
                                                {{ $booking->sessions_count ?? 1 }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($booking->montage_start)
                                                <span class="fw-semibold">{{ $booking->montage_start->format('m-d') }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($booking->initial_delivery)
                                                <span class="fw-semibold">{{ $booking->initial_delivery->format('m-d') }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($booking->final_delivery)
                                                <span class="fw-semibold text-success">{{ $booking->final_delivery->format('m-d') }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $booking->status }}">
                                                @if($booking->status == 'in_progress')
                                                    جاري
                                                @elseif($booking->status == 'completed')
                                                    مكتمل
                                                @else
                                                    ديون
                                                @endif
                                            </span>
                                        </td>
                                        <td class="text-center sticky-actions">
                                            <div class="action-buttons">
                                                <a href="{{ route('employee.photography-booking.show', $booking) }}" 
                                                   class="action-btn btn-view" title="عرض">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('employee.photography-booking.edit', $booking) }}" 
                                                   class="action-btn btn-edit" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                @if($bookings->hasPages())
                    <div class="pagination-wrapper">
                        {{ $bookings->withQueryString()->links() }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-camera"></i>
                    </div>
                    <h4>لا توجد حجوزات</h4>
                    <p>لم يتم العثور على أي حجوزات مخصصة لك</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
/* متغيرات الألوان */
:root {
    --taiba-50: #fef2f2;
    --taiba-100: #fee2e2;
    --taiba-500: #dc143c;
    --taiba-600: #b91c1c;
    --taiba-700: #991b1b;
    --success-color: #10ac84;
    --warning-color: #f39c12;
    --info-color: #17a2b8;
    --light-color: #f8f9fa;
    --dark-color: #343a40;
    --shadow: 0 4px 6px rgba(220, 20, 60, 0.1);
    --shadow-hover: 0 8px 15px rgba(220, 20, 60, 0.2);
    --border-radius: 12px;
    --transition: all 0.3s ease;
}

/* Page Header */
.page-header {
    background: linear-gradient(135deg, var(--taiba-500) 0%, var(--taiba-700) 100%);
    color: white;
    padding: 2rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin: 0;
}

/* Filter Section */
.filter-card {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    overflow: hidden;
}

.filter-card .card-header {
    background: linear-gradient(135deg, var(--taiba-100) 0%, var(--taiba-50) 100%);
    color: var(--taiba-700);
    border: none;
    padding: 1rem 1.5rem;
    border-bottom: 2px solid var(--taiba-500);
}

.filter-card .card-body {
    background: white;
    padding: 1.5rem;
}

/* Data Table */
.data-table-card {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    overflow: hidden;
}

.data-table-card .card-header {
    background: linear-gradient(135deg, var(--taiba-600) 0%, var(--taiba-700) 100%);
    color: white;
    border: none;
    padding: 1rem 1.5rem;
}

.table-wrapper {
    overflow-x: auto;
    max-width: 100%;
    -webkit-overflow-scrolling: touch;
}

/* Ultra Compact Table - جدول مضغوط أكثر */
.ultra-compact-table {
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    min-width: 1000px; 
    font-size: 0.65rem; 
}

.ultra-compact-table thead {
    background: linear-gradient(135deg, var(--taiba-700) 0%, var(--dark-color) 100%);
    position: sticky;
    top: 0;
    z-index: 10;
}

.ultra-compact-table thead th {
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.55rem; 
    letter-spacing: 0.3px;
    padding: 0.4rem 0.3rem; 
    border: none;
    white-space: nowrap;
    max-width: 80px; 
}

.sticky-column {
    position: sticky;
    left: 0;
    background: inherit !important;
    z-index: 5;
    border-right: 2px solid var(--taiba-500) !important;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    min-width: 40px; 
}

.sticky-actions {
    position: sticky;
    right: 0;
    background: inherit !important;
    z-index: 5;
    border-left: 2px solid var(--taiba-500) !important;
    box-shadow: -2px 0 5px rgba(0,0,0,0.1);
    min-width: 80px; 
}

.ultra-compact-table tbody tr {
    background: white;
    transition: var(--transition);
    border-bottom: 1px solid var(--taiba-50);
}

.ultra-compact-table tbody tr:hover {
    background: linear-gradient(135deg, var(--taiba-50) 0%, var(--taiba-100) 100%);
    transform: translateY(-1px);
    box-shadow: var(--shadow);
}

.ultra-compact-table tbody td {
    padding: 0.3rem 0.25rem; 
    vertical-align: middle;
    border: none;
    font-size: 0.6rem; 
    white-space: nowrap;
    max-width: 80px; 
    overflow: hidden;
    text-overflow: ellipsis;
}

.booking-id {
    font-weight: 700;
    color: var(--taiba-600);
    font-size: 0.7rem; 
}

/* Badges مضغوطة أكثر */
.badge {
    padding: 0.2rem 0.3rem; 
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.55rem; 
    color: white;
    display: inline-flex;
    align-items: center;
    gap: 0.1rem;
    white-space: nowrap;
}

.badge-in_progress {
    background: linear-gradient(135deg, var(--warning-color) 0%, #fd7e14 100%);
}

.badge-completed {
    background: linear-gradient(135deg, var(--success-color) 0%, #20c997 100%);
}

.badge-bad_debt {
    background: linear-gradient(135deg, var(--taiba-600) 0%, var(--taiba-700) 100%);
}

/* Action Buttons مضغوطة أكثر */
.action-buttons {
    display: flex;
    gap: 0.1rem; 
    justify-content: center;
}

.action-btn {
    width: 24px; 
    height: 24px;
    border-radius: 50%;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
    cursor: pointer;
    font-size: 0.6rem; 
}

.btn-view {
    background: linear-gradient(135deg, var(--info-color) 0%, #17a2b8 100%);
    color: white;
}

.btn-edit {
    background: linear-gradient(135deg, var(--warning-color) 0%, #fd7e14 100%);
    color: white;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-hover);
}

/* Form Elements */
.form-control, .form-select {
    border: 2px solid var(--taiba-100);
    border-radius: 8px;
    transition: var(--transition);
    padding: 0.6rem;
    font-size: 0.9rem;
}

.form-control:focus, .form-select:focus {
    border-color: var(--taiba-500);
    box-shadow: 0 0 0 0.2rem rgba(220, 20, 60, 0.25);
}

.form-label {
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 0.4rem;
    font-size: 0.85rem;
}

.text-taiba {
    color: var(--taiba-600) !important;
}

/* Buttons */
.btn {
    border-radius: 8px;
    font-weight: 600;
    transition: var(--transition);
    padding: 0.6rem 1.2rem;
    font-size: 0.9rem;
}

.btn-taiba {
    background: linear-gradient(135deg, var(--taiba-500) 0%, var(--taiba-600) 100%);
    border: none;
    color: white;
}

.btn-light-taiba {
    background: var(--taiba-50);
    border: 1px solid var(--taiba-100);
    color: var(--taiba-700);
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-hover);
}

.bg-taiba {
    background-color: var(--taiba-500) !important;
}

/* Empty State */
.empty-state {
    padding: 4rem 2rem;
    text-align: center;
    background: linear-gradient(135deg, var(--taiba-50) 0%, white 100%);
}

.empty-icon {
    font-size: 4rem;
    color: var(--taiba-500);
    margin-bottom: 1rem;
}

.empty-state h4 {
    color: var(--taiba-700);
    margin-bottom: 1rem;
}

.empty-state p {
    color: var(--taiba-600);
    margin-bottom: 2rem;
}

/* Pagination */
.pagination-wrapper {
    padding: 1.5rem;
    background: var(--taiba-50);
    border-top: 1px solid var(--taiba-100);
}

/* Custom Scrollbar */
.table-wrapper::-webkit-scrollbar {
    height: 8px;
}

.table-wrapper::-webkit-scrollbar-track {
    background: var(--taiba-50);
    border-radius: 4px;
}

.table-wrapper::-webkit-scrollbar-thumb {
    background: var(--taiba-500);
    border-radius: 4px;
}

.table-wrapper::-webkit-scrollbar-thumb:hover {
    background: var(--taiba-600);
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .ultra-compact-table {
        min-width: 700px; 
    }
    
    .ultra-compact-table thead th,
    .ultra-compact-table tbody td {
        padding: 0.2rem 0.15rem; 
        font-size: 0.55rem;
    }
    
    .action-btn {
        width: 20px; 
        height: 20px;
        font-size: 0.55rem;
    }
    
    .badge {
        font-size: 0.5rem;
        padding: 0.15rem 0.25rem;
    }
}

@media (max-width: 576px) {
    .ultra-compact-table {
        min-width: 500px; 
    }
}
</style>
@endsection
