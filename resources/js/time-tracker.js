// resources/js/time-tracker.js

import TimeTracker from './components/TimeTracker';

document.addEventListener('DOMContentLoaded', function() {
    initTimeTracker();
});

function initTimeTracker() {
    const trackerComponent = document.getElementById('time-tracker-component');
    if (!trackerComponent) return;

    const requestId = trackerComponent.dataset.requestId;
    const requestName = trackerComponent.dataset.requestName;

    const timerElement = document.getElementById('timeTracker_timer');
    const startBtn = document.getElementById('timeTracker_startBtn');
    const stopBtn = document.getElementById('timeTracker_stopBtn');
    const cancelBtn = document.getElementById('timeTracker_cancelBtn');
    const activeControls = document.getElementById('timeTracker_activeControls');
    const activeSession = document.getElementById('timeTracker_activeSession');
    const requestInfo = document.getElementById('timeTracker_requestInfo');
    const requestNameElement = document.getElementById('timeTracker_requestName');

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

    // Summary elements
    const summaryStart = document.getElementById('timeTracker_summary_start');
    const summaryEnd = document.getElementById('timeTracker_summary_end');
    const summaryTotal = document.getElementById('timeTracker_summary_total');
    const summaryMinutes = document.getElementById('timeTracker_summary_minutes');

    // Update callback for the timer
    TimeTracker.onUpdate(function(formattedTime) {
        timerElement.textContent = formattedTime;
    });

    // Check for existing session
    const activeSessionData = TimeTracker.checkForActiveSession();
    if (activeSessionData) {
        // We have an active session
        activeControls.classList.remove('hidden');
        startBtn.classList.add('hidden');
        activeSession.classList.remove('hidden');
        requestInfo.classList.remove('hidden');

        // Get the active request name using AJAX
        fetchRequestName(activeSessionData.requestId).then(name => {
            requestNameElement.textContent = name || `Požadavek #${activeSessionData.requestId}`;
        });

        // Update the timer
        timerElement.textContent = activeSessionData.elapsedTime;
    } else if (requestId) {
        // On a specific request page, pre-fill request info
        requestNameElement.textContent = requestName || `Požadavek #${requestId}`;
    }

    // Start timer button
    startBtn.addEventListener('click', function() {
        // If we're on a specific request page, use that request ID
        const activeRequestId = requestId || prompt('Zadejte ID požadavku, pro který chcete měřit čas:');

        if (activeRequestId) {
            TimeTracker.start(activeRequestId);

            activeControls.classList.remove('hidden');
            startBtn.classList.add('hidden');
            activeSession.classList.remove('hidden');
            requestInfo.classList.remove('hidden');

            // If we're not on a specific request page, get the request name
            if (!requestId) {
                fetchRequestName(activeRequestId).then(name => {
                    requestNameElement.textContent = name || `Požadavek #${activeRequestId}`;
                });
            } else {
                requestNameElement.textContent = requestName || `Požadavek #${requestId}`;
            }
        }
    });

    // Stop timer button
    stopBtn.addEventListener('click', function() {
        const result = TimeTracker.stop();
        if (result) {
            showReportModal(result);
        }

        resetTimerUI();
    });

    // Cancel timer button
    cancelBtn.addEventListener('click', function() {
        if (confirm('Opravdu chcete zrušit měření času? Data budou ztracena.')) {
            TimeTracker.cancel();
            resetTimerUI();
        }
    });

    // Save report button
    saveReportBtn.addEventListener('click', function() {
        if (reportForm.checkValidity()) {
            saveReport();
        } else {
            reportForm.reportValidity();
        }
    });

    // Cancel report button
    cancelReportBtn.addEventListener('click', function() {
        reportModal.classList.add('hidden');
    });

    function resetTimerUI() {
        activeControls.classList.add('hidden');
        startBtn.classList.remove('hidden');
        activeSession.classList.add('hidden');
        timerElement.textContent = '00:00:00';

        // Only hide request info if we're not on a specific request page
        if (!requestId) {
            requestInfo.classList.add('hidden');
        }
    }

    function showReportModal(result) {
        // Fill in the form values
        formRequestId.value = result.requestId;
        formWorkStart.value = result.startTime.toISOString();
        formWorkEnd.value = result.endTime.toISOString();

        // Calculate minutes from seconds and round up
        const totalMinutes = Math.ceil(result.totalSeconds / 60);
        formWorkTotal.value = totalMinutes;

        // Update summary
        summaryStart.textContent = formatDateTime(result.startTime);
        summaryEnd.textContent = formatDateTime(result.endTime);
        summaryTotal.textContent = formatDuration(result.totalSeconds);
        summaryMinutes.textContent = totalMinutes + ' min';

        // Show the modal
        reportModal.classList.remove('hidden');
    }

    function saveReport() {
        // Create form data
        const formData = new FormData(reportForm);

        // Add additional fields
        formData.append('state', 1); // Active

        // Send data using fetch
        fetch('/request-reports', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Hide modal
            reportModal.classList.add('hidden');

            // Show success message
            alert('Report byl úspěšně uložen!');

            // Reload page if on request detail
            if (window.location.href.includes('/requests/')) {
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error saving report:', error);
            alert('Při ukládání reportu došlo k chybě. Zkuste to prosím znovu.');
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
            .then(data => {
                return data.name;
            })
            .catch(error => {
                console.error('Error fetching request name:', error);
                return null;
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
