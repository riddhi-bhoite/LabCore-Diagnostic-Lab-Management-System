<?php
// api/admin-save-doctor.php
require_once __DIR__.'/../includes/helpers.php';
require_once __DIR__.'/../includes/db.php';
if(!isset($_SESSION['person_id'])||$_SESSION['role']!=='Admin') respond(false,'Unauthorized.');
if($_SERVER['REQUEST_METHOD']!=='POST') respond(false,'Method not allowed.');
$b=getBody();
$fn=clean($b['first_name']??'');$ln=clean($b['last_name']??'');$dob=clean($b['dob']??'');
$gender=clean($b['gender']??'');$email=clean($b['email']??'');$phone=clean($b['phone']??'');
$spec=clean($b['specialization']??'');$hosp=clean($b['hospital']??'');
$editMode=(bool)($b['edit_mode']??false);
if(!$fn||!$ln||!$dob||!$gender||!$email||!$phone||!$spec) respond(false,'Missing required fields.');
$db=getDB();
if($editMode){
  $personId=intval($b['person_id']??0);$doctorId=intval($b['doctor_id']??0);
  if(!$personId||!$doctorId) respond(false,'Invalid IDs.');
  try{
    $db->beginTransaction();
    $db->prepare("UPDATE Person SET First_Name=?,Last_Name=?,DOB=?,Gender=?,Email_ID=?,Phone_No=? WHERE Person_ID=?")->execute([$fn,$ln,$dob,$gender,$email,$phone,$personId]);
    $db->prepare("UPDATE Doctor SET Specialization=?,Hospital_Name=? WHERE Doctor_ID=?")->execute([$spec,$hosp,$doctorId]);
    $db->commit();respond(true,'Doctor updated.');
  }catch(PDOException $e){$db->rollBack();respond(false,'Update failed: '.$e->getMessage());}
}else{
  $pwd=($b['password']??'');
  if(strlen($pwd)<8) respond(false,'Password must be 8+ characters.');
  $chk=$db->prepare("SELECT Person_ID FROM Person WHERE Email_ID=? OR Phone_No=?");$chk->execute([$email,$phone]);
  if($chk->fetch()) respond(false,'Email or phone already registered.');
  $hash=password_hash($pwd,PASSWORD_BCRYPT);
  try{
    $db->beginTransaction();
    $db->prepare("INSERT INTO Person(First_Name,Last_Name,DOB,Gender,Phone_No,Email_ID,Role,Password_Hash)VALUES(?,?,?,?,?,?,'Doctor',?)")->execute([$fn,$ln,$dob,$gender,$phone,$email,$hash]);
    $pid=$db->lastInsertId();
    $db->prepare("INSERT INTO Doctor(Person_ID,Specialization,Hospital_Name)VALUES(?,?,?)")->execute([$pid,$spec,$hosp]);
    $db->commit();respond(true,'Doctor added.');
  }catch(PDOException $e){$db->rollBack();respond(false,'Insert failed: '.$e->getMessage());}
}