// voice-handler.js - ملف JavaScript منفصل للتعامل مع الصوت
class VoiceHandler {
    constructor() {
        this.activeAudios = new Map();
        this.mediaRecorder = null;
        this.audioChunks = [];
        this.isRecording = false;
        this.recordingTimer = null;
        this.recordingStartTime = null;
        
        // إعدادات التسجيل
        this.recordingConstraints = {
            audio: {
                echoCancellation: true,
                noiseSuppression: true,
                autoGainControl: true,
                sampleRate: 44100
            }
        };
        
        this.init();
    }
    
    init() {
        // إيقاف جميع الملفات الصوتية عند تغيير علامة التبويب
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.pauseAllActiveAudio();
            }
        });
        
        // إيقاف التسجيل بمفتاح Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isRecording) {
                this.stopRecording();
            }
        });
        
        // إيقاف جميع العمليات عند مغادرة الصفحة
        window.addEventListener('beforeunload', () => {
            this.cleanup();
        });
    }
    
    // تشغيل/إيقاف الصوت
    toggleVoicePlay(button, audioUrl, messageId) {
        const audio = button.closest('.voice-player').querySelector('.voice-audio');
        const isPlaying = button.getAttribute('data-playing') === 'true';
        
        if (isPlaying) {
            this.pauseVoice(button, audio);
        } else {
            this.playVoice(button, audio, audioUrl);
        }
    }
    
    // تشغيل الصوت
    async playVoice(button, audio, audioUrl) {
        try {
            // إيقاف جميع الملفات الصوتية الأخرى
            this.pauseAllActiveAudio();
            
            // إعداد الملف الصوتي
            if (!audio.src || audio.src !== audioUrl) {
                audio.src = audioUrl;
                
                // انتظار تحميل البيانات الأساسية
                await new Promise((resolve, reject) => {
                    const onLoadedData = () => {
                        audio.removeEventListener('loadeddata', onLoadedData);
                        audio.removeEventListener('error', onError);
                        resolve();
                    };
                    
                    const onError = (e) => {
                        audio.removeEventListener('loadeddata', onLoadedData);
                        audio.removeEventListener('error', onError);
                        reject(new Error('فشل في تحميل الملف الصوتي'));
                    };
                    
                    audio.addEventListener('loadeddata', onLoadedData);
                    audio.addEventListener('error', onError);
                    
                    audio.load();
                });
            }
            
            // تشغيل الصوت
            await audio.play();
            
            // تحديث واجهة المستخدم
            this.updatePlayButton(button, true);
            const player = button.closest('.voice-player');
            player.classList.add('voice-playing');
            player.classList.remove('voice-paused');
            
            // إضافة إلى قائمة الملفات النشطة
            this.activeAudios.set(audio, button);
            
        } catch (error) {
            console.error('Error playing audio:', error);
            this.showError('خطأ في تشغيل الملف الصوتي');
        }
    }
    
    // إيقاف الصوت
    pauseVoice(button, audio) {
        audio.pause();
        
        // تحديث واجهة المستخدم
        this.updatePlayButton(button, false);
        const player = button.closest('.voice-player');
        player.classList.remove('voice-playing');
        player.classList.add('voice-paused');
        
        // إزالة من قائمة الملفات النشطة
        this.activeAudios.delete(audio);
    }
    
    // إيقاف جميع الملفات الصوتية النشطة
    pauseAllActiveAudio() {
        this.activeAudios.forEach((button, audio) => {
            this.pauseVoice(button, audio);
        });
        this.activeAudios.clear();
    }
    
    // تحديث زر التشغيل
    updatePlayButton(button, isPlaying) {
        button.setAttribute('data-playing', isPlaying.toString());
        
        const icon = button.querySelector('i');
        if (isPlaying) {
            icon.className = 'fas fa-pause text-white text-sm';
        } else {
            icon.className = 'fas fa-play text-white text-sm';
        }
    }
    
    // تحديث شريط التقدم
    updateVoiceProgress(audio) {
        const player = audio.closest('.voice-player');
        if (!player) return;
        
        const progressBar = player.querySelector('.progress-bar');
        const currentTimeDisplay = player.querySelector('.current-time');
        
        if (audio.duration && audio.currentTime) {
            const progress = (audio.currentTime / audio.duration) * 100;
            if (progressBar) {
                progressBar.style.width = progress + '%';
            }
            
            if (currentTimeDisplay) {
                const minutes = Math.floor(audio.currentTime / 60);
                const seconds = Math.floor(audio.currentTime % 60);
                currentTimeDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            }
        }
    }
    
    // عند انتهاء الصوت
    voiceEnded(audio) {
        const player = audio.closest('.voice-player');
        const button = player.querySelector('.voice-btn');
        
        // إعادة تعيين واجهة المستخدم
        this.updatePlayButton(button, false);
        player.classList.remove('voice-playing', 'voice-paused');
        
        const progressBar = player.querySelector('.progress-bar');
        const currentTimeDisplay = player.querySelector('.current-time');
        
        if (progressBar) progressBar.style.width = '0%';
        if (currentTimeDisplay) currentTimeDisplay.textContent = '0:00';
        
        // إزالة من قائمة الملفات النشطة
        this.activeAudios.delete(audio);
    }
    
    // عند تحميل البيانات الأساسية للصوت
    voiceLoaded(audio) {
        const player = audio.closest('.voice-player');
        const durationDisplay = player.querySelector('.duration-display');
        
        if (audio.duration && durationDisplay && !durationDisplay.dataset.original) {
            const minutes = Math.floor(audio.duration / 60);
            const seconds = Math.floor(audio.duration % 60);
            durationDisplay.dataset.original = durationDisplay.textContent;
            durationDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }
    }
    
    // بدء التسجيل
    async startRecording() {
        try {
            // إيقاف جميع الملفات الصوتية
            this.pauseAllActiveAudio();
            
            // طلب إذن الوصول للميكروفون
            const stream = await navigator.mediaDevices.getUserMedia(this.recordingConstraints);
            
            // إعداد المسجل
            const options = {
                mimeType: 'audio/webm;codecs=opus'
            };
            
            // التحقق من دعم نوع الملف
            if (!MediaRecorder.isTypeSupported(options.mimeType)) {
                options.mimeType = 'audio/webm';
                if (!MediaRecorder.isTypeSupported(options.mimeType)) {
                    options.mimeType = 'audio/mp4';
                    if (!MediaRecorder.isTypeSupported(options.mimeType)) {
                        delete options.mimeType;
                    }
                }
            }
            
            this.mediaRecorder = new MediaRecorder(stream, options);
            this.audioChunks = [];
            
            this.mediaRecorder.ondataavailable = (event) => {
                if (event.data.size > 0) {
                    this.audioChunks.push(event.data);
                }
            };
            
            this.mediaRecorder.onstop = () => {
                const audioBlob = new Blob(this.audioChunks, { 
                    type: this.mediaRecorder.mimeType || 'audio/webm' 
                });
                this.handleRecordingComplete(audioBlob);
                
                // إيقاف جميع المسارات
                stream.getTracks().forEach(track => track.stop());
            };
            
            this.mediaRecorder.onerror = (event) => {
                console.error('Recording error:', event.error);
                this.showError('خطأ في التسجيل');
                this.stopRecording();
            };
            
            // بدء التسجيل
            this.mediaRecorder.start(100); // جمع البيانات كل 100ms
            this.isRecording = true;
            this.recordingStartTime = Date.now();
            
            // تحديث واجهة المستخدم
            this.updateRecordingUI(true);
            this.startRecordingTimer();
            
        } catch (error) {
            console.error('Error starting recording:', error);
            if (error.name === 'NotAllowedError') {
                this.showError('تم رفض الوصول للميكروفون. يرجى السماح بالوصول للميكروفون في إعدادات المتصفح.');
            } else if (error.name === 'NotFoundError') {
                this.showError('لم يتم العثور على ميكروفون. تأكد من وجود ميكروفون متصل بالجهاز.');
            } else {
                this.showError('خطأ في الوصول للميكروفون: ' + error.message);
            }
        }
    }
    
    // إيقاف التسجيل
    stopRecording() {
        if (this.mediaRecorder && this.isRecording) {
            this.mediaRecorder.stop();
            this.isRecording = false;
            
            // تحديث واجهة المستخدم
            this.updateRecordingUI(false);
            this.stopRecordingTimer();
        }
    }
    
    // تبديل التسجيل
    toggleRecording() {
        if (!this.isRecording) {
            this.startRecording();
        } else {
            this.stopRecording();
        }
    }
    
    // تحديث واجهة التسجيل
    updateRecordingUI(isRecording) {
        const voiceBtn = document.getElementById('voiceBtn');
        const recordingStatus = document.getElementById('recordingStatus');
        
        if (voiceBtn) {
            if (isRecording) {
                voiceBtn.classList.remove('bg-gray-100', 'hover:bg-gray-200');
                voiceBtn.classList.add('bg-red-500', 'hover:bg-red-600', 'recording-pulse');
                voiceBtn.innerHTML = '<i class="fas fa-stop text-white"></i>';
            } else {
                voiceBtn.classList.remove('bg-red-500', 'hover:bg-red-600', 'recording-pulse');
                voiceBtn.classList.add('bg-gray-100', 'hover:bg-gray-200');
                voiceBtn.innerHTML = '<i class="fas fa-microphone text-gray-600"></i>';
            }
        }
        
        if (recordingStatus) {
            if (isRecording) {
                recordingStatus.classList.remove('hidden');
            } else {
                recordingStatus.classList.add('hidden');
            }
        }
    }
    
    // بدء مؤقت التسجيل
    startRecordingTimer() {
        const recordingTimeElement = document.getElementById('recordingTime');
        
        this.recordingTimer = setInterval(() => {
            const elapsed = Math.floor((Date.now() - this.recordingStartTime) / 1000);
            const minutes = Math.floor(elapsed / 60);
            const seconds = elapsed % 60;
            
            if (recordingTimeElement) {
                recordingTimeElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            }
            
            // إيقاف التسجيل تلقائياً بعد 10 دقائق
            if (elapsed >= 600) {
                this.stopRecording();
                this.showError('تم إيقاف التسجيل تلقائياً بعد 10 دقائق');
            }
        }, 100);
    }
    
    // إيقاف مؤقت التسجيل
    stopRecordingTimer() {
        if (this.recordingTimer) {
            clearInterval(this.recordingTimer);
            this.recordingTimer = null;
        }
    }
    
    // التعامل مع اكتمال التسجيل
    handleRecordingComplete(audioBlob) {
        // حساب مدة التسجيل التقريبية
        const recordingDuration = this.recordingStartTime ? 
            Math.max(1, Math.floor((Date.now() - this.recordingStartTime) / 1000)) : 
            Math.max(1, Math.round(audioBlob.size / 16000));
        
        // إرسال الملف الصوتي
        this.sendVoiceMessage(audioBlob, recordingDuration);
    }
    
    // إرسال الرسالة الصوتية
    async sendVoiceMessage(audioBlob, duration) {
        try {
            // تحويل إلى Base64
            const reader = new FileReader();
            
            const base64Data = await new Promise((resolve, reject) => {
                reader.onload = () => resolve(reader.result);
                reader.onerror = reject;
                reader.readAsDataURL(audioBlob);
            });
            
            // إعداد البيانات
            const formData = new FormData();
            formData.append('message_type', 'voice');
            formData.append('voice', base64Data);
            formData.append('duration', duration);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            
            // إظهار مؤشر التحميل
            const loadingMessage = this.addLoadingMessage('جاري رفع التسجيل الصوتي...');
            
            // إرسال الطلب
            const chatId = window.chatId || document.querySelector('[data-chat-id]')?.dataset.chatId;
            const response = await fetch(`/admin/customer-communication/${chatId}/send`, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            // إزالة مؤشر التحميل
            this.removeLoadingMessage(loadingMessage);
            
            if (data.success) {
                // إضافة الرسالة للشات مباشرة
                if (window.addNewMessageToChat) {
                    window.addNewMessageToChat(data.message);
                    window.lastMessageId = Math.max(window.lastMessageId || 0, data.message.id);
                    window.scrollToBottom();
                }
            } else {
                this.showError('حدث خطأ في إرسال التسجيل الصوتي');
            }
            
        } catch (error) {
            console.error('Error sending voice message:', error);
            this.showError('حدث خطأ في إرسال التسجيل الصوتي');
        }
    }
    
    // إضافة رسالة تحميل
    addLoadingMessage(text) {
        const messagesList = document.getElementById('messagesList');
        if (!messagesList) return null;
        
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'flex justify-end loading-message';
        loadingDiv.innerHTML = `
            <div class="max-w-xs lg:max-w-md">
                <div class="rounded-2xl px-4 py-3 bg-gray-300 text-gray-600">
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <div class="animate-spin w-4 h-4 border-2 border-gray-500 border-t-transparent rounded-full"></div>
                        <span class="text-sm">${text}</span>
                    </div>
                </div>
            </div>
        `;
        
        messagesList.appendChild(loadingDiv);
        if (window.scrollToBottom) window.scrollToBottom();
        
        return loadingDiv;
    }
    
    // إزالة رسالة التحميل
    removeLoadingMessage(loadingDiv) {
        if (loadingDiv && loadingDiv.parentNode) {
            loadingDiv.parentNode.removeChild(loadingDiv);
        }
    }
    
    // عرض رسالة خطأ
    showError(message) {
        // إنشاء تنبيه مؤقت
        const alert = document.createElement('div');
        alert.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-3 rounded-lg shadow-lg z-50 max-w-sm';
        alert.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-auto">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(alert);
        
        // إزالة تلقائية بعد 5 ثواني
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }
    
    // تنظيف الموارد
    cleanup() {
        this.pauseAllActiveAudio();
        if (this.isRecording) {
            this.stopRecording();
        }
        this.activeAudios.clear();
    }
}

// إنشاء مثيل عام للاستخدام
window.voiceHandler = new VoiceHandler();

// وظائف عامة للاستخدام في HTML
window.toggleVoicePlay = (button, audioUrl, messageId) => {
    window.voiceHandler.toggleVoicePlay(button, audioUrl, messageId);
};

window.toggleVoiceRecording = () => {
    window.voiceHandler.toggleRecording();
};

window.updateVoiceProgress = (audio) => {
    window.voiceHandler.updateVoiceProgress(audio);
};

window.voiceEnded = (audio) => {
    window.voiceHandler.voiceEnded(audio);
};

window.voiceLoaded = (audio) => {
    window.voiceHandler.voiceLoaded(audio);
};