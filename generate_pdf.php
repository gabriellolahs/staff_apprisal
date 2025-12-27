<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['staff'])) {
    exit("Access denied");
}

$staff_id = $_SESSION['staff'];
$category = $_SESSION['category'];

// Fetch all data
$stmt = $conn->prepare("
    SELECT s.*, b.* 
    FROM staff s 
    LEFT JOIN save_biodata b ON s.staff_id = b.staff_id 
    WHERE s.staff_id = ?
");
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) die("No data found");

// Passport
$photo = (!empty($data['passport']) && file_exists($data['passport'])) 
    ? $data['passport'] 
    : 'uploads/default.png';

$full_name = trim($data['surname'] . ' ' . $data['firstname'] . ' ' . ($data['lastname'] ?? ''));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biodata - <?= htmlspecialchars($staff_id) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background: linear-gradient(135deg, #001f3f, #003087);
            color: #333;
            padding: 15px;
            line-height: 1.6;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 25px 70px rgba(0,0,0,0.4);
            position: relative;
            padding-top: 120px; /* Space for logo + header */
        }

        /* LOGO - TOP LEFT CORNER */
        .logo-left {
            position: absolute;
            top: 60px;
            left: 25px;
            z-index: 1000;
            padding: 0px;
            transition: transform 0.3s ease;
        }
        .logo-left:hover { transform: scale(1.05); }
        .logo-left img {
            width: 130px;
            height: auto;
            display: block;
        }

        .header {
            background: linear-gradient(135deg, #003087, #0056b3);
            color: white;
            padding: 35px 20px;
            text-align: center;
           
            margin-top: -90px;
            position: relative;
            z-index: 1;
        }
        .header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        .header h2 {
            font-size: 22px;
            opacity: 0.95;
            font-weight: 400;
        }
        .header h3 {
            font-size: 28px;
            margin-top: 20px;
            font-weight: 700;
        }

        .content { padding: 35px; }

        .profile-header {
            display: flex;
            align-items: flex-start;
            gap: 30px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .photo-frame {
            text-align: center;
            flex: 0 0 180px;
        }
        .photo-frame img {
            width: 180px;
            height: 200px;
            object-fit: cover;
            border: 8px solid #003087;
            border-radius: 18px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.4);
        }
        .photo-frame p {
            margin-top: 12px;
            font-weight: bold;
            color: #003087;
            font-size: 19px;
            background: #f0f8ff;
            padding: 10px;
            border-radius: 10px;
        }
        .name-status {
            flex: 1;
            min-width: 300px;
        }
        .name {
            font-size: 32px;
            font-weight: 700;
            color: #003087;
            margin-bottom: 15px;
            font-family: 'Playfair Display', serif;
        }
        .status-badge {
            display: inline-block;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: bold;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .locked { background: #ffebee; color: #c62828; border: 2px solid #c62828; }
        .unlocked { background: #e8f5e8; color: #2e7d32; border: 2px solid #2e7d32; }

        .section { margin-bottom: 35px; }
        .section-title {
            background: linear-gradient(90deg, #003087, #0056b3);
            color: white;
            padding: 16px 25px;
            font-size: 22px;
            font-weight: 600;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 18px;
        }
        .info-item {
            background: #f8f9fa;
            border-left: 5px solid #003087;
            padding: 18px;
            border-radius: 0 12px 12px 0;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .info-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .info-label {
            font-weight: bold;
            color: #003087;
            font-size: 15px;
            margin-bottom: 6px;
            display: block;
        }
        .info-value {
            font-size: 16px;
            color: #333;
        }

        .footer {
            text-align: center;
            padding: 35px 20px;
            background: #003087;
            color: white;
            margin-top: 50px;
            border-radius: 0 0 18px 18px;
        }
        .footer p { margin: 8px 0; font-size: 15px; }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .container { padding-top: 100px; }
            .header { margin-top: -70px; }
            .logo-left { top: 15px; left: 15px; padding: 10px; }
            .logo-left img { width: 90px; }
            .profile-header { flex-direction: column; text-align: center; }
            .photo-frame img { width: 150px; height: 170px; }
            .name { font-size: 26px; }
            .info-grid { grid-template-columns: 1fr; }
        }

        /* PRINT: LOGO & HEADER ONLY ON FIRST PAGE */
        @media print {
            body { background: white; padding: 0; }
            .container { box-shadow: none; border-radius: 0; padding-top: 140px; }
            .header { margin-top: -120px; }
            .logo-left {
               
                top: 50px;
                left: 30px;
                padding: 0px !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .logo-left img { width: 130px !important; }
        }
    </style>
</head>
<body onload="window.print()">
 <div class="container">
     <!-- LOGO - TOP LEFT (Only appears on first page when printed) -->
    <div class="logo-left">
        <img src="20250406_220923_transcpr-removebg-preview.png" alt="Federal Polytechnic Ile-Oluji">
    </div>
        <div class="header">
            <h1> FEDERAL POLYTECHNIC, ILE-OLUJI</h1>
            <h2>Office of the Registrar</h2>
            <h3>STAFF BIODATA FORM</h3>
        </div>

        <div class="content">
            <div class="profile-header">
                <div class="photo-frame">
                    <img src="<?= htmlspecialchars($photo) ?>" alt="Passport">
                </div>
                <div class="name-status">
                    <div class="name"><?= strtoupper(htmlspecialchars($full_name)) ?></div>
                     <h2><?= htmlspecialchars($staff_id) ?></h2>
                    <h2><?= htmlspecialchars($category) ?></h2><br>
                    <div class="status-badge <?= !empty($data['is_locked']) ? 'locked' : 'unlocked' ?>">
                        <?= !empty($data['is_locked']) ? 'LOCKED' : 'UNLOCKED' ?> BIODATA
                    </div>
                </div>
            </div>

            <!-- PERSONAL INFORMATION -->
            <div class="section">
                <div class="section-title">PERSONAL INFORMATION</div>
                <div class="info-grid">
                    <div class="info-item"><span class="info-label">Title</span><div class="info-value"><?= htmlspecialchars($data['title'] ?? 'Not Set') ?></div></div>
                    <div class="info-item"><span class="info-label">Sex</span><div class="info-value"><?= ucfirst($data['sex']) ?></div></div>
                    <div class="info-item"><span class="info-label">Date of Birth</span><div class="info-value"><?= htmlspecialchars($data['dob'] ?? 'Not Set') ?></div></div>
                    <div class="info-item"><span class="info-label">Place of Birth</span><div class="info-value"><?= htmlspecialchars($data['pob'] ?? 'Not Set') ?></div></div>
                    <div class="info-item"><span class="info-label">Marital Status</span><div class="info-value"><?= htmlspecialchars($data['marital_status'] ?? 'Not Set') ?></div></div>
                    <div class="info-item"><span class="info-label">Blood Group</span><div class="info-value"><?= htmlspecialchars($data['blood_group'] ?? 'Not Set') ?></div></div>
                    <div class="info-item"><span class="info-label">Genotype</span><div class="info-value"><?= htmlspecialchars($data['genotype'] ?? 'Not Set') ?></div></div>
                    <div class="info-item"><span class="info-label">No. of Children</span><div class="info-value"><?= htmlspecialchars($data['children'] ?? '0') ?></div></div>
                    <div class="info-item"><span class="info-label">Spouse Name</span><div class="info-value"><?= htmlspecialchars($data['spouse_name'] ?? 'N/A') ?></div></div>
                </div>
            </div>

            <!-- CONTACT INFORMATION -->
            <div class="section">
                <div class="section-title">CONTACT INFORMATION</div>
                <div class="info-grid">
                    <div class="info-item"><span class="info-label">Permanent Address</span><div class="info-value"><?= htmlspecialchars($data['perm_address'] ?? 'Not Set') ?></div></div>
                    <div class="info-item"><span class="info-label">Contact Address</span><div class="info-value"><?= htmlspecialchars($data['contact_address'] ?? 'Not Set') ?></div></div>
                    <div class="info-item"><span class="info-label">Residential Address</span><div class="info-value"><?= htmlspecialchars($data['res_address'] ?? 'Not Set') ?></div></div>
                    <div class="info-item"><span class="info-label">Phone</span><div class="info-value"><?= htmlspecialchars($data['phone'] ?? 'Not Set') ?></div></div>
                    <div class="info-item"><span class="info-label">GSM</span><div class="info-value"><?= htmlspecialchars($data['gsm'] ?? 'Not Set') ?></div></div>
                    <div class="info-item"><span class="info-label">Email</span><div class="info-value"><?= htmlspecialchars($data['email'] ?? 'Not Set') ?></div></div>
                </div>
            </div>

            <!-- INSTITUTIONAL INFORMATION -->
            <div class="section">
                <div class="section-title">INSTITUTIONAL INFORMATION</div>
                <div class="info-grid">
                    <div class="info-item"><span class="info-label">School</span><div class="info-value"><?= htmlspecialchars($data['school'] ?? 'Not Assigned') ?></div></div>
                    <div class="info-item"><span class="info-label">Department</span><div class="info-value"><?= htmlspecialchars($data['department'] ?? 'Not Assigned') ?></div></div>
                    <div class="info-item"><span class="info-label">Designation</span><div class="info-value"><?= htmlspecialchars($data['designation'] ?? 'Not Set') ?></div></div>
                </div>
            </div>

            <!-- NEXT OF KIN -->
            <div class="section">
                <div class="section-title">NEXT OF KIN</div>
                <div class="info-grid">
                    <div class="info-item"><span class="info-label">Name</span><div class="info-value"><?= htmlspecialchars($data['nok_name'] ?? 'Not Set') ?></div></div>
                    <div class="info-item"><span class="info-label">Relationship</span><div class="info-value"><?= htmlspecialchars($data['nok_relation'] ?? 'Not Set') ?></div></div>
                    <div class="info-item"><span class="info-label">Phone</span><div class="info-value"><?= htmlspecialchars($data['nok_phone'] ?? 'Not Set') ?></div></div>
                    <div class="info-item"><span class="info-label">Address</span><div class="info-value"><?= htmlspecialchars($data['nok_address'] ?? 'Not Set') ?></div></div>
                </div>
            </div>

            <!-- OFFICIAL INFORMATION -->
            <div class="section">
                <div class="section-title">OFFICIAL INFORMATION</div>
                <div class="info-grid">
                    <div class="info-item"><span class="info-label">First Appt (Before FPI)</span><div class="info-value"><?= htmlspecialchars($data['first_place_before'] ?? 'N/A') ?> (<?= $data['first_date_before'] ?? 'N/A' ?>)</div></div>
                    <div class="info-item"><span class="info-label">First Appt in FPI</span><div class="info-value"><?= htmlspecialchars($data['first_place_fpi'] ?? 'N/A') ?></div></div>
                    <div class="info-item"><span class="info-label">Type of Appointment</span><div class="info-value"><?= htmlspecialchars($data['appt_type'] ?? 'Not Set') ?></div></div>
                    <div class="info-item"><span class="info-label">Post on Appt</span><div class="info-value"><?= htmlspecialchars($data['post_appt'] ?? 'Not Set') ?></div></div>
                    <div class="info-item"><span class="info-label">Office Held</span><div class="info-value"><?= htmlspecialchars($data['office_held'] ?? 'None') ?></div></div>
                    <div class="info-item"><span class="info-label">Present Appt Date</span><div class="info-value"><?= htmlspecialchars($data['present_appt'] ?? 'Not Set') ?></div></div>
                    <div class="info-item"><span class="info-label">Regularization Date</span><div class="info-value"><?= htmlspecialchars($data['regularization'] ?? 'Not Set') ?></div></div>
                    <div class="info-item"><span class="info-label">Grade Level / Step</span><div class="info-value"><?= htmlspecialchars($data['gl'] ?? '-') ?> / <?= htmlspecialchars($data['step'] ?? '-') ?></div></div>
                    <div class="info-item"><span class="info-label">Confirmation Date</span><div class="info-value"><?= htmlspecialchars($data['confirmation'] ?? 'Not Confirmed') ?></div></div>
                    <div class="info-item"><span class="info-label">First Appt (Public Service)</span><div class="info-value"><?= htmlspecialchars($data['first_appt_pub'] ?? 'Not Set') ?></div></div>
                    <div class="info-item"><span class="info-label">Accommodation</span><div class="info-value"><?= htmlspecialchars($data['accommodation'] ?? 'Not Provided') ?></div></div>
                </div>
            </div>

            <!-- ADDITIONAL INFORMATION -->
            <div class="section">
                <div class="section-title">ADDITIONAL INFORMATION</div>
                <div class="info-grid">
                    <div class="info-item"><span class="info-label">Academic Qualifications</span><div class="info-value"><?= htmlspecialchars($data['qualifications'] ?? 'Not Set') ?></div></div>
                    <div class="info-item"><span class="info-label">Union Membership</span><div class="info-value"><?= htmlspecialchars($data['union_name'] ?? 'Not Set') ?></div></div>
                    <div class="info-item"><span class="info-label">Area of Specialization</span><div class="info-value"><?= htmlspecialchars($data['specialization'] ?? 'Not Set') ?></div></div>
                    <div class="info-item"><span class="info-label">Pension Fund Administrator (PFA)</span><div class="info-value"><?= htmlspecialchars($data['pfa'] ?? 'Not Set') ?></div></div>
                    <div class="info-item"><span class="info-label">PFA PIN Code</span><div class="info-value"><?= htmlspecialchars($data['pin_code'] ?? 'Not Set') ?></div></div>
                </div>
            </div>

            <div class="footer">
                <p><strong>Document Generated:</strong> <?= date('l, j F Y \a\t g:i A') ?></p>
                <p>Federal Polytechnic Ile-Oluji • Human Resource Management System</p>
                <p>© 2025 All Rights Reserved • Powered by Thrive's Hub</p>
            </div>
        </div>
    </div>
</body>
</html>