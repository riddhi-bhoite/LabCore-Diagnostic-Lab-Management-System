<?php
// api/admin-save-test.php
require_once __DIR__.'/../includes/helpers.php';
require_once __DIR__.'/../includes/db.php';
if(!isset($_SESSION['person_id'])||$_SESSION['role']!=='Admin') respond(false,'Unauthorized.');
if($_SERVER['REQUEST_METHOD']!=='POST') respond(false,'Method not allowed.');
$b=getBody();
$name=clean($b['test_name']??'');$cat=clean($b['test_category']??'');
$sample=clean($b['sample_type']??'');$price=floatval($b['test_price']??0);
$range=clean($b['normal_range']??'');$editMode=(bool)($b['edit_mode']??false);
if(!$name||!$cat||!$sample||$price<=0) respond(false,'Missing required fields.');
$db=getDB();
if($editMode){
  $id=intval($b['test_id']??0);if(!$id) respond(false,'Invalid test ID.');
  $db->prepare("UPDATE Test SET Test_Name=?,Test_Category=?,Sample_Type=?,Test_Price=?,Normal_Range=? WHERE Test_ID=?")->execute([$name,$cat,$sample,$price,$range?:null,$id]);
  respond(true,'Test updated.');
}else{
  $db->prepare("INSERT INTO Test(Test_Name,Test_Category,Sample_Type,Test_Price,Normal_Range)VALUES(?,?,?,?,?)")->execute([$name,$cat,$sample,$price,$range?:null]);
  respond(true,'Test added.');
}