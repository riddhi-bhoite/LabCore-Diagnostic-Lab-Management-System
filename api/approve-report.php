<?php
// api/approve-report.php
require_once __DIR__.'/../includes/helpers.php';
require_once __DIR__.'/../includes/db.php';
if(!isset($_SESSION['person_id'])||$_SESSION['role']!=='Doctor') respond(false,'Unauthorized.');
if($_SERVER['REQUEST_METHOD']!=='POST') respond(false,'Method not allowed.');
$body=getBody();$rid=intval($body['report_id']??0);if(!$rid) respond(false,'Invalid report.');
$db=getDB();
$stmt=$db->prepare("SELECT Doctor_ID FROM Doctor WHERE Person_ID=?");$stmt->execute([$_SESSION['person_id']]);$doc=$stmt->fetch();
$stmt=$db->prepare("UPDATE Test_Report SET Report_Status='Approved',Approved_By=? WHERE Report_ID=? AND Report_Status='Ready'");
$stmt->execute([$doc['Doctor_ID'],$rid]);
if($stmt->rowCount()) respond(true,'Report approved.');
else respond(false,'Report not found or already approved.');