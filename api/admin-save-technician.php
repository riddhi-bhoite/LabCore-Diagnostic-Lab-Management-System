<?php
// api/admin-save-technician.php
require_once __DIR__.'/../includes/helpers.php'; require_once __DIR__.'/../includes/db.php';
if(!isset($_SESSION['person_id'])||$_SESSION['role']!=='Admin') respond(false,'Unauthorized.');
if($_SERVER['REQUEST_METHOD']!=='POST') respond(false,'Method not allowed.');
$b=getBody();
$fn=clean($b['first_name']??'');$ln=clean($b['last_name']??'');$dob=clean($b['dob']??'');
$gender=clean($b['gender']??'');$email=clean($b['email']??'');$phone=clean($b['phone']??'');$pwd=($b['password']??'');
if(!$fn||!$ln||!$dob||!$gender||!$email||!$phone||strlen($pwd)<8) respond(false,'Missing or invalid fields.');
$db=getDB();
$chk=$db->prepare("SELECT Person_ID FROM Person WHERE Email_ID=? OR Phone_No=?");$chk->execute([$email,$phone]);
if($chk->fetch()) respond(false,'Email or phone already registered.');
$hash=password_hash($pwd,PASSWORD_BCRYPT);
try{
  $db->beginTransaction();
  $db->prepare("INSERT INTO Person(First_Name,Last_Name,DOB,Gender,Phone_No,Email_ID,Role,Password_Hash)VALUES(?,?,?,?,?,?,'Lab_Technician',?)")->execute([$fn,$ln,$dob,$gender,$phone,$email,$hash]);
  $pid=$db->lastInsertId();
  $db->prepare("INSERT INTO Lab_Technician(Person_ID)VALUES(?)")->execute([$pid]);
  $db->commit(); respond(true,'Technician added.');
}catch(PDOException $e){$db->rollBack();respond(false,'Insert failed.');}