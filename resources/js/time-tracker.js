// Debug logger
const debugElement = document.getElementById('timeTracker_debug');

function logDebug(message, data = null) {
    console.log(message, data || '');

    if (debugElement) {
        const timestamp = new Date().toLocaleTimeString();
        let logText = `[${timestamp}] ${message}`;

        if (data) {
            try {
                if (typeof data === 'object') {
                    logText += '\n' + JSON.stringify(data, null, 2);
                } else {
                    logText += '\n' + data.toString();
                }
            } catch (e) {
                logText += '\n[Cannot stringify data]';
            }
        }

        debugElement.innerHTML = logText + '\n\n' + debugElement.innerHTML;

        // Limit debug log size
        if (debugElement.innerHTML.length > 5000) {
            debugElement.innerHTML = debugElement.innerHTML.substring(0, 5000) + '...';
        }
    }
}    function showNotification(type, message) {
    // Odstranit existující notifikace
    const existingNotifications = document.querySelectorAll('.timetracker-notification');
    existingNotifications.forEach(n => n.remove());

    // Nastavit barvy na základě typu
    let bgColor, borderColor, textColor, iconColor;
    let iconPath = '';

    if (type === 'error') {
        bgColor = 'bg-red-100';
        borderColor = 'border-red-400';
        textColor = 'text-red-700';
        iconColor = 'text-red-600';
        iconPath = 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z';
    } else if (type === 'warning') {
        bgColor = 'bg-yellow-100';
        borderColor = 'border-yellow-400';
        textColor = 'text-yellow-700';
        iconColor = 'text-yellow-600';
        iconPath = 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z';
    } else {
        bgColor = 'bg-blue-100';
        borderColor = 'border-blue-400';
        textColor = 'text-blue-700';
        iconColor = 'text-blue-600';
        iconPath = 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
    }

    // Vytvořit notifikaci
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${bgColor} border ${borderColor} ${textColor} px-4 py-3 rounded max-w-md shadow-lg z-50 timetracker-notification`;
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="h-6 w-6 mr-2 ${iconColor}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${iconPath}" />
            </svg>
            <p>${message}</p>
        </div>
    `;
    document.body.appendChild(notification);

    // Automatické odstranění notifikace po 5 sekundách
    setTimeout(() => {
        notification.remove();
    }, 5000);
}import TimeTracker from './components/TimeTracker';

document.addEventListener('DOMContentLoaded', function() {
initTimeTracker();

// Poslouchání na události resetu časovače
document.addEventListener('timetracker-reset', function(event) {
    console.log('Time tracker reset event received:', event.detail.reason);
    resetTimerUI();

    if (event.detail.reason === 'completed_elsewhere') {
        // Můžeme zobrazit notifikaci uživateli
        showNotification('warning', 'Časovač byl ukončen v jiném zařízení nebo okně.');
    }
});

// Poslouchání na události chyb časovače
document.addEventListener('timetracker-error', function(event) {
    console.log('Time tracker error event received:', event.detail.message);
    resetTimerUI();
    showNotification('error', event.detail.message);
});
});

