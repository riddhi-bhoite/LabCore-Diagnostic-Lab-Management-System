<?php
// api/lab-dashboard.php
require_once __DIR__.'/../includes/helpers.php';
require_once __DIR__.'/../includes/db.php';
if(!isset($_SESSION['person_id'])||$_SESSION['role']!=='Lab_Technician') respond(false,'Unauthorized.');
$db=getDB();
$stmt=$db->prepare("SELECT Staff_ID FROM Lab_Technician WHERE Person_ID=?");
$stmt->execute([$_SESSION['person_id']]);
$tech=$stmt->fetch();
if(!$tech) respond(false,'Technician not found.');
$sid=$tech['Staff_ID'];

$stats=[];
$stats['pending']=$db->query("SELECT COUNT(*) FROM Booking WHERE Booking_Status='Confirmed' AND Booking_ID NOT IN (SELECT DISTINCT Booking_ID FROM Sample_Collection)")->fetchColumn();
$stmt=$db->prepare("SELECT COUNT(*) FROM Sample_Collection WHERE Staff_ID=? AND Collection_Date=CURDATE()");
$stmt->execute([$sid]); $stats['today']=$stmt->fetchColumn();
$stmt=$db->prepare("SELECT COUNT(*) FROM Report_Details rd JOIN Test_Report tr ON rd.Report_ID=tr.Report_ID JOIN Booking b ON tr.Booking_ID=b.Booking_ID JOIN Sample_Collection sc ON b.Booking_ID=sc.Booking_ID WHERE sc.Staff_ID=?");
$stmt->execute([$sid]); $stats['results']=$stmt->fetchColumn();

// Pending collections — all lowercase aliases
$pending=$db->query("
    SELECT 
        b.Booking_ID        AS booking_id,
        b.Booking_Date      AS booking_date,
        CONCAT(p.First_Name,' ',p.Last_Name) AS patient_name,
        (SELECT COUNT(*) FROM Booking_Test WHERE Booking_ID=b.Booking_ID) AS test_count
    FROM Booking b
    JOIN Patient pt ON b.Patient_ID=pt.Patient_ID
    JOIN Person p   ON pt.Person_ID=p.Person_ID
    WHERE b.Booking_Status='Confirmed'
      AND b.Booking_ID NOT IN (SELECT DISTINCT Booking_ID FROM Sample_Collection)
    ORDER BY b.Booking_Date LIMIT 20
")->fetchAll();

// Collected but no report yet
$collected=$db->query("
    SELECT 
        b.Booking_ID        AS booking_id,
        sc.Collection_Date  AS collection_date,
        CONCAT(p.First_Name,' ',p.Last_Name) AS patient_name,
        (SELECT COUNT(*) FROM Booking_Test WHERE Booking_ID=b.Booking_ID) AS test_count
    FROM Sample_Collection sc
    JOIN Booking b  ON sc.Booking_ID=b.Booking_ID
    JOIN Patient pt ON b.Patient_ID=pt.Patient_ID
    JOIN Person p   ON pt.Person_ID=p.Person_ID
    WHERE b.Booking_ID NOT IN (SELECT DISTINCT Booking_ID FROM Test_Report)
    GROUP BY b.Booking_ID
    ORDER BY sc.Collection_Date LIMIT 20
")->fetchAll();

foreach($collected as &$c){
    $ts=$db->prepare("
        SELECT 
            bt.Test_ID      AS test_id,
            t.Test_Name     AS test_name,
            t.Normal_Range  AS normal_range,
            t.Sample_Type   AS sample_type
        FROM Booking_Test bt
        JOIN Test t ON bt.Test_ID=t.Test_ID
        WHERE bt.Booking_ID=?
    ");
    $ts->execute([$c['booking_id']]);
    $c['tests']=$ts->fetchAll();
}

respond(true,'OK',['stats'=>$stats,'pending'=>$pending,'collected'=>$collected]);