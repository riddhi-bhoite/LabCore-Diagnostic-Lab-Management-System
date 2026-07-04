<?php
// api/make-payment.php
require_once __DIR__.'/../includes/helpers.php';
require_once __DIR__.'/../includes/db.php';
if(!isset($_SESSION['person_id'])||$_SESSION['role']!=='Patient') respond(false,'Unauthorized.');
if($_SERVER['REQUEST_METHOD']!=='POST') respond(false,'Method not allowed.');
$body=getBody();
$bid=intval($body['booking_id']??0);$amount=floatval($body['amount_paid']??0);$mode=clean($body['payment_mode']??'');
if(!$bid||$amount<=0||!$mode) respond(false,'Invalid payment details.');
$validModes=['Cash','Card','UPI','Net_Banking','Cheque'];
if(!in_array($mode,$validModes)) respond(false,'Invalid payment mode.');
$db=getDB();
try{
  $db->beginTransaction();
  $stmt=$db->prepare("INSERT INTO Payment (Booking_ID,Amount_Paid,Payment_Date,Payment_Mode) VALUES(?,?,CURDATE(),?)");
  $stmt->execute([$bid,$amount,$mode]);
  $stmt=$db->prepare("UPDATE Booking SET Payment_Status='Paid' WHERE Booking_ID=?");
  $stmt->execute([$bid]);
  $db->commit();
  respond(true,'Payment recorded successfully.');
}catch(PDOException $e){$db->rollBack();respond(false,'Payment failed.');}