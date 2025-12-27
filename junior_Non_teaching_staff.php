<?php

<!-- admin_dashboard.html -->
<div class="dashboard">
    <div class="stats">
        <div class="stat-card">
            <h3>Total Staff</h3>
            <span id="total-staff">0</span>
        </div>
        <div class="stat-card">
            <h3>Pending Bio-data</h3>
            <span id="pending-biodata">0</span>
        </div>
        <div class="stat-card">
            <h3>Pending Appraisals</h3>
            <span id="pending-appraisals">0</span>
        </div>
        <div class="stat-card">
            <h3>Approved Records</h3>
            <span id="approved-records">0</span>
        </div>
    </div>
    <div class="management">
        <button onclick="viewUsers()">Manage Users</button>
        <button onclick="viewSubmissions()">View Submissions</button>
        <button onclick="systemSettings()">System Settings</button>
    </div>
</div>



