{{-- resources/views/admin/project-tracking/components/timer-card.blade.php --}}
<div class="bg-gradient-to-r from-yellow-50 to-orange-100 p-6 rounded-xl shadow-lg border border-yellow-200 {{ $activeTimer ? 'ring-2 ring-yellow-400 ring-opacity-50 animate-pulse' : '' }} transform hover:scale-105 transition-all duration-300">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-3 rtl:space-x-reverse">
            <div class="p-3 {{ $activeTimer ? 'bg-yellow-500' : 'bg-gray-400' }} rounded-full">
                <i class="fas fa-stopwatch text-white text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-yellow-900">العداد الشخصي</h3>
                @if($activeTimer)
                    <p class="text-sm text-yellow-700">الجلسة #{{ $activeTimer->session_number }}</p>
                @endif
            </div>
        </div>
        <div class="h-12 w-12 {{ $activeTimer ? 'bg-yellow-500' : 'bg-gray-400' }} rounded-full flex items-center justify-center {{ $activeTimer ? 'animate-spin' : '' }}" style="{{ $activeTimer ? 'animation-duration: 3s;' : '' }}">
            <i class="fas fa-{{ $activeTimer ? ($activeTimer->is_paused ?? false) ? 'pause' : 'play' : 'stop' }} text-white"></i>
        </div>
    </div>

    @if ($activeTimer)
        <!-- Active Timer Display -->
        <div class="text-center mb-6">
            <div class="bg-white/70 rounded-lg p-4 backdrop-blur-sm border border-yellow-100 mb-4">
                <div class="text-4xl font-mono font-bold {{ ($activeTimer->is_paused ?? false) ? 'text-orange-600' : 'text-yellow-900' }} mb-2" 
                     x-text="activeTimerDisplay">00:00:00</div>
                
                <div class="space-y-1">
                    <p class="text-sm font-semibold text-yellow-800">{{ $activeTimer->project->name ?? 'مشروع محذوف' }}</p>
                    <p class="text-xs text-yellow-600">{{ $activeTimer->task->name ?? 'مهمة محذوفة' }}</p>
                </div>
                
                @if(($activeTimer->pause_count ?? 0) > 0)
                    <div class="mt-3 text-xs text-yellow-600 bg-yellow-50 py-1 px-3 rounded-full inline-block">
                        <i class="fas fa-pause ml-1"></i>
                        توقف {{ $activeTimer->pause_count }} مرة
                        @if(($activeTimer->resume_count ?? 0) > 0)
                            - استئناف {{ $activeTimer->resume_count }} مرة
                        @endif
                    </div>
                @endif
            </div>

            <!-- Timer Controls -->
            <div class="grid grid-cols-3 gap-2">
                @if(($activeTimer->is_paused ?? false))
                    <!-- Resume Button -->
                    <button @click="resumeTimer()" 
                        class="col-span-2 px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg font-semibold hover:from-green-600 hover:to-green-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                        <i class="fas fa-play ml-2"></i>
                        <span>استئناف</span>
                    </button>
                    <button @click="stopTimer()" 
                        class="px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg font-semibold hover:from-red-600 hover:to-red-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                        <i class="fas fa-stop"></i>
                    </button>
                @else
                    <!-- Pause and Stop Buttons -->
                    <button @click="pauseTimer()" 
                        class="px-4 py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg font-semibold hover:from-orange-600 hover:to-orange-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                        <i class="fas fa-pause"></i>
                    </button>
                    <button @click="stopTimer()" 
                        class="col-span-2 px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg font-semibold hover:from-red-600 hover:to-red-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                        <i class="fas fa-stop ml-2"></i>
                        <span>إنهاء</span>
                    </button>
                @endif
            </div>
        </div>
    @else
        <!-- Inactive Timer -->
        <div class="text-center space-y-4">
            <div class="p-6 bg-white/70 rounded-lg backdrop-blur-sm">
                <div class="text-6xl font-mono font-bold text-gray-400 mb-3">00:00:00</div>
                <i class="fas fa-clock text-gray-300 text-3xl mb-3"></i>
                <p class="text-gray-600 font-medium">العداد متوقف</p>
            </div>
            
            <button @click="showStartModal = true"
                class="w-full px-6 py-4 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-lg font-bold hover:from-yellow-600 hover:to-yellow-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                <i class="fas fa-play ml-3 text-lg"></i>
                <span class="text-lg">بدء العداد</span>
            </button>
        </div>
    @endif
</div>