@extends('employee.layouts.app')

@section('title', 'متابعة المشاريع والمهام')
@section('page-title', 'متابعة المشاريع والمهام')
@section('page-subtitle', 'نظام متابعة المشاريع مع الاستوب ووتش والبصمة')

@push('styles')
    <style>
        /* Custom animations */
        .pulse-border {
            animation: pulse-border 2s infinite;
        }

        @keyframes pulse-border {

            0%,
            100% {
                border-color: rgba(234, 179, 8, 0.5);
                box-shadow: 0 0 0 0 rgba(234, 179, 8, 0.4);
            }

            50% {
                border-color: rgba(234, 179, 8, 1);
                box-shadow: 0 0 0 10px rgba(234, 179, 8, 0);
            }
        }

        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0% {
                transform: translate(0, 0px);
            }

            50% {
                transform: translate(0, -10px);
            }

            100% {
                transform: translate(0, 0px);
            }
        }

        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
@endpush

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-slate-100 via-blue-50 to-indigo-100" x-data="employeeProjectTracking()"
        x-init="init()">

        {{-- Helper Functions --}}
        @php
            if (!function_exists('formatHoursToHoursMinutes')) {
                function formatHoursToHoursMinutes($hoursFloat)
                {
                    $totalMinutes = round($hoursFloat * 60);
                    $hours = floor($totalMinutes / 60);
                    $minutes = $totalMinutes % 60;
                    return sprintf('%02d:%02d', $hours, $minutes);
                }
            }

            if (!function_exists('formatLateTime')) {
                function formatLateTime($minutes)
                {
                    $minutes = abs($minutes);
                    $hours = floor($minutes / 60);
                    $mins = $minutes % 60;
                    return sprintf('%02d:%02d', $hours, $mins);
                }
            }

            if (!function_exists('formatTime12h')) {
                function formatTime12h($carbonTime)
                {
                    if (is_string($carbonTime)) {
                        $carbonTime = \Carbon\Carbon::parse($carbonTime);
                    }
                    $hour = $carbonTime->hour;
                    $minute = $carbonTime->minute;
                    $suffix = $hour >= 12 ? 'م' : 'ص';
                    $hour12 = $hour % 12;
                    if ($hour12 == 0) {
                        $hour12 = 12;
                    }
                    return sprintf('%02d:%02d %s', $hour12, $minute, $suffix);
                }
            }
        @endphp

        {{-- Header Section --}}
        <div class="mb-8 p-6 glass-effect rounded-2xl shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold gradient-text mb-2">مركز التحكم الشخصي</h1>
                    <p class="text-gray-600">إدارة المشاريع والمهام مع نظام التتبع المتقدم</p>
                </div>
                <div class="text-right">
                    <p class="text-lg font-semibold text-gray-800" x-text="currentTime"></p>
                    <p class="text-sm text-gray-600">{{ now()->format('Y-m-d') }}</p>
                </div>
            </div>
        </div>

        {{-- Main Dashboard Cards --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            {{-- Attendance Card --}}
            @include('employee.project-tracking.components.attendance-card')

            {{-- Timer Card --}}
            @include('employee.project-tracking.components.timer-card')
        </div>

        {{-- Stats Cards --}}
        <div class="mb-8">
            @include('employee.project-tracking.components.stats-cards')
        </div>

        {{-- Quick Actions --}}
        <div class="mb-8 p-6 glass-effect rounded-2xl shadow-xl">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-bolt text-yellow-500 ml-2"></i>
                الإجراءات السريعة
            </h3>
            <div class="flex flex-wrap gap-3">
                <button @click="showCreateProjectModal()"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-semibold hover:from-blue-600 hover:to-blue-700 transform hover:scale-105 transition-all duration-300 shadow-lg">
                    <i class="fas fa-plus-circle ml-2 text-lg"></i>
                    مشروع جديد
                </button>

                <button @click="showAddTaskModal()"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl font-semibold hover:from-green-600 hover:to-green-700 transform hover:scale-105 transition-all duration-300 shadow-lg">
                    <i class="fas fa-tasks ml-2 text-lg"></i>
                    مهمة جديدة
                </button>

                @if (!$activeTimer)
                    <button @click="showStartModal = true"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 text-white rounded-xl font-semibold hover:from-yellow-600 hover:to-orange-600 transform hover:scale-105 transition-all duration-300 shadow-lg">
                        <i class="fas fa-play-circle ml-2 text-lg"></i>
                        بدء عداد جديد
                    </button>
                @endif

                <button @click="loadTodayEntries()"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl font-semibold hover:from-purple-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 shadow-lg">
                    <i class="fas fa-sync-alt ml-2 text-lg"></i>
                    تحديث البيانات
                </button>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            {{-- Projects Section --}}
            <div class="xl:col-span-2 space-y-6">
                {{-- Active Projects --}}
                @include('employee.project-tracking.components.projects-list')
            </div>

            {{-- Today's Work Log --}}
            <div class="xl:col-span-1">
                @include('employee.project-tracking.components.today-entries')
            </div>
        </div>

        {{-- Modals --}}
        @include('employee.project-tracking.components.modals')
        @include('employee.project-tracking.components.project-task-modals')
    </div>

    @push('scripts')
        <script>
            function employeeProjectTracking() {
                return {
                    // Timer state
                    activeTimerDisplay: '00:00:00',
                    @if ($activeTimer)
                        myActiveTimer: @json($activeTimer),
                        myTimerStartTime: new Date('{{ $activeTimer->start_time->toISOString() }}'),
                    @else
                        myActiveTimer: null,
                        myTimerStartTime: null,
                    @endif
                    timerInterval: null,
                    currentTime: '',

                    // Modal states
                    showStartModal: false,
                    showEditModal: false,
                    showDetailsModal: false,
                    showCreateModal: false,
                    showTaskModal: false,

                    // Form data
                    selectedProject: '',
                    selectedTask: '',
                    timerDescription: '',
                    projectTasks: [],
                    todayEntries: [],

                    // Edit modal data
                    currentEditTimer: null,
                    editHours: 0,
                    editMinutes: 0,

                    // Details modal data
                    currentTimerDetails: null,

                    // Project/Task creation
                    newProject: {
                        name: '',
                        client_name: '',
                        description: '',
                        start_date: '',
                        end_date: ''
                    },
                    newTask: {
                        project_id: '',
                        name: '',
                        description: '',
                        estimated_hours: 1
                    },

                    // Stats
                    total_hours: 0,
                    target_percentage: 0,

                    init() {
                        this.updateCurrentTime();
                        this.loadTodayEntries();

                        // Update current time every minute
                        setInterval(() => {
                            this.updateCurrentTime();
                        }, 60000);

                        // Update timer display
                        if (this.myActiveTimer && this.myTimerStartTime) {
                            this.updateMyTimerDisplay();
                            this.timerInterval = setInterval(() => {
                                this.updateMyTimerDisplay();
                            }, 1000);
                        }
                    },

                    updateCurrentTime() {
                        const now = new Date();
                        this.currentTime = now.toLocaleTimeString('ar-SA', {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        });
                    },

                    updateMyTimerDisplay() {
                        if (this.myActiveTimer && this.myTimerStartTime) {
                            if (this.myActiveTimer.is_paused) {
                                // إذا متوقف، اعرض الوقت المحفوظ فقط
                                this.activeTimerDisplay = this.formatSeconds(this.myActiveTimer.total_seconds || 0);
                            } else {
                                // *** الحل المهم: احسب من start_time فقط، لا تجمع مع total_seconds ***
                                const now = new Date();
                                const diffInSeconds = Math.floor((now - this.myTimerStartTime) / 1000);

                                // تأكد من أن الرقم موجب
                                const displaySeconds = Math.max(0, diffInSeconds);
                                this.activeTimerDisplay = this.formatSeconds(displaySeconds);
                            }
                        }
                    },

                    async loadTodayEntries() {
                        try {
                            const response = await fetch('{{ route('employee.project-tracking.today-entries') }}');
                            if (!response.ok) throw new Error('Network response was not ok');
                            const data = await response.json();
                            this.todayEntries = data.entries || [];
                            this.total_hours = data.total_hours || 0;
                            this.target_percentage = data.target_percentage || 0;
                        } catch (error) {
                            console.error('Error loading today entries:', error);
                            this.todayEntries = [];
                        }
                    },

                    loadProjectTasks() {
                        const project = @json($activeProjects).find(p => p.id == this.selectedProject);
                        if (project && project.tasks) {
                            this.projectTasks = project.tasks;
                        } else {
                            this.projectTasks = [];
                        }
                        this.selectedTask = '';
                    },

                    // Timer functions
                    async startTimer() {
                        if (!this.selectedProject || !this.selectedTask) {
                            this.showAlert('يرجى اختيار المشروع والمهمة', 'error');
                            return;
                        }

                        try {
                            const response = await fetch('{{ route('employee.project-tracking.start-timer') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify({
                                    project_id: this.selectedProject,
                                    task_id: this.selectedTask,
                                    description: this.timerDescription
                                })
                            });

                            const data = await response.json();

                            if (data.success) {
                                this.showStartModal = false;
                                this.showAlert('تم بدء العداد بنجاح', 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                this.showAlert(data.message, 'error');
                            }
                        } catch (error) {
                            console.error('Start timer error:', error);
                            this.showAlert('حدث خطأ أثناء بدء العداد', 'error');
                        }
                    },

                    async pauseTimer() {
                        try {
                            const response = await fetch('{{ route('employee.project-tracking.pause-timer') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                }
                            });

                            const data = await response.json();

                            if (data.success) {
                                if (this.timerInterval) {
                                    clearInterval(this.timerInterval);
                                    this.timerInterval = null;
                                }

                                await this.showAlert('تم إيقاف العداد مؤقتاً', 'success');
                                location.reload();
                            } else {
                                this.showAlert(data.message, 'error');
                            }
                        } catch (error) {
                            console.error('Pause timer error:', error);
                            this.showAlert('حدث خطأ أثناء إيقاف العداد', 'error');
                        }
                    },

                    async resumeTimer() {
                        try {
                            const response = await fetch('{{ route('employee.project-tracking.resume-timer') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                }
                            });

                            const data = await response.json();

                            if (data.success) {
                                // *** الحل: تحديث بيانات الـ timer بالكامل من السيرفر ***

                                // إيقاف العداد القديم
                                if (this.timerInterval) {
                                    clearInterval(this.timerInterval);
                                    this.timerInterval = null;
                                }

                                // تحديث بيانات Timer من السيرفر
                                this.myActiveTimer = data.timer;

                                // *** هنا المهم: استخدم start_time الجديد من السيرفر ***
                                this.myTimerStartTime = new Date(data.timer.start_time);

                                // بدء العداد من جديد
                                this.updateMyTimerDisplay();
                                this.timerInterval = setInterval(() => {
                                    this.updateMyTimerDisplay();
                                }, 1000);

                                await this.showAlert('تم استئناف العداد بنجاح', 'success');
                                // لا حاجة لـ location.reload() هنا

                            } else {
                                this.showAlert(data.message, 'error');
                            }
                        } catch (error) {
                            console.error('Resume timer error:', error);
                            this.showAlert('حدث خطأ أثناء استئناف العداد', 'error');
                        }
                    },
                    async stopTimer() {
                        if (this.timerInterval) {
                            clearInterval(this.timerInterval);
                            this.timerInterval = null;
                        }

                        try {
                            const response = await fetch('{{ route('employee.project-tracking.stop-timer') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                }
                            });

                            const data = await response.json();

                            if (data.success) {
                                this.showAlert(`تم إنهاء العداد - المدة: ${data.duration}`, 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                this.showAlert(data.message, 'error');
                            }
                        } catch (error) {
                            console.error('Stop timer error:', error);
                            this.showAlert('حدث خطأ أثناء إيقاف العداد', 'error');
                        }
                    },

               async restartTimer(timerId) {
                        try {
                            const response = await fetch('{{ route('employee.project-tracking.restart-timer') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify({
                                    timer_id: timerId,
                                    description: 'استكمال العمل'
                                })
                            });

                            const data = await response.json();

                            if (data.success) {
                                // رسالة تظهر أنه كمل نفس الجلسة
                                const message = `تم استكمال الجلسة #${data.session_number} من ${data.continued_from}`;
                                await this.showAlert(message, 'success');
                                location.reload(); // ريفريش مباشر
                            } else {
                                this.showAlert(data.message, 'error');
                            }
                        } catch (error) {
                            console.error('Restart timer error:', error);
                            this.showAlert('حدث خطأ أثناء استكمال العداد', 'error');
                        }
                    },

                    // Edit timer functions
                    showEditModal(timerId) {
                        const entry = this.todayEntries.find(e => e.id === timerId);
                        if (entry) {
                            this.currentEditTimer = entry;
                            this.editHours = Math.floor(entry.hours);
                            this.editMinutes = Math.round((entry.hours % 1) * 60);
                            this.showEditModal = true;
                        }
                    },

                    async editTimer() {
                        if (!this.currentEditTimer) return;

                        try {
                            const response = await fetch('{{ route('employee.project-tracking.edit-timer') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify({
                                    timer_id: this.currentEditTimer.id,
                                    hours: this.editHours,
                                    minutes: this.editMinutes
                                })
                            });

                            const data = await response.json();

                            if (data.success) {
                                this.showEditModal = false;
                                this.showAlert('تم تعديل الوقت بنجاح', 'success');
                                this.loadTodayEntries();
                            } else {
                                this.showAlert(data.message, 'error');
                            }
                        } catch (error) {
                            console.error('Edit timer error:', error);
                            this.showAlert('حدث خطأ أثناء تعديل الوقت', 'error');
                        }
                    },

                    // Timer details functions
                    async showTimerDetails(timerId) {
                        try {
                            const response = await fetch('{{ route('employee.project-tracking.timer-details') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify({
                                    timer_id: timerId
                                })
                            });

                            const data = await response.json();

                            if (data.success) {
                                this.currentTimerDetails = data;
                                this.showDetailsModal = true;
                            } else {
                                this.showAlert(data.message, 'error');
                            }
                        } catch (error) {
                            console.error('Get timer details error:', error);
                            this.showAlert('حدث خطأ أثناء تحميل التفاصيل', 'error');
                        }
                    },

                    // Attendance functions
                    async checkIn() {
                        try {
                            const response = await fetch('{{ route('employee.project-tracking.check-in') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                }
                            });

                            const data = await response.json();

                            if (data.success) {
                                this.showAlert('تم تسجيل الحضور بنجاح', 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                this.showAlert(data.message, 'error');
                            }
                        } catch (error) {
                            console.error('Check-in error:', error);
                            this.showAlert('حدث خطأ أثناء تسجيل الحضور', 'error');
                        }
                    },

                    async checkOut(type = 'final') {
                        if (type === 'final') {
                            const confirmed = await Swal.fire({
                                title: 'تأكيد الانصراف النهائي',
                                text: 'هل أنت متأكد من الانصراف النهائي؟ لن تتمكن من العودة اليوم',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#ef4444',
                                cancelButtonColor: '#6b7280',
                                confirmButtonText: 'نعم، انصراف نهائي',
                                cancelButtonText: 'إلغاء',
                                reverseButtons: true
                            });

                            if (!confirmed.isConfirmed) {
                                return;
                            }
                        }

                        try {
                            const response = await fetch('{{ route('employee.project-tracking.check-out') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify({
                                    type: type
                                })
                            });

                            const data = await response.json();

                            if (data.success) {
                                this.showAlert(data.message, 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                this.showAlert(data.message, 'error');
                            }
                        } catch (error) {
                            console.error('Check-out error:', error);
                            this.showAlert('حدث خطأ أثناء تسجيل الانصراف', 'error');
                        }
                    },

                    async tempCheckOut() {
                        try {
                            const response = await fetch('{{ route('employee.project-tracking.temp-check-out') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                }
                            });

                            const data = await response.json();

                            if (data.success) {
                                this.showAlert('تم الانصراف المؤقت بنجاح', 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                this.showAlert(data.message, 'error');
                            }
                        } catch (error) {
                            console.error('Temp check-out error:', error);
                            this.showAlert('حدث خطأ أثناء الانصراف المؤقت', 'error');
                        }
                    },

                    async tempCheckIn() {
                        try {
                            const response = await fetch('{{ route('employee.project-tracking.temp-check-in') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                }
                            });

                            const data = await response.json();

                            if (data.success) {
                                this.showAlert('تم العودة من الانصراف المؤقت بنجاح', 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                this.showAlert(data.message, 'error');
                            }
                        } catch (error) {
                            console.error('Temp check-in error:', error);
                            this.showAlert('حدث خطأ أثناء العودة من الانصراف', 'error');
                        }
                    },

                    // Project/Task creation functions
                    showCreateProjectModal() {
                        this.showCreateModal = true;
                        const today = new Date();
                        this.newProject = {
                            name: '',
                            client_name: '',
                            description: '',
                            start_date: today.toISOString().split('T')[0],
                            end_date: ''
                        };
                    },

                    async createProject() {
                        if (!this.newProject.name || !this.newProject.start_date) {
                            this.showAlert('يرجى ملء الحقول المطلوبة', 'error');
                            return;
                        }

                        try {
                            const response = await fetch('{{ route('employee.project-tracking.create-project') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify(this.newProject)
                            });

                            const data = await response.json();

                            if (data.success) {
                                this.showCreateModal = false;
                                this.showAlert('تم إنشاء المشروع بنجاح', 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                this.showAlert('حدث خطأ أثناء إنشاء المشروع', 'error');
                            }
                        } catch (error) {
                            console.error('Create project error:', error);
                            this.showAlert('حدث خطأ أثناء إنشاء المشروع', 'error');
                        }
                    },

                    showAddTaskModal() {
                        this.showTaskModal = true;
                        this.newTask = {
                            project_id: '',
                            name: '',
                            description: '',
                            estimated_hours: 1
                        };
                    },

                    async addTask() {
                        if (!this.newTask.project_id || !this.newTask.name || !this.newTask.estimated_hours) {
                            this.showAlert('يرجى ملء الحقول المطلوبة', 'error');
                            return;
                        }

                        try {
                            const response = await fetch('{{ route('employee.project-tracking.add-task') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify(this.newTask)
                            });

                            const data = await response.json();

                            if (data.success) {
                                this.showTaskModal = false;
                                this.showAlert('تم إضافة المهمة بنجاح', 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                this.showAlert('حدث خطأ أثناء إضافة المهمة', 'error');
                            }
                        } catch (error) {
                            console.error('Add task error:', error);
                            this.showAlert('حدث خطأ أثناء إضافة المهمة', 'error');
                        }
                    },

                    startTaskTimer(projectId, taskId) {
                        this.selectedProject = projectId;
                        this.selectedTask = taskId;
                        this.loadProjectTasks();
                        this.timerDescription = '';
                        this.startTimer();
                    },

                    // Utility functions
                    formatSeconds(totalSeconds) {
                        if (totalSeconds < 0) totalSeconds = 0;

                        const hours = Math.floor(totalSeconds / 3600);
                        const minutes = Math.floor((totalSeconds % 3600) / 60);
                        const seconds = totalSeconds % 60;

                        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                    },

                    formatHours(hours) {
                        if (hours < 0) hours = 0;
                        const totalMinutes = Math.round(hours * 60);
                        const displayHours = Math.floor(totalMinutes / 60);
                        const minutes = totalMinutes % 60;
                        return `${displayHours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
                    },

                    showAlert(message, type) {
                        if (typeof Swal !== 'undefined') {
                            if (type === 'success') {
                                Swal.fire({
                                    title: 'نجح!',
                                    text: message,
                                    icon: 'success',
                                    timer: 2500,
                                    showConfirmButton: false,
                                    position: 'top-end',
                                    toast: true,
                                    background: '#f0fdf4',
                                    color: '#15803d'
                                });
                            } else {
                                Swal.fire({
                                    title: 'خطأ!',
                                    text: message,
                                    icon: 'error',
                                    confirmButtonText: 'حسناً',
                                    confirmButtonColor: '#ef4444',
                                    background: '#fef2f2',
                                    color: '#dc2626'
                                });
                            }
                        } else {
                            alert(message);
                        }
                    }
                }
            }
        </script>
    @endpush
@endsection
