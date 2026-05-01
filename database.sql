
DROP DATABASE IF EXISTS cyber_incident_db;
CREATE DATABASE cyber_incident_db;
USE cyber_incident_db;

-- =============================================
-- TABLE 1: USERS (Role-based authentication)
-- =============================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('employee', 'security', 'admin') DEFAULT 'employee',
    department VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- TABLE 2: INCIDENTS (Main reporting table)
-- =============================================
CREATE TABLE incidents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    incident_code VARCHAR(20) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    employee_name VARCHAR(100) NOT NULL,
    employee_email VARCHAR(120) NOT NULL,
    department VARCHAR(100) NOT NULL,
    incident_type VARCHAR(100) NOT NULL,
    severity ENUM('Low', 'Medium', 'High', 'Critical') DEFAULT 'Medium',
    description TEXT NOT NULL,
    status ENUM('Reported', 'Under Review', 'Investigating', 'Resolved', 'False Alarm') DEFAULT 'Reported',
    ai_suggested_type VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- TABLE 3: ALERTS (Live threat alerts)
-- =============================================
CREATE TABLE alerts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    message TEXT NOT NULL,
    severity ENUM('Low', 'Medium', 'High', 'Critical') DEFAULT 'Medium',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- =============================================
-- TABLE 4: ALERT_READS (Track who read which alert)
-- =============================================
CREATE TABLE alert_reads (
    alert_id INT NOT NULL,
    user_id INT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    PRIMARY KEY (alert_id, user_id),
    FOREIGN KEY (alert_id) REFERENCES alerts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- TABLE 5: SYSTEM_LOGS (Audit trail)
-- =============================================
CREATE TABLE system_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    user_email VARCHAR(100),
    action VARCHAR(100),
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- FAKE DATA FOR DEMO (30+ records)
-- =============================================

-- Users
INSERT INTO users (full_name, email, password, role, department) VALUES
('Ureen Jamil', 'ureen@example.com', MD5('1234'), 'employee', 'IT Security'),
('Aiman', 'aiman@example.com', MD5('1234'), 'employee', 'Network Security'),
('Ahmed Raza', 'ahmed@example.com', MD5('1234'), 'employee', 'Finance'),
('Fatima Khan', 'fatima@example.com', MD5('1234'), 'employee', 'HR'),
('Security Team Lead', 'security@example.com', MD5('1234'), 'security', 'Security Operations'),
('System Admin', 'admin@example.com', MD5('1234'), 'admin', 'IT Management');

-- Incidents (25+ records)
INSERT INTO incidents (incident_code, user_id, employee_name, employee_email, department, incident_type, severity, description, status) VALUES
('INC-001', 1, 'Ureen Jamil', 'ureen@example.com', 'IT Security', 'Phishing', 'High', 'Received suspicious email from unknown sender asking for password reset. Email appeared to be from IT department but sender domain was fake.', 'Investigating'),
('INC-002', 1, 'Ureen Jamil', 'ureen@example.com', 'IT Security', 'Malware', 'Critical', 'Computer screen showed ransomware message demanding Bitcoin payment. All files appear to be encrypted.', 'Under Review'),
('INC-003', 2, 'Aiman', 'aiman@example.com', 'Network Security', 'Unauthorized Access', 'High', 'Multiple failed login attempts detected on firewall management console from unknown IP address.', 'Reported'),
('INC-004', 2, 'Aiman', 'aiman@example.com', 'Network Security', 'Data Breach', 'Critical', 'Sensitive network configuration files were accessed outside business hours.', 'Investigating'),
('INC-005', 3, 'Ahmed Raza', 'ahmed@example.com', 'Finance', 'Phishing', 'Medium', 'Fake invoice email received from spoofed vendor address asking for payment.', 'Resolved'),
('INC-006', 3, 'Ahmed Raza', 'ahmed@example.com', 'Finance', 'Suspicious Login', 'High', 'Unrecognized device logged into banking portal at midnight.', 'Under Review'),
('INC-007', 4, 'Fatima Khan', 'fatima@example.com', 'HR', 'Phishing', 'High', 'Employees received fake HR benefits email asking for SSN information.', 'Investigating'),
('INC-008', 4, 'Fatima Khan', 'fatima@example.com', 'HR', 'Malware', 'Medium', 'HR computer showing pop-ups and running slow after opening attachment.', 'Reported'),
('INC-009', 1, 'Ureen Jamil', 'ureen@example.com', 'IT Security', 'Unauthorized Access', 'Critical', 'Unknown device connected to internal network. MAC address not in whitelist.', 'Investigating'),
('INC-010', 2, 'Aiman', 'aiman@example.com', 'Network Security', 'DoS Attack', 'High', 'Network traffic spike detected. Possible denial of service attack.', 'Under Review');

-- More incidents (total 20+)
INSERT INTO incidents (incident_code, user_id, employee_name, employee_email, department, incident_type, severity, description, status) VALUES
('INC-011', 1, 'Ureen Jamil', 'ureen@example.com', 'IT Security', 'Phishing', 'Critical', 'CEO impersonation email sent to finance department requesting urgent wire transfer.', 'Investigating'),
('INC-012', 2, 'Aiman', 'aiman@example.com', 'Network Security', 'Malware', 'High', 'Antivirus detected Trojan in email attachment. File quarantined.', 'Resolved'),
('INC-013', 3, 'Ahmed Raza', 'ahmed@example.com', 'Finance', 'Unauthorized Access', 'High', 'Former employee account still active and showing login attempts.', 'Under Review'),
('INC-014', 4, 'Fatima Khan', 'fatima@example.com', 'HR', 'Data Leak', 'Critical', 'Employee personal data found on public cloud storage.', 'Investigating'),
('INC-015', 1, 'Ureen Jamil', 'ureen@example.com', 'IT Security', 'Suspicious Login', 'Medium', 'Login from unusual geographic location detected on admin account.', 'Reported');

-- Alerts (15+ live alerts)
INSERT INTO alerts (message, severity, created_by) VALUES
('CRITICAL: Active phishing campaign targeting all employees. Do not click any links in emails asking for password reset.', 'Critical', 5),
('HIGH: Ransomware outbreak detected on network segment 10.0.2.x. Disconnect immediately if affected.', 'High', 5),
('MEDIUM: Scheduled security patch deployment tonight from 10 PM to 2 AM. Systems may be unavailable.', 'Medium', 5),
('CRITICAL: Zero-day vulnerability detected in company VPN. Update your VPN client immediately.', 'Critical', 5),
('HIGH: Multiple brute force attacks detected on SSH gateways. Security team investigating.', 'High', 5);

-- Alert reads (for all employees)
INSERT INTO alert_reads (alert_id, user_id, is_read)
SELECT a.id, u.id, FALSE FROM alerts a CROSS JOIN users u WHERE u.role = 'employee';

-- System logs
INSERT INTO system_logs (user_id, user_email, action, details, ip_address) VALUES
(1, 'ureen@example.com', 'LOGIN', 'Ureen Jamil logged into the system', '192.168.1.101'),
(1, 'ureen@example.com', 'REPORT_INCIDENT', 'Ureen Jamil reported INC-001 (Phishing incident)', '192.168.1.101'),
(2, 'aiman@example.com', 'LOGIN', 'Aiman logged into the system', '192.168.1.102'),
(2, 'aiman@example.com', 'REPORT_INCIDENT', 'Aiman reported INC-003 (Unauthorized access)', '192.168.1.102'),
(5, 'security@example.com', 'LOGIN', 'Security team logged in', '192.168.1.200'),
(5, 'security@example.com', 'BROADCAST_ALERT', 'Security team broadcast critical phishing alert', '192.168.1.200'),
(5, 'security@example.com', 'UPDATE_STATUS', 'Changed INC-001 status to Investigating', '192.168.1.200'),
(6, 'admin@example.com', 'LOGIN', 'Admin logged in', '192.168.1.250');