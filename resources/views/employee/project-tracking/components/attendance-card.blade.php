{{-- resources/views/employee/project-tracking/components/attendance-card.blade.php --}}
<div class="bg-gradient-to-r from-blue-50 to-blue-100 p-6 rounded-xl shadow-lg border border-blue-200 transform hover:scale-105 transition-all duration-300">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-3 rtl:space-x-reverse">
            <div class="p-3 bg-blue-500 rounded-full">
                <i class="fas fa-user-check text-white text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-blue-900">حالة البصمة</h3>
                <p class="text-sm text-blue-600" x-text="currentTime"></p>
            </div>
        </div>
        <div class="h-12 w-12 bg-blue-500 rounded-full flex items-center justify-center animate-pulse">
            <i class="fas fa-clock text-white"></i>
        </div>
    </div>

    @if ($todayAttendance)
        <!-- Attendance Info -->
        <div class="mb-6 p-4 bg-white/70 rounded-lg backdrop-blur-sm border border-blue-100">
            <div class="space-y-2 text-sm">
                <div class="flex justify-between items-center">
                    <span class="text-blue-700 font-medium">دخول:</span>
                    <span class="font-bold text-blue-900">{{ formatTime12h($todayAttendance->check_in_time) }}</span>
                </div>

                @if ($todayAttendance->is_temp_out)
                    <div class="flex justify-between items-center text-orange-600">
                        <span class="font-medium">انصراف مؤقت:</span>
                        <span class="font-bold">{{ formatTime12h($todayAttendance->temp_checkout_time) }}</span>
                    </div>
                    <div class="text-center text-xs text-orange-500 bg-orange-50 py-1 px-2 rounded">
                        المدة: {{ $todayAttendance->temp_out_duration ?? 'جاري...' }}
                    </div>
                @elseif($todayAttendance->temp_checkout_time && $todayAttendance->temp_checkin_time)
                    <div class="text-xs text-gray-600 bg-gray-50 p-2 rounded">
                        <div class="flex justify-between">
                            <span>آخر انصراف مؤقت:</span>
                            <span class="font-semibold">
                                {{ formatTime12h($todayAttendance->temp_checkout_time) }} - {{ formatTime12h($todayAttendance->temp_checkin_time) }}
                            </span>
                        </div>
                        <div class="text-center mt-1">
                            المدة: {{ $todayAttendance->temp_out_duration }} ({{ $todayAttendance->temp_checkout_count }} مرات)
                        </div>
                    </div>
                @endif

                @if ($todayAttendance->check_out_time)
                    <div class="flex justify-between items-center border-t border-blue-100 pt-2">
                        <span class="text-blue-700 font-medium">خروج نهائي:</span>
                        <span class="font-bold text-blue-900">{{ formatTime12h($todayAttendance->check_out_time) }}</span>
                    </div>
                    <div class="flex justify-between items-center bg-green-50 p-2 rounded">
                        <span class="text-green-700 font-medium">الإجمالي:</span>
                        <span class="font-bold text-green-800">{{ formatHoursToHoursMinutes($todayAttendance->total_hours) }}</span>
                    </div>
                @endif

                @if ($todayAttendance->is_late)
                    <div class="flex justify-between items-center bg-red-50 p-2 rounded">
                        <span class="text-red-600 font-medium">تأخير:</span>
                        <span class="font-bold text-red-700">{{ formatLateTime($todayAttendance->late_minutes) }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-3">
            @if ($todayAttendance->check_out_time && $todayAttendance->checkout_type === 'final')
                <div class="flex items-center justify-center p-4 bg-gray-100 rounded-lg">
                    <div class="text-center">
                        <i class="fas fa-check-circle text-green-600 text-2xl mb-2"></i>
                        <span class="text-gray-800 font-semibold block">تم الانصراف النهائي</span>
                        <span class="text-xs text-gray-600">{{ formatTime12h($todayAttendance->check_out_time) }}</span>
                    </div>
                </div>
            @elseif($todayAttendance->is_temp_out)
                <div class="text-center mb-3">
                    <div class="inline-flex items-center px-3 py-2 bg-orange-100 rounded-full">
                        <i class="fas fa-pause text-orange-600 ml-2"></i>
                        <span class="text-orange-800 text-sm font-medium">في انصراف مؤقت</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <button @click="tempCheckIn()"
                        class="flex-1 px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg font-semibold hover:from-green-600 hover:to-green-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                        <i class="fas fa-play ml-2"></i>
                        <span>عودة</span>
                    </button>
                    <button @click="checkOut('final')"
                        class="flex-1 px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg font-semibold hover:from-red-600 hover:to-red-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                        <i class="fas fa-stop ml-2"></i>
                        <span>انصراف نهائي</span>
                    </button>
                </div>
            @else
                <div class="grid grid-cols-2 gap-3">
                    <button @click="tempCheckOut()"
                        class="flex-1 px-4 py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-lg font-semibold hover:from-yellow-600 hover:to-yellow-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                        <i class="fas fa-pause ml-2"></i>
                        <span>انصراف مؤقت</span>
                    </button>
                    <button @click="checkOut('final')"
                        class="flex-1 px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg font-semibold hover:from-red-600 hover:to-red-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                        <i class="fas fa-sign-out-alt ml-2"></i>
                        <span>انصراف نهائي</span>
                    </button>
                </div>
            @endif
        </div>
    @else
        <!-- Check In Button -->
        <div class="text-center space-y-4">
            <div class="p-6 bg-white/70 rounded-lg backdrop-blur-sm">
                <i class="fas fa-calendar-times text-gray-400 text-4xl mb-3"></i>
                <p class="text-gray-600 font-medium">لم يتم تسجيل الحضور اليوم</p>
            </div>
            <button @click="checkIn()"
                class="w-full px-6 py-4 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg font-bold hover:from-green-600 hover:to-green-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                <i class="fas fa-sign-in-alt ml-3 text-lg"></i>
                <span class="text-lg">تسجيل حضور</span>
            </button>
        </div>
    @endif
</div>