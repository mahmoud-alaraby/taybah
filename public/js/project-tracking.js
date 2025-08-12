// ملف JavaScript منفصل للـ stopwatch والتفاعل
class ProjectTracker {
    constructor() {
        this.timerInterval = null;
        this.currentSeconds = 0;
        this.isActive = false;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.checkActiveTimer();
        this.startClockUpdate();
    }

    setupEventListeners() {
        // Event listeners للأزرار
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-action="start-timer"]')) {
                this.showStartTimerModal();
            }
            if (e.target.matches('[data-action="stop-timer"]')) {
                this.stopTimer();
            }
        });
    }

    startClockUpdate() {
        setInterval(() => {
            const now = new Date();
            const timeString = now.toLocaleTimeString('ar-SA');
            const timeElements = document.querySelectorAll('[data-current-time]');
            timeElements.forEach(el => el.textContent = timeString);
        }, 1000);
    }

    async checkActiveTimer() {
        try {
            const response = await fetch('/employee/project-tracking/active-timer');
            const data = await response.json();
            
            if (data.active) {
                this.isActive = true;
                this.currentSeconds = data.current_seconds;
                this.startTimerDisplay();
            }
        } catch (error) {
            console.error('Error checking active timer:', error);
        }
    }

    startTimerDisplay() {
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
        }

        this.timerInterval = setInterval(() => {
            if (this.isActive) {
                this.currentSeconds++;
                this.updateTimerDisplay();
            }
        }, 1000);
    }

    updateTimerDisplay() {
        const hours = Math.floor(this.currentSeconds / 3600);
        const minutes = Math.floor((this.currentSeconds % 3600) / 60);
        const seconds = this.currentSeconds % 60;
        
        const timeString = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        const timerElements = document.querySelectorAll('[data-timer-display]');
        timerElements.forEach(el => el.textContent = timeString);
    }

    async stopTimer() {
        try {
            const response = await fetch('/employee/project-tracking/stop-timer', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.isActive = false;
                clearInterval(this.timerInterval);
                this.currentSeconds = 0;
                this.updateTimerDisplay();
                this.showNotification('تم إيقاف العداد بنجاح', 'success');
            }
        } catch (error) {
            this.showNotification('حدث خطأ أثناء إيقاف العداد', 'error');
        }
    }

    showNotification(message, type) {
        // استخدام SweetAlert أو نظام إشعارات آخر
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: type === 'success' ? 'نجح!' : 'خطأ!',
                text: message,
                icon: type,
                timer: 3000,
                showConfirmButton: false
            });
        }
    }
}

// تشغيل النظام عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('[data-page="project-tracking"]')) {
        new ProjectTracker();
    }
});