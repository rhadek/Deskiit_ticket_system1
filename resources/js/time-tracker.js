// resources/js/time-tracker.js

import TimeTracker from './components/TimeTracker';

document.addEventListener('DOMContentLoaded', function() {
    initTimeTracker();
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

    // Modal elements
    const reportModal = document.getElementById('timeTracker_reportModal');
    const saveReportBtn = document.getElementById('timeTracker_saveReportBtn');
    const cancelReportBtn = document.getElementById('timeTracker_cancelReportBtn');

    // Form elements
    const reportForm = document.getElementById('timeTracker_reportForm');
    const formRequestId = document.getElementById('timeTracker_form_requestId');
    const formWorkStart = document.getElementById('timeTracker_form_work_start');
    const formWorkEnd = document.getElementById('timeTracker_form_work_end');
    const formWorkTotal = document.getElementById('timeTracker_form_work_total');
    const formCsrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Summary elements
    const summaryStart = document.getElementById('timeTracker_summary_start');
    const summaryEnd = document.getElementById('timeTracker_summary_end');
    const summaryTotal = document.getElementById('timeTracker_summary_total');
    const summaryMinutes = document.getElementById('timeTracker_summary_minutes');

    // Update callback for the timer
    TimeTracker.onUpdate(function(formattedTime) {
        if (timerElement) {
            timerElement.textContent = formattedTime;
        }
    });

    // Check for existing session
    const activeSessionData = TimeTracker.checkForActiveSession();
    if (activeSessionData) {
        // We have an active session
        if (activeControls) activeControls.classList.remove('hidden');
        if (startBtn) startBtn.classList.add('hidden');
        if (activeSession) activeSession.classList.remove('hidden');
        if (requestInfo) requestInfo.classList.remove('hidden');

        // Get the active request name using AJAX
        fetchRequestName(activeSessionData.requestId).then(nameData => {
            const name = nameData?.name || `Požadavek #${activeSessionData.requestId}`;
            if (requestNameElement) requestNameElement.textContent = name;

            // Show warning if tracking a different request than the current page
            if (currentRequestId && currentRequestId != activeSessionData.requestId) {
                differentRequestWarning.classList.remove('hidden');
            }
        });

        // Update the timer
        if (timerElement) {
            timerElement.textContent = activeSessionData.elapsedTime;
        }
    } else if (currentRequestId) {
        // On a specific request page, pre-fill request info
        if (requestNameElement) {
            requestNameElement.textContent = currentRequestName || `Požadavek #${currentRequestId}`;
        }
        if (requestInfo) {
            requestInfo.classList.remove('hidden');
        }
    }

    // Start timer button
    if (startBtn) {
        startBtn.addEventListener('click', function() {
            // If we're on a specific request page, use that request ID
            const activeRequestId = currentRequestId || prompt('Zadejte ID požadavku, pro který chcete měřit čas:');

            if (activeRequestId) {
                TimeTracker.start(activeRequestId);

                if (activeControls) activeControls.classList.remove('hidden');
                if (startBtn) startBtn.classList.add('hidden');
                if (activeSession) activeSession.classList.remove('hidden');
                if (requestInfo) requestInfo.classList.remove('hidden');

                // If we're not on a specific request page, get the request name
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
            }
        });
    }

    // Stop timer button
    if (stopBtn) {
        stopBtn.addEventListener('click', function() {
            const result = TimeTracker.stop();
            if (result) {
                showReportModal(result);
            }
        });
    }

    // Cancel timer button
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            if (confirm('Opravdu chcete zrušit měření času? Data budou ztracena.')) {
                TimeTracker.cancel();
                resetTimerUI();
            }
        });
    }

    // Save report button
    if (saveReportBtn) {
        saveReportBtn.addEventListener('click', function() {
            if (reportForm && reportForm.checkValidity()) {
                saveReport();
            } else if (reportForm) {
                reportForm.reportValidity();
            }
        });
    }

    // Cancel report button
    if (cancelReportBtn) {
        cancelReportBtn.addEventListener('click', function() {
            if (reportModal) {
                reportModal.classList.add('hidden');
                resetTimerUI();
            }
        });
    }

    function resetTimerUI() {
        if (activeControls) activeControls.classList.add('hidden');
        if (startBtn) startBtn.classList.remove('hidden');
        if (activeSession) activeSession.classList.add('hidden');
        if (timerElement) timerElement.textContent = '00:00:00';
        if (differentRequestWarning) differentRequestWarning.classList.add('hidden');

        // Only hide request info if we're not on a specific request page
        if (!currentRequestId && requestInfo) {
            requestInfo.classList.add('hidden');
        }
    }

    function showReportModal(result) {
        // Fill in the form values
        if (formRequestId) formRequestId.value = result.requestId;
        if (formWorkStart) formWorkStart.value = result.startTime.toISOString();
        if (formWorkEnd) formWorkEnd.value = result.endTime.toISOString();

        // Calculate minutes from seconds and round up
        const totalMinutes = Math.ceil(result.totalSeconds / 60);
        if (formWorkTotal) formWorkTotal.value = totalMinutes;

        // Update summary
        if (summaryStart) summaryStart.textContent = formatDateTime(result.startTime);
        if (summaryEnd) summaryEnd.textContent = formatDateTime(result.endTime);
        if (summaryTotal) summaryTotal.textContent = formatDuration(result.totalSeconds);
        if (summaryMinutes) summaryMinutes.textContent = totalMinutes + ' min';

        // Show the modal
        if (reportModal) reportModal.classList.remove('hidden');
    }

    function saveReport() {
        // Create form data from the report form
        const formData = new FormData(reportForm);

        // Add CSRF token as form field rather than header
        formData.append('_token', formCsrfToken);

        // Send data using fetch
        fetch('/request-reports', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => {
            // If status is 2xx, consider it a success regardless of content
            if (response.ok) {
                // Don't try to parse JSON, just return success status
                return { success: true };
            }

            // Only try to parse JSON for error messages
            if (response.headers.get('content-type')?.includes('application/json')) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Network response was not ok');
                });
            } else {
                throw new Error('Server returned an error');
            }
        })
        .then(data => {
            // Hide modal
            if (reportModal) reportModal.classList.add('hidden');

            // Reset UI
            resetTimerUI();

            // Show success message
            alert('Report byl úspěšně uložen!');

            // Reload page if on request detail to show the new report
            if (window.location.href.includes('/requests/')) {
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error saving report:', error);

            // Still check if data was saved despite error
            if (window.location.href.includes('/requests/')) {
                // Refresh the page anyway to see if the report was actually saved
                window.location.reload();
            } else {
                alert('Při ukládání reportu došlo k chybě: ' + error.message);
            }
        });
    }

    function fetchRequestName(requestId) {
        return fetch(`/api/requests/${requestId}/name`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .catch(error => {
                console.error('Error fetching request name:', error);
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
}
