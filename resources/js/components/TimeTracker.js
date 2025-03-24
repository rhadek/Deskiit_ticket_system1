class TimeTracker {
    constructor() {
        this.isTracking = false;
        this.startTime = null;
        this.elapsedTime = 0;
        this.timer = null;
        this.requestId = null;
        this.updateCallback = null;
    }

    // Start the timer
    start(requestId) {
        if (this.isTracking) return; // Don't start if already tracking

        this.isTracking = true;
        this.requestId = requestId;
        this.startTime = new Date();

        // Save the start time to localStorage for persistence
        localStorage.setItem('timeTracker_startTime', this.startTime.toISOString());
        localStorage.setItem('timeTracker_requestId', this.requestId);
        localStorage.setItem('timeTracker_isTracking', 'true');
        localStorage.setItem('timeTracker_elapsedTime', '0');

        // Start the timer to update the display
        this.timer = setInterval(() => {
            this.elapsedTime = Math.floor((new Date() - this.startTime) / 1000);
            localStorage.setItem('timeTracker_elapsedTime', this.elapsedTime.toString());

            if (this.updateCallback) {
                this.updateCallback(this.formatTime(this.elapsedTime));
            }
        }, 1000);
    }

    // Stop the timer
    stop() {
        if (!this.isTracking) return;

        clearInterval(this.timer);
        this.isTracking = false;

        const endTime = new Date();
        const totalTime = Math.floor((endTime - this.startTime) / 1000);

        // Clear localStorage
        localStorage.removeItem('timeTracker_startTime');
        localStorage.removeItem('timeTracker_requestId');
        localStorage.removeItem('timeTracker_isTracking');
        localStorage.removeItem('timeTracker_elapsedTime');

        // Reset the timer
        this.timer = null;
        this.startTime = null;
        this.elapsedTime = 0;

        return {
            requestId: this.requestId,
            startTime: this.startTime,
            endTime: endTime,
            totalSeconds: totalTime
        };
    }

    // Cancel the timer without saving
    cancel() {
        if (!this.isTracking) return;

        clearInterval(this.timer);
        this.isTracking = false;

        // Clear localStorage
        localStorage.removeItem('timeTracker_startTime');
        localStorage.removeItem('timeTracker_requestId');
        localStorage.removeItem('timeTracker_isTracking');
        localStorage.removeItem('timeTracker_elapsedTime');

        // Reset the timer
        this.timer = null;
        this.startTime = null;
        this.elapsedTime = 0;
    }

    // Format seconds into HH:MM:SS
    formatTime(seconds) {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;

        return [
            hours.toString().padStart(2, '0'),
            minutes.toString().padStart(2, '0'),
            secs.toString().padStart(2, '0')
        ].join(':');
    }

    // Check if there's an active tracking session
    checkForActiveSession() {
        const isTracking = localStorage.getItem('timeTracker_isTracking') === 'true';
        if (isTracking) {
            const startTimeStr = localStorage.getItem('timeTracker_startTime');
            this.requestId = localStorage.getItem('timeTracker_requestId');
            this.startTime = new Date(startTimeStr);
            this.elapsedTime = parseInt(localStorage.getItem('timeTracker_elapsedTime') || '0');
            this.isTracking = true;

            // Restart the timer
            this.timer = setInterval(() => {
                this.elapsedTime = Math.floor((new Date() - this.startTime) / 1000);
                localStorage.setItem('timeTracker_elapsedTime', this.elapsedTime.toString());

                if (this.updateCallback) {
                    this.updateCallback(this.formatTime(this.elapsedTime));
                }
            }, 1000);

            return {
                requestId: this.requestId,
                elapsedTime: this.formatTime(this.elapsedTime)
            };
        }

        return null;
    }

    // Set callback for timer updates
    onUpdate(callback) {
        this.updateCallback = callback;
    }

    // Get current status
    getStatus() {
        return {
            isTracking: this.isTracking,
            requestId: this.requestId,
            elapsedTime: this.elapsedTime,
            formattedTime: this.formatTime(this.elapsedTime)
        };
    }
}

// Export a singleton instance
export default new TimeTracker();
