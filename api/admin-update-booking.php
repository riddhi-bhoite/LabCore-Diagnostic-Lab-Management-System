<?php
// api/admin-update-booking.php
require_once __DIR__.'/../includes/helpers.php'; require_once __DIR__.'/../includes/db.php';
if(!isset($_SESSION['person_id'])||$_SESSION['role']!=='Admin') respond(false,'Unauthorized.');
if($_SERVER['REQUEST_METHOD']!=='POST') respond(false,'Method not allowed.');
$b=getBody(); $id=intval($b['booking_id']??0); $status=clean($b['status']??'');
$valid=['Pending','Confirmed','Completed','Cancelled'];
if(!$id||!in_array($status,$valid)) respond(false,'Invalid data.');
getDB()->prepare("UPDATE Booking SET Booking_Status=? WHERE Booking_ID=?")->execute([$status,$id]);
respond(true,'Booking updated.');