<?php
// api/admin-get-doctors.php
require_once __DIR__.'/../includes/helpers.php';
require_once __DIR__.'/../includes/db.php';
if(!isset($_SESSION['person_id'])||$_SESSION['role']!=='Admin') respond(false,'Unauthorized.');
$db=getDB();
$stmt=$db->query("SELECT d.Doctor_ID doctor_id,p.Person_ID person_id,p.First_Name first_name,p.Last_Name last_name,p.Email_ID email,p.Phone_No phone,p.DOB dob,p.Gender gender,d.Specialization specialization,d.Hospital_Name hospital FROM Doctor d JOIN Person p ON d.Person_ID=p.Person_ID ORDER BY p.First_Name");
respond(true,'OK',['doctors'=>$stmt->fetchAll()]);