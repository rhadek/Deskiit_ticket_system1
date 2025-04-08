class TimeTracker {
    constructor() {
        this.isTracking = false;
        this.startTime = null;
        this.elapsedTime = 0;
        this.timer = null;
        this.requestId = null;
        this.updateCallback = null;
        this.apiBaseUrl = '/api/time-tracker';
        this.sessionId = null;
        this.syncInterval = null;
    }

    async start(requestId) {
        if (this.isTracking) {
            console.error('Time tracker is already running');
            return false;
        }

        if (!requestId) {
            console.error('No request ID provided');
            return false;
        }

        // Validate requestId is a number
        const requestIdNum = parseInt(requestId, 10);
        if (isNaN(requestIdNum) || requestIdNum <= 0) {
            console.error('Invalid request ID:', requestId);
            return false;
        }

        this.isTracking = true;
        this.requestId = requestIdNum; // Use the validated number
        this.startTime = new Date();

        try {
            console.log('Starting time tracker for request ID:', this.requestId, 'at:', this.startTime.toISOString());

            // Volání API pro zahájení trackování
            const response = await fetch(`${this.apiBaseUrl}/start`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    id_request: this.requestId,
                    start_time: this.startTime.toISOString()
                })
            });

            // Check for network errors
            if (!response.ok) {
                const data = await response.json().catch(() => ({ message: 'Nepodařilo se zpracovat odpověď ze serveru' }));
                console.error('API Error:', data.message || response.statusText);
                this.isTracking = false;

                // Vyvolat událost pro zobrazení chyby
                const event = new CustomEvent('timetracker-error', {
                    detail: { message: data.message || `Chyba serveru: ${response.status} ${response.statusText}` }
                });
                document.dispatchEvent(event);

                return false;
            }

            // Parse response data
            let data;
            try {
                data = await response.json();
                console.log('API response:', data);
            } catch (error) {
                console.error('Failed to parse JSON response:', error);
                this.isTracking = false;

                const event = new CustomEvent('timetracker-error', {
                    detail: { message: 'Nepodařilo se zpracovat odpověď ze serveru: ' + error.message }
                });
                document.dispatchEvent(event);

                return false;
            }

            // Check if response has expected format
            if (!data || !data.success || !data.session || !data.session.id) {
                console.error('Invalid API response:', data);
                this.isTracking = false;

                const event = new CustomEvent('timetracker-error', {
                    detail: { message: 'Neplatná odpověď ze serveru' }
                });
                document.dispatchEvent(event);

                return false;
            }

            // Uložení session ID
            this.sessionId = data.session.id;

            // Get CSRF token for future requests
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            if (!csrfToken) {
                console.warn('CSRF token not found. Future requests might fail.');
            }

            // Pokračovat v lokálním ukládání pro případ výpadku připojení
            localStorage.setItem('timeTracker_startTime', this.startTime.toISOString());
            localStorage.setItem('timeTracker_requestId', this.requestId);
            localStorage.setItem('timeTracker_isTracking', 'true');
            localStorage.setItem('timeTracker_elapsedTime', '0');
            localStorage.setItem('timeTracker_sessionId', this.sessionId);

            // Nastavení timeru pro tiky časovače
            this.timer = setInterval(() => {
                this.elapsedTime = Math.floor((new Date() - this.startTime) / 1000);
                localStorage.setItem('timeTracker_elapsedTime', this.elapsedTime.toString());

                if (this.updateCallback) {
                    this.updateCallback(this.formatTime(this.elapsedTime));
                }
            }, 1000);

            // Nastavení intervalu pro synchronizaci s backend
            this.syncInterval = setInterval(() => {
                this.checkSessionValidity();
            }, 30000); // Kontrola každých 30 sekund

            console.log('TimeTracker started for request ID:', requestId, 'Session ID:', this.sessionId);

            // Pokud byl změněn stav požadavku, reloadnout stránku
            if (data.request_updated) {
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            }

            return true;
        } catch (error) {
            console.error('Error starting time tracker:', error);
            this.isTracking = false;

            // Vyvolat událost pro zobrazení chyby
            const event = new CustomEvent('timetracker-error', {
                detail: { message: 'Při spouštění časovače došlo k chybě: ' + error.message }
            });
            document.dispatchEvent(event);

            return false;
        }
    }

    async stop() {
        if (!this.isTracking) return null;

        clearInterval(this.timer);
        clearInterval(this.syncInterval); // Vyčistit interval synchronizace
        this.isTracking = false;

        const endTime = new Date();
        const totalTime = Math.floor((endTime - this.startTime) / 1000);
        const totalMinutes = Math.ceil(totalTime / 60); // Zaokrouhleno nahoru

        try {
            // Volání API pro ukončení trackování
            const response = await fetch(`${this.apiBaseUrl}/stop`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    id_request: this.requestId,
                    end_time: endTime.toISOString(),
                    total_minutes: totalMinutes
                })
            });

            const data = await response.json();

            if (!response.ok) {
                console.error('API Error:', data.message);
                // Pokračujeme i při chybě, aby uživatel mohl dokončit report
            }
        } catch (error) {
            console.error('Error stopping time tracker:', error);
            // Pokračujeme i při chybě, aby uživatel mohl dokončit report
        }

        const result = {
            requestId: this.requestId,
            startTime: this.startTime,
            endTime: endTime,
            totalSeconds: totalTime,
            totalMinutes: totalMinutes,
            sessionId: this.sessionId
        };

        // Vyčistit lokální úložiště
        localStorage.removeItem('timeTracker_startTime');
        localStorage.removeItem('timeTracker_requestId');
        localStorage.removeItem('timeTracker_isTracking');
        localStorage.removeItem('timeTracker_elapsedTime');
        localStorage.removeItem('timeTracker_sessionId');

        this.timer = null;
        this.startTime = null;
        this.elapsedTime = 0;
        this.requestId = null;
        this.sessionId = null;

        console.log('TimeTracker stopped with result:', result);
        return result;
    }

    async cancel() {
        if (!this.isTracking) return false;

        clearInterval(this.timer);
        clearInterval(this.syncInterval); // Vyčistit interval synchronizace
        this.isTracking = false;

        try {
            // Volání API pro zrušení trackování
            const response = await fetch(`${this.apiBaseUrl}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    id_request: this.requestId
                })
            });

            const data = await response.json();

            if (!response.ok) {
                console.error('API Error:', data.message);
            }
        } catch (error) {
            console.error('Error canceling time tracker:', error);
        }

        // Vyčistit lokální úložiště
        localStorage.removeItem('timeTracker_startTime');
        localStorage.removeItem('timeTracker_requestId');
        localStorage.removeItem('timeTracker_isTracking');
        localStorage.removeItem('timeTracker_elapsedTime');
        localStorage.removeItem('timeTracker_sessionId');

        this.timer = null;
        this.startTime = null;
        this.elapsedTime = 0;
        this.requestId = null;
        this.sessionId = null;

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

    async checkForActiveSession() {
        // Nejprve zkusíme získat aktivní session z API
        try {
            const response = await fetch(`${this.apiBaseUrl}/active`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success && data.session) {
                    this.sessionId = data.session.id;
                    this.requestId = data.session.id_request;
                    this.startTime = new Date(data.session.start_time);
                    this.isTracking = true;

                    // Výpočet uplynulého času
                    this.elapsedTime = Math.floor((new Date() - this.startTime) / 1000);

                    // Nastavení timeru
                    this.timer = setInterval(() => {
                        this.elapsedTime = Math.floor((new Date() - this.startTime) / 1000);

                        if (this.updateCallback) {
                            this.updateCallback(this.formatTime(this.elapsedTime));
                        }
                    }, 1000);

                    // Nastavení intervalu pro synchronizaci s backend
                    this.syncInterval = setInterval(() => {
                        this.checkSessionValidity();
                    }, 30000); // Kontrola každých 30 sekund

                    console.log('Restored active TimeTracker session from API, request ID:', this.requestId);

                    return {
                        requestId: this.requestId,
                        elapsedTime: this.formatTime(this.elapsedTime),
                        sessionId: this.sessionId
                    };
                }
            }
        } catch (error) {
            console.warn('Could not fetch active session from API, trying localStorage fallback');
        }

        // Fallback na localStorage
        const isTracking = localStorage.getItem('timeTracker_isTracking') === 'true';
        if (isTracking) {
            const startTimeStr = localStorage.getItem('timeTracker_startTime');
            this.requestId = localStorage.getItem('timeTracker_requestId');
            this.sessionId = localStorage.getItem('timeTracker_sessionId');

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

            // Nastavení intervalu pro synchronizaci s backend
            this.syncInterval = setInterval(() => {
                this.checkSessionValidity();
            }, 30000); // Kontrola každých 30 sekund

            console.log('Restored active TimeTracker session from localStorage, request ID:', this.requestId);

            return {
                requestId: this.requestId,
                elapsedTime: this.formatTime(this.elapsedTime),
                sessionId: this.sessionId
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
            sessionId: this.sessionId,
            elapsedTime: this.elapsedTime,
            formattedTime: this.formatTime(this.elapsedTime)
        };
    }

    async checkSessionValidity() {
        // Kontrola, zda je session stále platná
        if (!this.isTracking || !this.sessionId) return;

        try {
            const response = await fetch(`${this.apiBaseUrl}/check/${this.sessionId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            // Pokud session už neexistuje nebo byla ukončena jinde
            if (!response.ok || !data.success || !data.session || data.session.completed) {
                console.log('Session was completed elsewhere, stopping local timer');

                // Zastavit místní časovač
                clearInterval(this.timer);
                clearInterval(this.syncInterval);
                this.isTracking = false;

                // Vyčistit lokální úložiště
                localStorage.removeItem('timeTracker_startTime');
                localStorage.removeItem('timeTracker_requestId');
                localStorage.removeItem('timeTracker_isTracking');
                localStorage.removeItem('timeTracker_elapsedTime');
                localStorage.removeItem('timeTracker_sessionId');

                this.timer = null;
                this.startTime = null;
                this.elapsedTime = 0;
                this.requestId = null;
                this.sessionId = null;

                // Upozornit UI
                if (this.updateCallback) {
                    this.updateCallback('00:00:00');

                    // Vyvolat událost pro resetování UI
                    const event = new CustomEvent('timetracker-reset', {
                        detail: { reason: 'completed_elsewhere' }
                    });
                    document.dispatchEvent(event);
                }
            }
        } catch (error) {
            console.warn('Failed to check session validity:', error);
            // Při chybě připojení necháme časovač běžet dále
        }
    }
}

// Export a singleton instance
export default new TimeTracker();
