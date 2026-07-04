<?php
// api/admin-approve-report.php
require_once __DIR__.'/../includes/helpers.php'; require_once __DIR__.'/../includes/db.php';
if(!isset($_SESSION['person_id'])||$_SESSION['role']!=='Admin') respond(false,'Unauthorized.');
if($_SERVER['REQUEST_METHOD']!=='POST') respond(false,'Method not allowed.');
$b=getBody(); $rid=intval($b['report_id']??0); if(!$rid) respond(false,'Invalid report ID.');
// Admin approves without needing a doctor_id — set Approved_By = NULL or leave as is
getDB()->prepare("UPDATE Test_Report SET Report_Status='Approved' WHERE Report_ID=? AND Report_Status='Ready'")->execute([$rid]);
respond(true,'Report approved.');