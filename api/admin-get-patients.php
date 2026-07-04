<?php
// api/admin-get-patients.php
require_once __DIR__.'/../includes/helpers.php'; require_once __DIR__.'/../includes/db.php';
if(!isset($_SESSION['person_id'])||$_SESSION['role']!=='Admin') respond(false,'Unauthorized.');
$db=getDB();
$stmt=$db->query("SELECT p.Person_ID person_id,p.First_Name first_name,p.Last_Name last_name,p.Email_ID email,p.Phone_No phone,p.DOB dob,p.Age age,p.Gender gender,pt.Blood_Group blood_group,pt.Address address,(SELECT COUNT(*) FROM Booking WHERE Patient_ID=pt.Patient_ID) booking_count FROM Patient pt JOIN Person p ON pt.Person_ID=p.Person_ID ORDER BY p.First_Name");
respond(true,'OK',['patients'=>$stmt->fetchAll()]);