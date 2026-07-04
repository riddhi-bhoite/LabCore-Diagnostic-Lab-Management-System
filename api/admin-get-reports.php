<?php
// api/admin-get-reports.php
require_once __DIR__.'/../includes/helpers.php'; require_once __DIR__.'/../includes/db.php';
if(!isset($_SESSION['person_id'])||$_SESSION['role']!=='Admin') respond(false,'Unauthorized.');
$db=getDB();
$stmt=$db->query("SELECT tr.Report_ID report_id,tr.Booking_ID booking_id,tr.Generated_Date generated_date,tr.Report_Status report_status,CONCAT(pp.First_Name,' ',pp.Last_Name) patient_name,CONCAT(dp.First_Name,' ',dp.Last_Name) doctor_name FROM Test_Report tr JOIN Booking b ON tr.Booking_ID=b.Booking_ID JOIN Patient pt ON b.Patient_ID=pt.Patient_ID JOIN Person pp ON pt.Person_ID=pp.Person_ID JOIN Doctor d ON b.Doctor_ID=d.Doctor_ID JOIN Person dp ON d.Person_ID=dp.Person_ID ORDER BY tr.Report_ID DESC");
$reports=$stmt->fetchAll();
foreach($reports as &$r){
  $ds=$db->prepare("SELECT t.Test_Name test_name,rd.Result_Value result_value,rd.Units units,rd.Interpretation interpretation FROM Report_Details rd JOIN Test t ON rd.Test_ID=t.Test_ID WHERE rd.Report_ID=?");
  $ds->execute([$r['report_id']]); $r['details']=$ds->fetchAll();
}
respond(true,'OK',['reports'=>$reports]);