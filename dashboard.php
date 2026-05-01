<?php
include 'includes/auth.php';
requireAuth();
include 'includes/db.php';
include 'includes/functions.php';

$user_id = getCurrentUserId();
$role = getCurrentUserRole();
$user_name = getCurrentUserName();
$user_email = getCurrentUserEmail();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Cyber Incident System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Cyber Incident</h2>
                <p>Ureen Jamil & Aiman</p>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item active">Dashboard</a>
                <?php if ($role == 'employee'): ?>
                <a href="#report" class="nav-item" onclick="scrollToSection('report')">Report Incident</a>
                <?php endif; ?>
                <a href="#incidents" class="nav-item" onclick="scrollToSection('incidents')">Incidents</a>
                <?php if ($role == 'admin'): ?>
                <a href="admin_panel.php" class="nav-item">Admin Panel</a>
                <?php endif; ?>
                <a href="logout.php" class="nav-item">Logout</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="top-bar">
                <div class="user-info">
                    <span>Welcome, <?php echo htmlspecialchars($user_name); ?></span>
                    <span class="role-badge <?php echo $role; ?>"><?php echo ucfirst($role); ?></span>
                </div>
                <div class="alert-badge" id="alertBadgeContainer">
                    <span>Alerts</span>
                    <span class="badge-count" id="alertCount">0</span>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid" id="statsContainer">
                <div class="stat-card">Loading stats...</div>
            </div>

            <!-- Report Incident Section (Employee only) -->
            <?php if ($role == 'employee'): ?>
            <section id="report" class="card">
                <h2>Report New Incident</h2>
                <form id="incidentForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="employee_name" value="<?php echo htmlspecialchars($user_name); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="employee_email" value="<?php echo htmlspecialchars($user_email); ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Department</label>
                        <input type="text" name="department" placeholder="Your department" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" id="description" rows="4" placeholder="Describe the incident in detail..." required></textarea>
                        <small id="aiHint" class="ai-hint"></small>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Incident Type</label>
                            <select name="incident_type" id="incident_type" required>
                                <option value="">Select Type</option>
                                <option>Phishing</option>
                                <option>Malware</option>
                                <option>Unauthorized Access</option>
                                <option>Data Breach</option>
                                <option>Suspicious Login</option>
                                <option>DoS Attack</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Severity</label>
                            <select name="severity" required>
                                <option value="">Select Severity</option>
                                <option>Low</option>
                                <option>Medium</option>
                                <option>High</option>
                                <option>Critical</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Report</button>
                </form>
                <div id="reportResponse"></div>
            </section>
            <?php endif; ?>

            <!-- Security: Broadcast Alert Section -->
            <?php if ($role == 'security'): ?>
            <section id="broadcast" class="card">
                <h2>Broadcast Threat Alert</h2>
                <div class="form-group">
                    <label>Alert Message</label>
                    <textarea id="alertMessage" rows="3" placeholder="Enter alert message for all employees..."></textarea>
                </div>
                <div class="form-group">
                    <label>Severity</label>
                    <select id="alertSeverity">
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                        <option value="Critical">Critical</option>
                    </select>
                </div>
                <button onclick="broadcastAlert()" class="btn btn-danger">Broadcast Alert</button>
                <div id="broadcastResponse"></div>
            </section>
            <?php endif; ?>

            <!-- Incidents List Section -->
            <section id="incidents" class="card">
                <h2>Incidents</h2>
                <div class="filter-bar">
                    <input type="text" id="searchInput" placeholder="Search incidents..." class="search-input">
                    <select id="statusFilter">
                        <option value="">All Status</option>
                        <option value="Reported">Reported</option>
                        <option value="Under Review">Under Review</option>
                        <option value="Investigating">Investigating</option>
                        <option value="Resolved">Resolved</option>
                    </select>
                    <select id="severityFilter">
                        <option value="">All Severity</option>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                        <option value="Critical">Critical</option>
                    </select>
                    <button onclick="loadIncidents()" class="btn btn-secondary">Refresh</button>
                </div>
                <div id="incidentsList" class="table-container">
                    <div class="loading">Loading incidents...</div>
                </div>
            </section>

            <!-- Live Alerts Section -->
            <section class="card">
                <h2>Live Threat Alerts</h2>
                <div id="alertsList" class="alerts-container">
                    <div class="loading">Loading alerts...</div>
                </div>
            </section>

            <div class="footer-credit">
                <p>Cyber Incident Reporting System | Developed by Ureen Jamil & Aiman | WEB ENGINEERING (COMP-351) CCP | Fall 2025</p>
            </div>
        </main>
    </div>

    <script src="assets/js/app.js"></script>
    <script>
        // Initialize all functions on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadStats();
            loadIncidents();
            loadAlerts();
            startAlertPolling();
            
            // AI suggestion on description input
            const descInput = document.getElementById('description');
            if (descInput) {
                let debounceTimer;
                descInput.addEventListener('keyup', function() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function() {
                        const description = descInput.value;
                        if (description.length > 20) {
                            fetch('api/ai_suggest.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ description: description })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.suggested_type) {
                                    document.getElementById('aiHint').innerHTML = 'AI Suggestion: ' + data.suggested_type;
                                    if (data.confidence === 'high') {
                                        document.getElementById('incident_type').value = data.suggested_type;
                                    }
                                }
                            });
                        }
                    }, 500);
                });
            }
            
            // Search and filter events
            document.getElementById('searchInput')?.addEventListener('keyup', () => loadIncidents());
            document.getElementById('statusFilter')?.addEventListener('change', () => loadIncidents());
            document.getElementById('severityFilter')?.addEventListener('change', () => loadIncidents());
        });
        
        function scrollToSection(id) {
            document.getElementById(id).scrollIntoView({ behavior: 'smooth' });
        }
        
        function loadStats() {
            fetch('api/fetch_stats.php')
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        document.getElementById('statsContainer').innerHTML = `
                            <div class="stat-card"><h3>Total Incidents</h3><p class="stat-number">${data.total}</p></div>
                            <div class="stat-card"><h3>Under Review</h3><p class="stat-number">${data.reported}</p></div>
                            <div class="stat-card"><h3>Investigating</h3><p class="stat-number">${data.investigating}</p></div>
                            <div class="stat-card"><h3>Resolved</h3><p class="stat-number">${data.resolved}</p></div>
                        `;
                    }
                });
        }
        
        function loadIncidents() {
            const search = document.getElementById('searchInput')?.value || '';
            const status = document.getElementById('statusFilter')?.value || '';
            const severity = document.getElementById('severityFilter')?.value || '';
            
            fetch(`api/fetch_incidents.php?search=${encodeURIComponent(search)}&status=${status}&severity=${severity}`)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success' && data.incidents) {
                        let html = '<table class="data-table"><thead><tr><th>ID</th><th>Type</th><th>Description</th><th>Severity</th><th>Status</th><th>Reported</th><?php echo $role == 'security' ? '<th>Action</th>' : ''; ?></tr></thead><tbody>';
                        data.incidents.forEach(inc => {
                            html += `<tr>
                                <td>${inc.incident_code}</td>
                                <td>${inc.incident_type}</td>
                                <td>${inc.description.substring(0, 50)}...</td>
                                <td><span class="badge badge-${inc.severity.toLowerCase()}">${inc.severity}</span></td>
                                <td><span class="badge badge-${inc.status.toLowerCase().replace(' ', '-')}">${inc.status}</span></td>
                                <td>${inc.created_at}</td>`;
                            <?php if ($role == 'security'): ?>
                            html += `<td>
                                <select onchange="updateStatus(${inc.id}, this.value)" class="status-select">
                                    <option ${inc.status === 'Reported' ? 'selected' : ''}>Reported</option>
                                    <option ${inc.status === 'Under Review' ? 'selected' : ''}>Under Review</option>
                                    <option ${inc.status === 'Investigating' ? 'selected' : ''}>Investigating</option>
                                    <option ${inc.status === 'Resolved' ? 'selected' : ''}>Resolved</option>
                                    <option ${inc.status === 'False Alarm' ? 'selected' : ''}>False Alarm</option>
                                </select>
                            </td>`;
                            <?php endif; ?>
                            html += `</tr>`;
                        });
                        html += '</tbody></table>';
                        document.getElementById('incidentsList').innerHTML = html;
                    } else {
                        document.getElementById('incidentsList').innerHTML = '<p class="no-data">No incidents found.</p>';
                    }
                });
        }
        
        function loadAlerts() {
            fetch('api/fetch_alerts.php')
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success' && data.alerts) {
                        let html = '';
                        data.alerts.forEach(alert => {
                            html += `<div class="alert-card alert-${alert.severity.toLowerCase()}">
                                <strong>[${alert.severity}]</strong> ${alert.message}
                                <small>${alert.created_at}</small>
                            </div>`;
                        });
                        document.getElementById('alertsList').innerHTML = html || '<p>No new alerts.</p>';
                        document.getElementById('alertCount').innerText = data.unread_count || 0;
                    }
                });
        }
        
        function startAlertPolling() {
            setInterval(loadAlerts, 5000);
        }
        
        function updateStatus(incidentId, status) {
            fetch('api/update_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ incident_id: incidentId, status: status })
            }).then(() => loadIncidents());
        }
        
        function broadcastAlert() {
            const message = document.getElementById('alertMessage').value;
            const severity = document.getElementById('alertSeverity').value;
            if (!message) { alert('Please enter alert message'); return; }
            
            fetch('api/broadcast_alert.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: message, severity: severity })
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById('broadcastResponse').innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                document.getElementById('alertMessage').value = '';
                setTimeout(() => document.getElementById('broadcastResponse').innerHTML = '', 3000);
            });
        }
        
        document.getElementById('incidentForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('api/report_incident.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById('reportResponse').innerHTML = `<div class="alert alert-${data.status}">${data.message}</div>`;
                if (data.status === 'success') {
                    this.reset();
                    loadIncidents();
                }
                setTimeout(() => document.getElementById('reportResponse').innerHTML = '', 5000);
            });
        });
        
        <?php if ($role == 'security'): ?>
        window.updateStatus = updateStatus;
        window.broadcastAlert = broadcastAlert;
        <?php endif; ?>
    </script>
</body>
</html>