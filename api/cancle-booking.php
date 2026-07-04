<?php
// api/cancel-booking.php
require_once __DIR__.'/../includes/helpers.php';
require_once __DIR__.'/../includes/db.php';
if(!isset($_SESSION['person_id'])||$_SESSION['role']!=='Patient') respond(false,'Unauthorized.');
if($_SERVER['REQUEST_METHOD']!=='POST') respond(false,'Method not allowed.');
$body=getBody();$bid=intval($body['booking_id']??0);
if(!$bid) respond(false,'Invalid booking.');
$db=getDB();
$stmt=$db->prepare("SELECT Patient_ID FROM Patient WHERE Person_ID=?");$stmt->execute([$_SESSION['person_id']]);$pat=$stmt->fetch();
$stmt=$db->prepare("UPDATE Booking SET Booking_Status='Cancelled' WHERE Booking_ID=? AND Patient_ID=? AND Booking_Status='Pending'");
$stmt->execute([$bid,$pat['Patient_ID']]);
if($stmt->rowCount()) respond(true,'Booking cancelled.');
else respond(false,'Cannot cancel this booking.');