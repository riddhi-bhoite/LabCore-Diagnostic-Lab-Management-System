<?php
// api/book-appointment.php
require_once __DIR__.'/../includes/helpers.php';
require_once __DIR__.'/../includes/db.php';
if(!isset($_SESSION['person_id'])||$_SESSION['role']!=='Patient') respond(false,'Unauthorized.');
if($_SERVER['REQUEST_METHOD']!=='POST') respond(false,'Method not allowed.');
$body=getBody();
$doctorId=intval($body['doctor_id']??0);$date=clean($body['booking_date']??'');$testIds=$body['test_ids']??[];
if(!$doctorId||!$date||empty($testIds)) respond(false,'Missing required fields.');
$db=getDB();
$stmt=$db->prepare("SELECT Patient_ID FROM Patient WHERE Person_ID=?");$stmt->execute([$_SESSION['person_id']]);$pat=$stmt->fetch();
if(!$pat) respond(false,'Patient not found.');
$pid=$pat['Patient_ID'];
// calc total
$in=implode(',',array_fill(0,count($testIds),'?'));
$stmt=$db->prepare("SELECT Test_ID,Test_Price FROM Test WHERE Test_ID IN($in)");$stmt->execute($testIds);$tests=$stmt->fetchAll();
if(count($tests)!==count($testIds)) respond(false,'Invalid test selection.');
$total=array_sum(array_column($tests,'Test_Price'));
try{
  $db->beginTransaction();
  $stmt=$db->prepare("INSERT INTO Booking (Patient_ID,Doctor_ID,Booking_Date,Total_Amount) VALUES(?,?,?,?)");
  $stmt->execute([$pid,$doctorId,$date,$total]);$bid=$db->lastInsertId();
  $stmt=$db->prepare("INSERT INTO Booking_Test (Booking_ID,Test_ID,Test_Price) VALUES(?,?,?)");
  foreach($tests as $t) $stmt->execute([$bid,$t['Test_ID'],$t['Test_Price']]);
  $db->commit();
  respond(true,'Booking confirmed.',['booking_id'=>$bid]);
}catch(PDOException $e){$db->rollBack();respond(false,'Booking failed.');}