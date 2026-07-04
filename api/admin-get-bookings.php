<?php
// api/admin-get-bookings.php
require_once __DIR__.'/../includes/helpers.php';
require_once __DIR__.'/../includes/db.php';
if(!isset($_SESSION['person_id'])||$_SESSION['role']!=='Admin') respond(false,'Unauthorized.');
$db=getDB();
$stmt=$db->query("SELECT b.Booking_ID booking_id,b.Booking_Date booking_date,b.Booking_Status booking_status,b.Total_Amount total_amount,b.Payment_Status payment_status,CONCAT(pp.First_Name,' ',pp.Last_Name) patient_name,CONCAT(dp.First_Name,' ',dp.Last_Name) doctor_name,(SELECT COUNT(*) FROM Booking_Test WHERE Booking_ID=b.Booking_ID) test_count FROM Booking b JOIN Patient pt ON b.Patient_ID=pt.Patient_ID JOIN Person pp ON pt.Person_ID=pp.Person_ID JOIN Doctor d ON b.Doctor_ID=d.Doctor_ID JOIN Person dp ON d.Person_ID=dp.Person_ID ORDER BY b.Booking_ID DESC");
respond(true,'OK',['bookings'=>$stmt->fetchAll()]);