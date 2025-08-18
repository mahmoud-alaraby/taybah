@extends('admin.layouts.app')

@section('title', 'الرئيسية')
@section('page-title', 'لوحة التحكم')
@section('page-subtitle', 'مرحباً ' . auth("admin")->user()->name . '، إليك نظرة عامة على النظام')

@section('content')
 {{-- ستايلات Tailwind إضافية --}}
    <style>
    .circle-progress {
        position: relative;
        width: 130px;
        height: 130px;
    }
    .circle-svg {
        transform: rotate(-90deg);
    }
    .circle-value {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        font-weight: bold;
        font-size: 2rem;
    }
    </style>

    
<style>
    .stat-circle { width:82px; height:82px; position:relative; margin:auto; }
    .stat-circle svg { width:82px; height:82px; }
    .stat-circle .stat-value {
        position:absolute;
        top:50%; left:50%;
        transform:translate(-50%, -50%);
        font-weight:700;
        font-size:1.5rem;
        color:#262626;
    }
    .stats-grid {
        display:grid;
        grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));
        gap:28px;
        margin-bottom:2.5rem;
    }
    .stat-block {
        background:linear-gradient(135deg,#f3f4f6 70%,#fff 100%);
        box-shadow:0 2px 16px 0 #0001;
        border-radius:1.2rem;
        padding:1.6rem 1rem 1.2rem 1rem;
        display:flex;
        flex-direction:column;
        align-items:center;
        position:relative;
        overflow:hidden;
    }
    .stat-block .stat-icon {
        margin-bottom:.5rem;
        margin-top:-.2rem;
        font-size:2.1rem;
    }
    .stat-block .stat-label {
        font-size:1.04rem;
        color:#555;
        margin-top:.3rem;
        font-weight:500;
        text-align:center;
    }
    .stat-block .stat-desc {
        font-size:0.89rem;
        color:#888;
        margin-top:.8rem;
        font-weight:400;
        text-align:center;
    }
</style>


<style>
.stats-grid {display: grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap: 14px; margin-bottom: 2rem;}
.stat-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 0 8px #ededed;
    min-height: 120px;
    padding: 1.1rem .7rem .5rem .7rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    border-top: 5px solid; /* ستعدل بالإنلاين لكل كارت */
    overflow: hidden;
}
.stat-icon {
    width: 32px; height: 32px;
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg,#26ffe5,#484fc2);
    color: #fff;
    font-size: 1.5rem;
    border-radius: 50%;
    margin-bottom: .2rem;
}
.stat-value {
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 0.3rem;
}
.stat-mini {font-size:0.9rem;font-weight:500;margin-top:0.1rem; color:#7c7c7c;}
.stat-emp-mini {font-size:0.94rem; color:#6062aa; margin-top:.1rem;}
.stat-small {
    font-size: 0.86rem;
    color: #aaa;
    margin-top: .05rem;
    font-weight:400;
}
.stat-arrow-up {
    color: #16a34a;
    background: linear-gradient(135deg,#16a34a,#8fd93b);
}
.stat-arrow-down {
    color: #ea580c;
    background: linear-gradient(135deg,#ea580c,#fbbf24);
}
.tiny-nums {font-size:.92rem; color:#6b6b6b;}
.stat-card label {font-size:.82rem;color:#737;}
</style>

<style>
.stats-grid {display: grid; grid-template-columns: repeat(auto-fit, minmax(160px,1fr)); gap: 14px; margin-bottom: 2rem;}
.stat-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 0 8px #ededed;
    min-height: 100px;
    padding: .7rem .35rem .3rem .35rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    border-top: 5px solid;
    overflow: hidden;
}
.stat-icon {
    width: 28px; height: 28px;
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg,#26ffe5,#484fc2);
    color: #fff;
    font-size: 1.20rem;
    border-radius: 50%;
    margin-bottom: .1rem;
}
.stat-mini {font-size:0.87rem;font-weight:500;margin-top:0.04rem; color:#7c7c7c;}
.stat-small {
    font-size: 0.83rem;
    color: #aaa;
    margin-top: 0.03rem;
    font-weight:400;
}
label {font-size:.78rem;color:#737;}
</style>

<style>
.stats-grid {display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 16px; margin-bottom: 2.5rem;}
.stat-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 0 10px #ededed;
    min-height: 92px;
    padding: .65rem .3rem .25rem .3rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    border-top: 5px solid;
    overflow: hidden;
    transition: box-shadow 0.2s;
}
.stat-card:hover {box-shadow:0 0 18px #eab3083a;}
.stat-icon {
    width: 22px; height: 22px;
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg,#d71f32,#fbbf24);
    color: #fff;
    font-size: 1.12rem;
    border-radius: 50%;
    margin-bottom: .1rem;
}
.stat-mini {font-size:0.81rem;font-weight:500;margin-top:0.02rem; color:#7c7c7c;}
label {font-size:.78rem;color:#737;}
.stat-small {
    font-size: 0.82rem;
    color: #555;
    margin-top: 0.04rem;
    font-weight:400;
}
.stats-row-gap {margin-bottom:50px : }
.filter-row {margin-bottom:28px;}
.note-box {
    background:#f9fafb; border-radius:.92rem; padding:.82rem 1rem .66rem 1rem; margin-bottom:22px;
    color:#e11d48;font-weight:500;font-size:1.01rem;text-align:center;
    box-shadow:0 0 6px #ffd6db;
    max-width:420px;margin:auto;
}
.select-filter {border-radius:.5rem; padding:.32rem .9rem; border:1px solid #ececec;}
.filter-btn {
    border-radius:.7rem; padding:.39rem 1.5rem;font-size:.99rem;
    background: linear-gradient(135deg,#d71f32,#fbbf24); color:#fff; font-weight:600; border:none;
    box-shadow:0 2px 8px #fbbf2433;transition:box-shadow 0.2s;
}
.filter-btn:hover {box-shadow:0 0 18px #fbbf2499;}
.curves-grid {
    display: grid; grid-template-columns: 2fr 1fr;
    gap: 22px; margin-bottom:38px;
    align-items:center;
}
@media (max-width:900px) {
    .curves-grid {grid-template-columns:1fr;}
    .stats-grid {grid-template-columns:repeat(2,minmax(0,1fr));}
}
</style>

<div class="space-y-6">
    <!-- Welcome Card -->
    <div class=" overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-16 w-16 bg-red-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-home text-white text-2xl"></i>
                    </div>
                </div>
                <div class="mr-5">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        مرحباً بك في نظام شركة طيبة
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        نظام إدارة متكامل للتسويق والخدمات
                    </p>
                </div>
            </div>
        </div>
    </div>
<!-- Stats Grid - بشكل مشابه للكروت الأولى -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    
    <!-- إجمالي المديرين -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90">إجمالي المديرين</p>
                <p class="text-2xl font-bold">{{ \App\Models\Admin::count() }}</p>
            </div>
            <i class="fas fa-users text-white text-2xl opacity-80"></i>
        </div>
    </div>

    <!-- حالة النظام -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90">حالة النظام</p>
                <p class="text-2xl font-bold">نشط</p>
            </div>
            <i class="fas fa-check-circle text-white text-2xl opacity-80"></i>
        </div>
    </div>

    <!-- آخر دخول -->
    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-4 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90">آخر دخول</p>
                <p class="text-2xl font-bold">الآن</p>
            </div>
            <i class="fas fa-clock text-white text-2xl opacity-80"></i>
        </div>
    </div>

</div>

<div class="my-60">
    <h2 class="text-xl font-bold mb-4">إحصائيات الأنظمة</h2>
    <!-- 3 كاردات في الصف -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @php
            $category_names = [
                'financial'     => 'المالي',
                'design'        => 'التصميم',
                'customers'     => 'العملاء',
                'communication' => 'التواصل',
                'tasks'         => 'المهام',
                'scheduling'    => 'المواعيد',
                'production'    => 'الإنتاج',
                'operations'    => 'العمليات',
            ];
            $icons = [
                'financial'     => '<svg class="h-5 w-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2L2 7v6a2 2 0 002 2h12a2 2 0 002-2V7l-8-5z"/></svg>',
                'design'        => '<svg class="h-5 w-5 text-pink-500" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="7"/></svg>',
                'customers'     => '<svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><rect x="5" y="5" width="10" height="10" rx="2"/></svg>',
                'communication' => '<svg class="h-5 w-5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2 8a6 6 0 1112 0v2a2 2 0 01-2 2H4a2 2 0 01-2-2V8z"/></svg>',
                'tasks'         => '<svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9 12h6m-6-4h6M5 8h.01M5 12h.01"/></svg>',
                'scheduling'    => '<svg class="h-5 w-5 text-orange-500" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="8"/></svg>',
                'production'    => '<svg class="h-5 w-5 text-teal-500" fill="currentColor" viewBox="0 0 20 20"><rect x="4" y="4" width="12" height="12" rx="3"/></svg>',
                'operations'    => '<svg class="h-5 w-5 text-purple-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 3a7 7 0 100 14 7 7 0 000-14z"/></svg>',
            ];
            // مصفوفة جريدينت Tailwind (كل عنصر يمثل كلاس جريدينت سفلي مختلف)
            $gradient_borders = [
                'from-green-400 to-blue-500',
                'from-pink-400 to-yellow-400',
                'from-indigo-400 to-purple-500',
                'from-yellow-400 to-red-400',
                'from-teal-400 to-emerald-500',
                'from-purple-400 to-pink-400',
                'from-blue-400 to-indigo-500',
                'from-red-400 to-orange-500',
            ];
            $colors = ['text-red-400', 'text-yellow-400', 'text-pink-400', 'text-green-400', 'text-indigo-400', 'text-blue-400', 'text-purple-400'];
        @endphp

        @foreach($systems_data as $idx => $system)
            @php
                $name = $category_names[$system->system_category] ?? $system->system_category;
                $icon_svg = $icons[$system->system_category] ?? '';
                $modules = $systems_details[$system->system_category] ?? [];
                // اختر الجريدينت بالترتيب حسب رقم الكارد
                $gradient = $gradient_borders[$idx % count($gradient_borders)];
            @endphp
            <div class="relative bg-white rounded-xl shadow p-3 flex flex-col items-center hover:shadow-lg transition">
                <!-- بوردر جريدينت سفلي -->
                <div class="absolute left-4 right-4 bottom-0 h-2 rounded-b-xl bg-gradient-to-r {{ $gradient }}"></div>
                <div class="flex items-center gap-2 mb-2">
                    {!! $icon_svg !!}
                    <span class="text-base font-semibold text-gray-800">{{ $name }}</span>
                </div>
                <div class="relative flex justify-center items-center w-16 h-16 mb-2">
                    <!-- دائرة النسبة -->
                    <svg width="64" height="64" viewBox="0 0 64 64" class="absolute">
                        <circle cx="32" cy="32" r="25" fill="#f3f4f6"/>
                        <circle cx="32" cy="32" r="25"
                            stroke="#22c55e" stroke-width="6"
                            stroke-dasharray="{{ 2 * 3.14 * 25 }}"
                            stroke-dashoffset="{{ 2 * 3.14 * 25 * (1 - ($system->percentage / 100)) }}"
                            fill="none"
                            style="transition:stroke-dashoffset 0.6s;"/>
                    </svg>
                    <span class="text-sm font-bold text-green-600 z-10">{{ $system->percentage }}%</span>
                </div>
                <div class="w-full px-2">
                    <div class="grid grid-cols-2 gap-x-2 gap-y-1">
                        @foreach($modules as $index => $mod)
                            @php
                                $color_class = $colors[$index % count($colors)];
                            @endphp
                            <div class="flex items-center gap-1 text-xs text-gray-600 my-0.5">
                                <svg class="h-3 w-3 flex-shrink-0 {{ $color_class }}" fill="currentColor" viewBox="0 0 20 20">
                                    <circle cx="10" cy="10" r="8" />
                                </svg>
                                <span>{{ $mod }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>





<!-- الفلاتر -->
 <div class="  mt-24">

<form method="get" class="filter-row flex gap-2 items-center justify-start  ">
    <select name="month" class="select-filter">
        @foreach($months as $m)
            <option value="{{ $m['num'] }}" @if($month == $m['num']) selected @endif>{{ $m['name'] }}</option>
        @endforeach
    </select>
    <select name="year" class="select-filter">
        @foreach($years as $y)
            <option value="{{ $y }}" @if($year == $y) selected @endif>{{ $y }}</option>
        @endforeach
    </select>
    <button class="bg-gradient-to-r from-[#ed4026] to-[#f32e2e] text-white px-4 py-2 px-6 rounded-xl rounded text-xs font-bold">عرض البيانات</button>
</form>
        </div>


<!-- الرسم البيانيات -->
<div class="curves-grid">
    <!-- الكيرف -->
    <div>
        <h4 class="font-bold text-lg mb-2 text-[#d71f32]">مخطط المقبوضات والمدفوعات 6 أشهر</h4>
        <canvas id="receiptsCurve" height="125"></canvas>
    </div>
    <!-- الدائرة النسبية -->
    <div>
        <h4 class="font-bold text-lg mb-2 text-[#d71f32]">توزيعات المشاريع لهذا الشهر</h4>
        <canvas id="projectsPie" height="160"></canvas>
        <div class="stat-small mt-2 text-center">
            <span style="color:#22c55e;">جارية: {{ $pieData['جارية'] }}</span> |
            <span style="color:#fbbf24;">مكتملة: {{ $pieData['مكتملة'] }}</span> |
            <span style="color:#6d28d9;">متوقفة: {{ $pieData['متوقفة'] }}</span> |
            <span style="color:#f43f5e;">ملغاة: {{ $pieData['ملغاة'] }}</span>
        </div>
    </div>
</div>

<!-- الفلاتر -->
<form method="get" class="mb-8 flex gap-4 items-center justify-start">
    <select name="month" class="border px-2 py-1 rounded text-xs">
        @foreach($months as $m)
            <option value="{{ $m['num'] }}" @if($month == $m['num']) selected @endif>{{ $m['name'] }}</option>
        @endforeach
    </select>
    <select name="year" class="border px-2 py-1 rounded text-xs">
        @foreach($years as $y)
            <option value="{{ $y }}" @if($year == $y) selected @endif>{{ $y }}</option>
        @endforeach
    </select>
    <button class="bg-gradient-to-r from-[#ed4026] to-[#f32e2e] text-white px-4 py-2 px-6 rounded-xl rounded text-xs font-bold">عرض البيانات</button>
</form>
<div class="grid grid-cols-4 gap-8">


    <!-- المشاريع الفعالة -->
    <div class="stat-card " style="border-top-color: #22c55e;">
        <div class="stat-icon" style="background:linear-gradient(135deg,#22c55e,#16a34a)">
            <i class="fas fa-project-diagram"></i>
        </div>
        <div class="stat-mini">{{ $projects_count ?? 0 }}</div>
        <label>المشاريع الفعالة</label>
    </div>

    <!-- الموظفين الفعالين -->
    <div class="stat-card" style="border-top-color: #f59e42;">
        <div class="stat-icon" style="background:linear-gradient(135deg,#f59e42,#fb923c)">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-mini">{{ $employees_count ?? 0 }}</div>
        <label>عدد الموظفين</label>
    </div>

    <!-- المقبوضات -->
    <div class="stat-card" style="border-top-color: #d71f32;">
        <div class="stat-icon" style="background:linear-gradient(135deg,#d71f32,#be123c)">
            <i class="fas fa-arrow-up"></i>
        </div>
        <div class="stat-mini">{{ number_format($receipts_month,2) }}</div>
        <label>المقبوضات</label>
    </div>

    <!-- المدفوعات -->
    <div class="stat-card" style="border-top-color: #be123c;">
        <div class="stat-icon" style="background:linear-gradient(135deg,#be123c,#d97706)">
            <i class="fas fa-arrow-down"></i>
        </div>
        <div class="stat-mini">{{ number_format($payments_month,2) }}</div>
        <label>المدفوعات</label>
    </div>

    <!-- العملاء المحتملين -->
    <div class="stat-card" style="border-top-color: #a21caf;">
        <div class="stat-icon" style="background:linear-gradient(135deg,#a21caf,#f472b6)">
            <i class="fas fa-user-tie"></i>
        </div>
        <div class="stat-mini">{{ $total_potentials }}</div>
        <label>العملاء المحتملين</label>
        <div class="flex gap-3 mt-1">
            <span class="stat-mini">
                <i class="fas fa-phone"></i> {{ $requested_call_count ?? 0 }}
            </span>
            <span class="stat-mini">
                <i class="fas fa-map-marker-alt"></i> {{ $requested_visit_count ?? 0 }}
            </span>
        </div>
    </div>

    <!-- أعلى مقبوضات موظف -->
    <div class="stat-card" style="border-top-color: #f59e42;">
        <div class="stat-icon" style="background:linear-gradient(135deg,#eab308,#f59e42)">
            <i class="fas fa-star"></i>
        </div>
        <div class="stat-mini" style="font-size:1.02rem;">{{ $top_employee_name ?? '-' }}</div>
        <div class="stat-small tiny-nums">{{ number_format($top_employee_amount,2) }} ريال</div>
        <label>أعلى مقبوضات</label>
    </div>


    <!-- أكثر تأخير -->
    <div class="stat-card" style="border-top-color: #ef4444;">
        <div class="stat-icon" style="background:linear-gradient(135deg,#ef4444,#f59e42)">
            <i class="fas fa-user-clock"></i>
        </div>
        <div class="stat-mini" style="font-size:1.04rem;">{{ $most_late_name }}</div>
        <label>أكثر موظف تأخير</label>
    </div>

    <!-- أكثر تنفيذ مهمات -->
    <div class="stat-card" style="border-top-color: #7c3aed;">
        <div class="stat-icon" style="background:linear-gradient(135deg,#7c3aed,#f472b6)">
            <i class="fas fa-tasks"></i>
        </div>
        <div class="stat-mini" style="font-size:1.04rem;">{{ $most_project_tasks_name }}</div>
        <div class="stat-small tiny-nums">{{ $most_project_tasks_count }} مهمة</div>
        <label>أكثر تنفيذ مهمات مشاريع</label>
    </div>
</div>

<!-- المتوسط كشريط هينت -->
<div class="w-full mt-2">
    <div class="stat-small" style="background:#f3f4f6; border-radius:.6rem; padding:.5rem 1rem; text-align:center; margin:auto; color:#d71f32; max-width:340px;">
        متوسط ساعات العمل الشهرية للموظفين: <b style="color:#16a34a">{{ number_format($avg_work_hours,2) }}</b>
        <br>
        <span style="font-size:.86rem; color:#888;">يتم الحساب من مجموع الإدخالات اليومية للموظفين.</span>
    </div>
</div>

<!-- مكتبة شارتات للرسم -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function(){
    // Curve Line Chart مقبوضات/مدفوعات
    var ctxCurve = document.getElementById('receiptsCurve').getContext('2d');
    new Chart(ctxCurve, {
        type: 'line',
        data: {
            labels: @json($curveData['months']),
            datasets: [{
                label: 'المقبوضات',
                data: @json($curveData['receipts']),
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34,197,94,0.13)',
                tension:0.35,
                fill:true, pointStyle:'rectRounded', pointRadius:4
            },
            {
                label: 'المدفوعات',
                data: @json($curveData['payments']),
                borderColor: '#d71f32',
                backgroundColor: 'rgba(215,31,50,0.13)',
                tension:0.35,
                fill:true, pointStyle:'rectRounded', pointRadius:4
            }]
        },
        options: {
            plugins: {
                legend: {display:true,labels:{font:{size:13}, color:'#d71f32'}}
            },
            scales: {
                x: {grid:{display:false}},
                y: {beginAtZero:true},
            }
        }
    });

    // Pie Chart تقسيم المشاريع
    var ctxPie = document.getElementById('projectsPie').getContext('2d');
    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: Object.keys(@json($pieData)),
            datasets: [{
                data: Object.values(@json($pieData)),
                backgroundColor: [
                    "#22c55e",
                    "#fbbf24",
                    "#6d28d9",
                    "#f43f5e"
                ],
                borderWidth:2
            }]
        },
        options: {
            plugins: {
                legend: {display:true,labels:{font:{size:13}, color:'#374151'}}
            },
            cutout:"68%",
        }
    });
});
</script>


    <!-- Quick Actions -->
    <!-- <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">الإجراءات السريعة</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('admin.admins.index') }}" 
                   class="relative block p-6 border-2 border-dashed border-gray-300 rounded-lg hover:border-red-400 hover:bg-gray-50 transition-colors">
                    <div class="text-center">
                        <i class="fas fa-users-cog text-4xl text-gray-400 mb-3"></i>
                        <span class="block text-sm font-medium text-gray-900">إدارة المديرين</span>
                    </div>
                </a>

                <div class="relative block p-6 border-2 border-dashed border-gray-300 rounded-lg">
                    <div class="text-center">
                        <i class="fas fa-project-diagram text-4xl text-gray-400 mb-3"></i>
                        <span class="block text-sm font-medium text-gray-900">المشاريع</span>
                        <span class="block text-xs text-gray-500">قريباً</span>
                    </div>
                </div>

                <div class="relative block p-6 border-2 border-dashed border-gray-300 rounded-lg">
                    <div class="text-center">
                        <i class="fas fa-chart-bar text-4xl text-gray-400 mb-3"></i>
                        <span class="block text-sm font-medium text-gray-900">التقارير</span>
                        <span class="block text-xs text-gray-500">قريباً</span>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
</div>
@endsection