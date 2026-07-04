<?php
// api/admin-get-technicians.php
require_once __DIR__.'/../includes/helpers.php'; require_once __DIR__.'/../includes/db.php';
if(!isset($_SESSION['person_id'])||$_SESSION['role']!=='Admin') respond(false,'Unauthorized.');
$db=getDB();
$stmt=$db->query("SELECT lt.Staff_ID staff_id,p.Person_ID person_id,p.First_Name first_name,p.Last_Name last_name,p.Email_ID email,p.Phone_No phone FROM Lab_Technician lt JOIN Person p ON lt.Person_ID=p.Person_ID ORDER BY p.First_Name");
respond(true,'OK',['techs'=>$stmt->fetchAll()]);