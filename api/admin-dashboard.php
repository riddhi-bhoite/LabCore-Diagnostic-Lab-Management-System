<?php
// api/admin-dashboard.php
require_once __DIR__.'/../includes/helpers.php';
require_once __DIR__.'/../includes/db.php';
if(!isset($_SESSION['person_id'])||$_SESSION['role']!=='Admin') respond(false,'Unauthorized.');
$db=getDB();

$stats=[];
$stats['total_bookings']  = $db->query("SELECT COUNT(*) FROM Booking")->fetchColumn();
$stats['today_bookings']  = $db->query("SELECT COUNT(*) FROM Booking WHERE Booking_Date=CURDATE()")->fetchColumn();
$stats['revenue']         = $db->query("SELECT COALESCE(SUM(Amount_Paid),0) FROM Payment")->fetchColumn();
$stats['pending_reports'] = $db->query("SELECT COUNT(*) FROM Test_Report WHERE Report_Status='Ready'")->fetchColumn();
$stats['active_patients'] = $db->query("SELECT COUNT(*) FROM Patient")->fetchColumn();

$stmt=$db->query("
    SELECT
        b.Booking_ID        AS booking_id,
        b.Booking_Date      AS booking_date,
        b.Booking_Status    AS booking_status,
        b.Payment_Status    AS payment_status,
        CONCAT(pp.First_Name,' ',pp.Last_Name) AS patient_name,
        CONCAT(dp.First_Name,' ',dp.Last_Name) AS doctor_name
    FROM Booking b
    JOIN Patient pt  ON b.Patient_ID=pt.Patient_ID
    JOIN Person pp   ON pt.Person_ID=pp.Person_ID
    JOIN Doctor d    ON b.Doctor_ID=d.Doctor_ID
    JOIN Person dp   ON d.Person_ID=dp.Person_ID
    ORDER BY b.Booking_ID DESC LIMIT 10
");
$bookings=$stmt->fetchAll();

respond(true,'OK',['stats'=>$stats,'bookings'=>$bookings]);