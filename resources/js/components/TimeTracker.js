
class TimeTracker {
    constructor() {
        this.isTracking = false;
        this.startTime = null;
        this.elapsedTime = 0;
        this.timer = null;
        this.requestId = null;
        this.updateCallback = null;
    }

    start(requestId) {
        if (this.isTracking) return;

        this.isTracking = true;
        this.requestId = requestId;
        this.startTime = new Date();

        localStorage.setItem('timeTracker_startTime', this.startTime.toISOString());
        localStorage.setItem('timeTracker_requestId', this.requestId);
        localStorage.setItem('timeTracker_isTracking', 'true');
        localStorage.setItem('timeTracker_elapsedTime', '0');

        this.timer = setInterval(() => {
            this.elapsedTime = Math.floor((new Date() - this.startTime) / 1000);
            localStorage.setItem('timeTracker_elapsedTime', this.elapsedTime.toString());

            if (this.updateCallback) {
                this.updateCallback(this.formatTime(this.elapsedTime));
            }
        }, 1000);

        console.log('TimeTracker started for request ID:', requestId);
        return true;
    }

    stop() {
        if (!this.isTracking) return null;

        clearInterval(this.timer);
        this.isTracking = false;

        const endTime = new Date();
        const totalTime = Math.floor((endTime - this.startTime) / 1000);
        const result = {
            requestId: this.requestId,
            startTime: this.startTime,
            endTime: endTime,
            totalSeconds: totalTime
        };

        localStorage.removeItem('timeTracker_startTime');
        localStorage.removeItem('timeTracker_requestId');
        localStorage.removeItem('timeTracker_isTracking');
        localStorage.removeItem('timeTracker_elapsedTime');

        this.timer = null;
        this.startTime = null;
        this.elapsedTime = 0;
        this.requestId = null;

        console.log('TimeTracker stopped with result:', result);
        return result;
    }

    cancel() {
        if (!this.isTracking) return false;

        clearInterval(this.timer);
        this.isTracking = false;

        localStorage.removeItem('timeTracker_startTime');
        localStorage.removeItem('timeTracker_requestId');
        localStorage.removeItem('timeTracker_isTracking');
        localStorage.removeItem('timeTracker_elapsedTime');

        this.timer = null;
        this.startTime = null;
        this.elapsedTime = 0;
        this.requestId = null;

        console.log('TimeTracker cancelled');
        return true;
    }

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

    checkForActiveSession() {
        const isTracking = localStorage.getItem('timeTracker_isTracking') === 'true';
        if (isTracking) {
            const startTimeStr = localStorage.getItem('timeTracker_startTime');
            this.requestId = localStorage.getItem('timeTracker_requestId');

            if (!startTimeStr || !this.requestId) {
                this.cancel();
                return null;
            }

            this.startTime = new Date(startTimeStr);
            const storedElapsedTime = parseInt(localStorage.getItem('timeTracker_elapsedTime') || '0');

            if (!isNaN(this.startTime.getTime())) {
                this.elapsedTime = Math.floor((new Date() - this.startTime) / 1000);

                if (storedElapsedTime > this.elapsedTime) {
                    this.elapsedTime = storedElapsedTime;
                }
            } else {
                this.elapsedTime = storedElapsedTime;
                this.startTime = new Date(new Date().getTime() - (this.elapsedTime * 1000));
            }

            this.isTracking = true;

            this.timer = setInterval(() => {
                this.elapsedTime = Math.floor((new Date() - this.startTime) / 1000);
                localStorage.setItem('timeTracker_elapsedTime', this.elapsedTime.toString());

                if (this.updateCallback) {
                    this.updateCallback(this.formatTime(this.elapsedTime));
                }
            }, 1000);

            console.log('Restored active TimeTracker session for request ID:', this.requestId);

            return {
                requestId: this.requestId,
                elapsedTime: this.formatTime(this.elapsedTime)
            };
        }

        return null;
    }

    onUpdate(callback) {
        this.updateCallback = callback;
    }

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
