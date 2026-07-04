<?php
// api/get-doctors.php
require_once __DIR__.'/../includes/helpers.php';
require_once __DIR__.'/../includes/db.php';
if(!isset($_SESSION['person_id'])) respond(false,'Unauthorized.');
$db=getDB();
$stmt=$db->query("SELECT d.Doctor_ID doctor_id,CONCAT(p.First_Name,' ',p.Last_Name) name,d.Specialization specialization,d.Hospital_Name hospital FROM Doctor d JOIN Person p ON d.Person_ID=p.Person_ID ORDER BY p.First_Name");
respond(true,'OK',['doctors'=>$stmt->fetchAll()]);