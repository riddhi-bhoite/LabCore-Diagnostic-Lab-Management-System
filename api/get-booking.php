<?php
// api/get-booking.php
require_once __DIR__.'/../includes/helpers.php';
require_once __DIR__.'/../includes/db.php';
if(!isset($_SESSION['person_id'])||$_SESSION['role']!=='Patient') respond(false,'Unauthorized.');
$id=intval($_GET['id']??0); if(!$id) respond(false,'Missing booking ID.');
$db=getDB();
$stmt=$db->prepare("
    SELECT
        b.Booking_ID        AS booking_id,
        b.Booking_Date      AS booking_date,
        b.Booking_Status    AS booking_status,
        b.Total_Amount      AS total_amount,
        b.Payment_Status    AS payment_status,
        CONCAT(p.First_Name,' ',p.Last_Name) AS doctor_name,
        d.Specialization    AS specialization
    FROM Booking b
    JOIN Doctor doc ON b.Doctor_ID=doc.Doctor_ID
    JOIN Person p   ON doc.Person_ID=p.Person_ID
    JOIN Doctor d   ON b.Doctor_ID=d.Doctor_ID
    WHERE b.Booking_ID=?
");
$stmt->execute([$id]); $booking=$stmt->fetch();
if(!$booking) respond(false,'Booking not found.');

$tests=$db->prepare("
    SELECT t.Test_Name AS test_name, t.Test_Price AS test_price
    FROM Booking_Test bt JOIN Test t ON bt.Test_ID=t.Test_ID
    WHERE bt.Booking_ID=?
");
$tests->execute([$id]); $booking['tests']=$tests->fetchAll();
respond(true,'OK',['booking'=>$booking]);