function initTimeTracker() {
const trackerComponent = document.getElementById('time-tracker-component');
if (!trackerComponent) return;

const currentRequestId = trackerComponent.dataset.requestId;
const currentRequestName = trackerComponent.dataset.requestName;

const timerElement = document.getElementById('timeTracker_timer');
const startBtn = document.getElementById('timeTracker_startBtn');
const stopBtn = document.getElementById('timeTracker_stopBtn');
const cancelBtn = document.getElementById('timeTracker_cancelBtn');
const activeControls = document.getElementById('timeTracker_activeControls');
const activeSession = document.getElementById('timeTracker_activeSession');
const requestInfo = document.getElementById('timeTracker_requestInfo');
const requestNameElement = document.getElementById('timeTracker_requestName');
const differentRequestWarning = document.getElementById('timeTracker_differentRequestWarning');

const reportModal = document.getElementById('timeTracker_reportModal');
const saveReportBtn = document.getElementById('timeTracker_saveReportBtn');
const cancelReportBtn = document.getElementById('timeTracker_cancelReportBtn');

const reportForm = document.getElementById('timeTracker_reportForm');
const formRequestId = document.getElementById('timeTracker_form_requestId');
const formWorkStart = document.getElementById('timeTracker_form_work_start');
const formWorkEnd = document.getElementById('timeTracker_form_work_end');
const formWorkTotal = document.getElementById('timeTracker_form_work_total');
const formCsrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

const summaryStart = document.getElementById('timeTracker_summary_start');
const summaryEnd = document.getElementById('timeTracker_summary_end');
const summaryTotal = document.getElementById('timeTracker_summary_total');
const summaryMinutes = document.getElementById('timeTracker_summary_minutes');

// Přidat indikátor načítání
const loadingIndicator = document.createElement('div');
loadingIndicator.id = 'timeTracker_loading';
loadingIndicator.className = 'hidden';
loadingIndicator.innerHTML = '<div class="inline-block animate-spin h-4 w-4 border-2 border-indigo-500 border-t-transparent rounded-full mr-2"></div><span>Načítání...</span>';
trackerComponent.appendChild(loadingIndicator);

TimeTracker.onUpdate(function(formattedTime) {
    if (timerElement) {
        timerElement.textContent = formattedTime;
    }
});

// Kontrola aktivní session
checkActiveSession();

async function checkActiveSession() {
    showLoading(true);

    try {
        logDebug('Checking for active session...');
        const activeSessionData = await TimeTracker.checkForActiveSession();
        logDebug('Active session data:', activeSessionData);

        if (activeSessionData) {
            if (activeControls) activeControls.classList.remove('hidden');
            if (startBtn) startBtn.classList.add('hidden');
            if (activeSession) activeSession.classList.remove('hidden');
            if (requestInfo) requestInfo.classList.remove('hidden');

            fetchRequestName(activeSessionData.requestId).then(nameData => {
                const name = nameData?.name || `Požadavek #${activeSessionData.requestId}`;
                if (requestNameElement) requestNameElement.textContent = name;

                if (currentRequestId && currentRequestId != activeSessionData.requestId) {
                    differentRequestWarning.classList.remove('hidden');
                }
            });

            if (timerElement) {
                timerElement.textContent = activeSessionData.elapsedTime;
            }
        } else if (currentRequestId) {
            logDebug('No active session, but current request ID is set:', currentRequestId);
            if (requestNameElement) {
                requestNameElement.textContent = currentRequestName || `Požadavek #${currentRequestId}`;
            }
            if (requestInfo) {
                requestInfo.classList.remove('hidden');
            }
        } else {
            logDebug('No active session and no current request ID');
        }
    } catch (error) {
        logDebug('Error checking active session:', error.message);
        showNotification('error', 'Chyba při kontrole aktivní session: ' + error.message);
    } finally {
        showLoading(false);
    }
}

if (startBtn) {
    startBtn.addEventListener('click', async function() {
        showLoading(true);

        try {
            const activeRequestId = currentRequestId || prompt('Zadejte ID požadavku, pro který chcete měřit čas:');

            if (activeRequestId) {
                logDebug('Starting timer for request ID:', activeRequestId);
                const started = await TimeTracker.start(activeRequestId);

                if (started) {
                    logDebug('Timer started successfully');
                    if (activeControls) activeControls.classList.remove('hidden');
                    if (startBtn) startBtn.classList.add('hidden');
                    if (activeSession) activeSession.classList.remove('hidden');
                    if (requestInfo) requestInfo.classList.remove('hidden');

                    if (!currentRequestId) {
                        fetchRequestName(activeRequestId).then(nameData => {
                            const name = nameData?.name || `Požadavek #${activeRequestId}`;
                            if (requestNameElement) requestNameElement.textContent = name;
                        });
                    } else {
                        if (requestNameElement) {
                            requestNameElement.textContent = currentRequestName || `Požadavek #${currentRequestId}`;
                        }
                    }
                } else {
                    logDebug('Timer failed to start');
                    // Chyba bude oznámena přes timetracker-error událost
                }
            } else {
                logDebug('No request ID provided');
                showNotification('warning', 'Nebylo zadáno ID požadavku');
            }
        } catch (error) {
            logDebug('Error in start button handler:', error.message);
            showNotification('error', 'Při spouštění časovače došlo k chybě: ' + error.message);
        } finally {
            showLoading(false);
        }
    });
}

if (stopBtn) {
    stopBtn.addEventListener('click', async function() {
        showLoading(true);

        try {
            logDebug('Stopping time tracker');
            const result = await TimeTracker.stop();
            if (result) {
                logDebug('Time tracker stopped successfully', result);
                showReportModal(result);
            } else {
                logDebug('Failed to stop time tracker - no result returned');
                showNotification('error', 'Nepodařilo se zastavit časovač');
            }
        } catch (error) {
            logDebug('Error stopping timer:', error.message);
            showNotification('error', 'Při zastavení časovače došlo k chybě: ' + error.message);
        } finally {
            showLoading(false);
        }
    });
}

if (cancelBtn) {
    cancelBtn.addEventListener('click', async function() {
        if (confirm('Opravdu chcete zrušit měření času? Data budou ztracena.')) {
            showLoading(true);

            try {
                logDebug('Canceling time tracker');
                const canceled = await TimeTracker.cancel();
                if (canceled) {
                    logDebug('Time tracker canceled successfully');
                    resetTimerUI();
                } else {
                    logDebug('Failed to cancel time tracker');
                    showNotification('error', 'Nepodařilo se zrušit časovač');
                }
            } catch (error) {
                logDebug('Error canceling timer:', error.message);
                showNotification('error', 'Při rušení časovače došlo k chybě: ' + error.message);
            } finally {
                showLoading(false);
            }
        }
    });
}

if (saveReportBtn) {
    saveReportBtn.addEventListener('click', function() {
        if (reportForm && reportForm.checkValidity()) {
            saveReport();
        } else if (reportForm) {
            reportForm.reportValidity();
        }
    });
}

if (cancelReportBtn) {
    cancelReportBtn.addEventListener('click', function() {
        if (reportModal) {
            reportModal.classList.add('hidden');
            resetTimerUI();
        }
    });
}

function resetTimerUI() {
    logDebug('Resetting timer UI');
    if (activeControls) activeControls.classList.add('hidden');
    if (startBtn) startBtn.classList.remove('hidden');
    if (activeSession) activeSession.classList.add('hidden');
    if (timerElement) timerElement.textContent = '00:00:00';
    if (differentRequestWarning) differentRequestWarning.classList.add('hidden');

    if (!currentRequestId && requestInfo) {
        requestInfo.classList.add('hidden');
    }
}

function showReportModal(result) {
    logDebug('Showing report modal with result:', result);
    if (formRequestId) formRequestId.value = result.requestId;
    if (formWorkStart) formWorkStart.value = result.startTime.toISOString();
    if (formWorkEnd) formWorkEnd.value = result.endTime.toISOString();

    const totalMinutes = result.totalMinutes;
    if (formWorkTotal) formWorkTotal.value = totalMinutes;

    // Přidáme session ID pokud existuje
    if (result.sessionId) {
        // Kontrola, zda už existuje skryté pole pro session_id, pokud ne, vytvoříme ho
        let sessionIdInput = document.getElementById('timeTracker_form_session_id');
        if (!sessionIdInput) {
            sessionIdInput = document.createElement('input');
            sessionIdInput.type = 'hidden';
            sessionIdInput.id = 'timeTracker_form_session_id';
            sessionIdInput.name = 'session_id';
            reportForm.appendChild(sessionIdInput);
        }
        sessionIdInput.value = result.sessionId;
        logDebug('Set session ID in form:', result.sessionId);
    } else {
        logDebug('No session ID available');
    }

    if (summaryStart) summaryStart.textContent = formatDateTime(result.startTime);
    if (summaryEnd) summaryEnd.textContent = formatDateTime(result.endTime);
    if (summaryTotal) summaryTotal.textContent = formatDuration(result.totalSeconds);
    if (summaryMinutes) summaryMinutes.textContent = totalMinutes + ' min';

    if (reportModal) reportModal.classList.remove('hidden');
}

function saveReport() {
    showLoading(true);
    logDebug('Saving report...');

    const formData = new FormData(reportForm);
    formData.append('_token', formCsrfToken);

    // Zkontrolovat, jestli existuje session_id k odeslání
    const sessionIdInput = document.getElementById('timeTracker_form_session_id');
    if (sessionIdInput && sessionIdInput.value) {
        formData.append('session_id', sessionIdInput.value);
        logDebug('Including session ID in report:', sessionIdInput.value);
    } else {
        logDebug('No session ID to include in report');
    }

    fetch('/request-reports', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        if (response.ok) {
            logDebug('Report saved successfully');
            return { success: true };
        }

        if (response.headers.get('content-type')?.includes('application/json')) {
            return response.json().then(err => {
                logDebug('Error response from server:', err);
                throw new Error(err.message || 'Network response was not ok');
            });
        } else {
            logDebug('Non-JSON error response from server');
            throw new Error('Server returned an error');
        }
    })
    .then(data => {
        if (reportModal) reportModal.classList.add('hidden');
        resetTimerUI();
        showNotification('info', 'Report byl úspěšně uložen!');

        if (window.location.href.includes('/requests/')) {
            logDebug('Reloading page for request view');
            window.location.reload();
        }
    })
    .catch(error => {
        logDebug('Error saving report:', error.message);

        if (window.location.href.includes('/requests/')) {
            window.location.reload();
        } else {
            showNotification('error', 'Při ukládání reportu došlo k chybě: ' + error.message);
        }
    })
    .finally(() => {
        showLoading(false);
    });
}

function fetchRequestName(requestId) {
    logDebug('Fetching request name for ID: ' + requestId);
    return fetch(`/api/requests/${requestId}/name`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            logDebug('Request name fetched successfully:', data);
            return data;
        })
        .catch(error => {
            logDebug('Error fetching request name:', error.message);
            return { name: null };
        });
}

function formatDateTime(date) {
    return new Date(date).toLocaleString('cs-CZ', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function formatDuration(seconds) {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;

    return [
        hours + 'h',
        minutes + 'm',
        secs + 's'
    ].join(' ');
}

function showLoading(isLoading) {
    const loadingElement = document.getElementById('timeTracker_loading');
    if (loadingElement) {
        if (isLoading) {
            logDebug('Showing loading indicator');
            loadingElement.classList.remove('hidden');
        } else {
            logDebug('Hiding loading indicator');
            loadingElement.classList.add('hidden');
        }
    }
}
}

