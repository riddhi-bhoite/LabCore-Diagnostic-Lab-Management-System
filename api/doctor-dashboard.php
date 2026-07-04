<?php
// api/doctor-dashboard.php
require_once __DIR__.'/../includes/helpers.php';
require_once __DIR__.'/../includes/db.php';
if(!isset($_SESSION['person_id'])||$_SESSION['role']!=='Doctor') respond(false,'Unauthorized.');
$db=getDB();
$stmt=$db->prepare("SELECT Doctor_ID FROM Doctor WHERE Person_ID=?");
$stmt->execute([$_SESSION['person_id']]);
$doc=$stmt->fetch();
if(!$doc) respond(false,'Doctor not found.');
$did=$doc['Doctor_ID'];

// Stats
$stats=[];
$s=$db->prepare("SELECT COUNT(*) FROM Booking WHERE Doctor_ID=?"); $s->execute([$did]); $stats['total']=$s->fetchColumn();
$s=$db->prepare("SELECT COUNT(*) FROM Booking WHERE Doctor_ID=? AND Booking_Date=CURDATE()"); $s->execute([$did]); $stats['today']=$s->fetchColumn();
$s=$db->prepare("SELECT COUNT(*) FROM Booking WHERE Doctor_ID=? AND Booking_Status='Completed' AND MONTH(Booking_Date)=MONTH(CURDATE())"); $s->execute([$did]); $stats['completed']=$s->fetchColumn();
$s=$db->prepare("SELECT COUNT(*) FROM Test_Report tr JOIN Booking b ON tr.Booking_ID=b.Booking_ID WHERE b.Doctor_ID=? AND tr.Report_Status='Ready'"); $s->execute([$did]); $stats['pending_approval']=$s->fetchColumn();

// Bookings
$stmt=$db->prepare("
    SELECT
        b.Booking_ID        AS booking_id,
        b.Booking_Date      AS booking_date,
        b.Booking_Status    AS booking_status,
        CONCAT(p.First_Name,' ',p.Last_Name) AS patient_name,
        (SELECT COUNT(*) FROM Booking_Test WHERE Booking_ID=b.Booking_ID) AS test_count
    FROM Booking b
    JOIN Patient pt ON b.Patient_ID=pt.Patient_ID
    JOIN Person p   ON pt.Person_ID=p.Person_ID
    WHERE b.Doctor_ID=?
    ORDER BY b.Booking_Date DESC LIMIT 20
");
$stmt->execute([$did]);
$bookings=$stmt->fetchAll();

// Reports pending approval
$stmt=$db->prepare("
    SELECT
        tr.Report_ID        AS report_id,
        tr.Booking_ID       AS booking_id,
        tr.Generated_Date   AS generated_date,
        tr.Report_Status    AS report_status,
        CONCAT(p.First_Name,' ',p.Last_Name) AS patient_name,
        (SELECT COUNT(*) FROM Report_Details WHERE Report_ID=tr.Report_ID) AS test_count
    FROM Test_Report tr
    JOIN Booking b  ON tr.Booking_ID=b.Booking_ID
    JOIN Patient pt ON b.Patient_ID=pt.Patient_ID
    JOIN Person p   ON pt.Person_ID=p.Person_ID
    WHERE b.Doctor_ID=? AND tr.Report_Status='Ready'
    ORDER BY tr.Report_ID DESC
");
$stmt->execute([$did]);
$reports=$stmt->fetchAll();

respond(true,'OK',['stats'=>$stats,'bookings'=>$bookings,'reports'=>$reports]);