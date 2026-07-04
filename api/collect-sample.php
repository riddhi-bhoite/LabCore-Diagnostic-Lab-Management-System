<?php
// api/collect-sample.php
require_once __DIR__.'/../includes/helpers.php';
require_once __DIR__.'/../includes/db.php';
if(!isset($_SESSION['person_id'])||$_SESSION['role']!=='Lab_Technician') respond(false,'Unauthorized.');
if($_SERVER['REQUEST_METHOD']!=='POST') respond(false,'Method not allowed.');
$body=getBody();$bid=intval($body['booking_id']??0);$date=clean($body['collection_date']??'');
if(!$bid||!$date) respond(false,'Missing fields.');
$db=getDB();
$stmt=$db->prepare("SELECT Staff_ID FROM Lab_Technician WHERE Person_ID=?");$stmt->execute([$_SESSION['person_id']]);$tech=$stmt->fetch();
$stmt=$db->prepare("INSERT INTO Sample_Collection (Booking_ID,Staff_ID,Collection_Date) VALUES(?,?,?)");
$stmt->execute([$bid,$tech['Staff_ID'],$date]);
// Update booking status to Completed
$db->prepare("UPDATE Booking SET Booking_Status='Completed' WHERE Booking_ID=?")->execute([$bid]);
respond(true,'Sample collection recorded.');