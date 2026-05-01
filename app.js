console.log("Cyber Incident Reporting System Loaded");
console.log("Developed by: Ureen Jamil & Aiman");
console.log("Semester Project - Fall 2025");

// Global functions that might be called from dashboard.php

function loadIncidents() {
    const search = document.getElementById('searchInput')?.value || '';
    const status = document.getElementById('statusFilter')?.value || '';
    const severity = document.getElementById('severityFilter')?.value || '';
    
    fetch(`api/fetch_incidents.php?search=${encodeURIComponent(search)}&status=${status}&severity=${severity}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('incidentsList');
            if (!container) return;
            
            if (data.status === 'success' && data.incidents && data.incidents.length > 0) {
                let html = '<table class="data-table"><thead><tr><th>ID</th><th>Type</th><th>Description</th><th>Severity</th><th>Status</th><th>Date</th></tr></thead><tbody>';
                data.incidents.forEach(incident => {
                    html += `<tr>
                        <td>${incident.incident_code}</td>
                        <td>${incident.incident_type}</td>
                        <td>${incident.description.substring(0, 60)}${incident.description.length > 60 ? '...' : ''}</td>
                        <td><span class="badge badge-${incident.severity.toLowerCase()}">${incident.severity}</span></td>
                        <td><span class="badge badge-${incident.status.toLowerCase().replace(' ', '-')}">${incident.status}</span></td>
                        <td>${incident.created_at}</td>
                    </tr>`;
                });
                html += '</tbody></table>';
                container.innerHTML = html;
            } else {
                container.innerHTML = '<p class="no-data">No incidents found.</p>';
            }
        })
        .catch(error => {
            console.error('Error loading incidents:', error);
            if (document.getElementById('incidentsList')) {
                document.getElementById('incidentsList').innerHTML = '<p class="loading">Error loading incidents. Please refresh.</p>';
            }
        });
}

function loadAlerts() {
    fetch('api/fetch_alerts.php')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('alertsList');
            const badge = document.getElementById('alertCount');
            
            if (!container) return;
            
            if (data.status === 'success' && data.alerts && data.alerts.length > 0) {
                let html = '';
                data.alerts.forEach(alert => {
                    html += `<div class="alert-card alert-${alert.severity.toLowerCase()}">
                        <strong>${alert.severity.toUpperCase()}:</strong> ${alert.message}
                        <small>${alert.created_at}</small>
                    </div>`;
                });
                container.innerHTML = html;
                if (badge) badge.innerText = data.alerts.length;
            } else {
                container.innerHTML = '<p class="no-data">No new alerts. System secure.</p>';
                if (badge) badge.innerText = '0';
            }
        })
        .catch(error => {
            console.error('Error loading alerts:', error);
        });
}

function startAlertPolling() {
    // Fetch alerts every 10 seconds
    setInterval(loadAlerts, 10000);
}

// Handle incident form submission if it exists
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('incidentForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('api/report_incident.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const responseDiv = document.getElementById('reportResponse');
                if (responseDiv) {
                    responseDiv.innerHTML = `<div class="alert alert-${data.status}">${data.message}</div>`;
                    if (data.status === 'success') {
                        form.reset();
                        loadIncidents();
                    }
                    setTimeout(() => {
                        responseDiv.innerHTML = '';
                    }, 5000);
                }
            })
            .catch(error => {
                console.error('Error submitting incident:', error);
                const responseDiv = document.getElementById('reportResponse');
                if (responseDiv) {
                    responseDiv.innerHTML = '<div class="alert alert-error">Server error. Please try again.</div>';
                }
            });
        });
    }
    
    // Load initial data
    if (document.getElementById('incidentsList')) {
        loadIncidents();
    }
    if (document.getElementById('alertsList')) {
        loadAlerts();
        startAlertPolling();
    }
});