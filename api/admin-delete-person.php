<?php
// api/admin-delete-person.php
require_once __DIR__.'/../includes/helpers.php';
require_once __DIR__.'/../includes/db.php';
if(!isset($_SESSION['person_id'])||$_SESSION['role']!=='Admin') respond(false,'Unauthorized.');
if($_SERVER['REQUEST_METHOD']!=='POST') respond(false,'Method not allowed.');
$b=getBody();$pid=intval($b['person_id']??0);
if(!$pid) respond(false,'Invalid ID.');
$db=getDB();
$db->prepare("DELETE FROM Person WHERE Person_ID=?")->execute([$pid]);
respond(true,'Deleted.');