<?php
// api/admin-get-tests.php
require_once __DIR__.'/../includes/helpers.php';
require_once __DIR__.'/../includes/db.php';
if(!isset($_SESSION['person_id'])||$_SESSION['role']!=='Admin') respond(false,'Unauthorized.');
$db=getDB();
$stmt=$db->query("SELECT Test_ID test_id,Test_Name test_name,Test_Category test_category,Sample_Type sample_type,Test_Price test_price,Normal_Range normal_range FROM Test ORDER BY Test_Category,Test_Name");
respond(true,'OK',['tests'=>$stmt->fetchAll()]);