<?php
// api/patient-dashboard.php
require_once __DIR__.'/../includes/helpers.php';
require_once __DIR__.'/../includes/db.php';
if(!isset($_SESSION['person_id'])||$_SESSION['role']!=='Patient') respond(false,'Unauthorized.');
$db=getDB();
$stmt=$db->prepare("SELECT Patient_ID FROM Patient WHERE Person_ID=?");
$stmt->execute([$_SESSION['person_id']]); $pat=$stmt->fetch();
if(!$pat) respond(false,'Not found.');
$pid=$pat['Patient_ID'];

$stats=[];
$s=$db->prepare("SELECT COUNT(*) FROM Booking WHERE Patient_ID=?"); $s->execute([$pid]); $stats['total_bookings']=$s->fetchColumn();
$s=$db->prepare("SELECT COUNT(*) FROM Booking WHERE Patient_ID=? AND Booking_Status='Pending'"); $s->execute([$pid]); $stats['pending']=$s->fetchColumn();
$s=$db->prepare("SELECT COUNT(*) FROM Test_Report tr JOIN Booking b ON tr.Booking_ID=b.Booking_ID WHERE b.Patient_ID=? AND tr.Report_Status='Approved'"); $s->execute([$pid]); $stats['reports']=$s->fetchColumn();
$s=$db->prepare("SELECT COUNT(*) FROM Booking WHERE Patient_ID=? AND Payment_Status='Unpaid' AND Booking_Status='Completed'"); $s->execute([$pid]); $stats['unpaid']=$s->fetchColumn();

$stmt=$db->prepare("
    SELECT
        b.Booking_ID        AS booking_id,
        b.Booking_Date      AS booking_date,
        b.Booking_Status    AS booking_status,
        b.Payment_Status    AS payment_status,
        b.Total_Amount      AS total_amount,
        CONCAT(p.First_Name,' ',p.Last_Name) AS doctor_name
    FROM Booking b
    JOIN Doctor doc ON b.Doctor_ID=doc.Doctor_ID
    JOIN Person p   ON doc.Person_ID=p.Person_ID
    WHERE b.Patient_ID=?
    ORDER BY b.Booking_Date DESC LIMIT 5
");
$stmt->execute([$pid]); $bookings=$stmt->fetchAll();

$stmt=$db->prepare("
    SELECT
        tr.Report_ID        AS report_id,
        tr.Generated_Date   AS generated_date,
        tr.Report_Status    AS report_status,
        CONCAT(p.First_Name,' ',p.Last_Name) AS doctor_name
    FROM Test_Report tr
    JOIN Booking b  ON tr.Booking_ID=b.Booking_ID
    JOIN Doctor doc ON b.Doctor_ID=doc.Doctor_ID
    JOIN Person p   ON doc.Person_ID=p.Person_ID
    WHERE b.Patient_ID=?
    ORDER BY tr.Generated_Date DESC LIMIT 5
");
$stmt->execute([$pid]); $reports=$stmt->fetchAll();

respond(true,'OK',['stats'=>$stats,'bookings'=>$bookings,'reports'=>$reports]);