<?php
// api/my-bookings.php
require_once __DIR__.'/../includes/helpers.php';
require_once __DIR__.'/../includes/db.php';
if(!isset($_SESSION['person_id'])||$_SESSION['role']!=='Patient') respond(false,'Unauthorized.');
$db=getDB();
$stmt=$db->prepare("SELECT Patient_ID FROM Patient WHERE Person_ID=?");
$stmt->execute([$_SESSION['person_id']]);
$pat=$stmt->fetch();
if(!$pat) respond(false,'Not found.');

$stmt=$db->prepare("
    SELECT 
        b.Booking_ID        AS booking_id,
        b.Booking_Date      AS booking_date,
        b.Booking_Status    AS booking_status,
        b.Total_Amount      AS total_amount,
        b.Payment_Status    AS payment_status,
        CONCAT(p.First_Name,' ',p.Last_Name) AS doctor_name,
        d.Specialization    AS specialization,
        (SELECT COUNT(*) FROM Booking_Test WHERE Booking_ID=b.Booking_ID) AS test_count
    FROM Booking b
    JOIN Doctor doc ON b.Doctor_ID=doc.Doctor_ID
    JOIN Person p   ON doc.Person_ID=p.Person_ID
    JOIN Doctor d   ON b.Doctor_ID=d.Doctor_ID
    WHERE b.Patient_ID=?
    ORDER BY b.Booking_Date DESC
");
$stmt->execute([$pat['Patient_ID']]);
respond(true,'OK',['bookings'=>$stmt->fetchAll()]);