<?php
// api/enter-results.php
require_once __DIR__.'/../includes/helpers.php';
require_once __DIR__.'/../includes/db.php';
if(!isset($_SESSION['person_id'])||$_SESSION['role']!=='Lab_Technician') respond(false,'Unauthorized.');
if($_SERVER['REQUEST_METHOD']!=='POST') respond(false,'Method not allowed.');
$body=getBody();$bid=intval($body['booking_id']??0);$results=$body['results']??[];
if(!$bid||empty($results)) respond(false,'Missing data.');
$db=getDB();
try{
  $db->beginTransaction();
  // Create report
  $stmt=$db->prepare("INSERT INTO Test_Report (Booking_ID,Generated_Date,Report_Status) VALUES(?,CURDATE(),'Ready')");
  $stmt->execute([$bid]);$rid=$db->lastInsertId();
  // Insert each result
  $stmt=$db->prepare("INSERT INTO Report_Details (Report_ID,Test_ID,Result_Value,Units,Interpretation) VALUES(?,?,?,?,?)");
  foreach($results as $r){
    $stmt->execute([$rid,intval($r['test_id']),clean($r['result_value']),clean($r['units']??''),clean($r['interpretation']??'Normal')]);
  }
  $db->commit();
  respond(true,'Results submitted. Report is ready for approval.');
}catch(PDOException $e){$db->rollBack();respond(false,'Failed to save results.');}