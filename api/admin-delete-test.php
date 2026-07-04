<?php // api/admin-delete-test.php
require_once __DIR__.'/../includes/helpers.php'; require_once __DIR__.'/../includes/db.php';
if(!isset($_SESSION['person_id'])||$_SESSION['role']!=='Admin') respond(false,'Unauthorized.');
if($_SERVER['REQUEST_METHOD']!=='POST') respond(false,'Method not allowed.');
$b=getBody(); $id=intval($b['test_id']??0); if(!$id) respond(false,'Invalid ID.');
getDB()->prepare("DELETE FROM Test WHERE Test_ID=?")->execute([$id]); respond(true,'Test deleted.');