<?php
// api/my-reports.php
require_once __DIR__.'/../includes/helpers.php';
require_once __DIR__.'/../includes/db.php';
if(!isset($_SESSION['person_id'])||$_SESSION['role']!=='Patient') respond(false,'Unauthorized.');
$db=getDB();
$stmt=$db->prepare("SELECT Patient_ID FROM Patient WHERE Person_ID=?");
$stmt->execute([$_SESSION['person_id']]);
$pat=$stmt->fetch();
if(!$pat) respond(false,'Not found.');

$stmt=$db->prepare("
    SELECT
        tr.Report_ID        AS report_id,
        tr.Booking_ID       AS booking_id,
        tr.Generated_Date   AS generated_date,
        tr.Report_Status    AS report_status,
        CONCAT(p.First_Name,' ',p.Last_Name)  AS doctor_name,
        CONCAT(ap.First_Name,' ',ap.Last_Name) AS approved_by
    FROM Test_Report tr
    JOIN Booking b      ON tr.Booking_ID=b.Booking_ID
    JOIN Doctor doc     ON b.Doctor_ID=doc.Doctor_ID
    JOIN Person p       ON doc.Person_ID=p.Person_ID
    LEFT JOIN Doctor apd ON tr.Approved_By=apd.Doctor_ID
    LEFT JOIN Person ap  ON apd.Person_ID=ap.Person_ID
    WHERE b.Patient_ID=?
    ORDER BY tr.Generated_Date DESC
");
$stmt->execute([$pat['Patient_ID']]);
$reports=$stmt->fetchAll();

foreach($reports as &$rep){
    $ds=$db->prepare("
        SELECT
            t.Test_Name         AS test_name,
            rd.Result_Value     AS result_value,
            rd.Units            AS units,
            rd.Interpretation   AS interpretation
        FROM Report_Details rd
        JOIN Test t ON rd.Test_ID=t.Test_ID
        WHERE rd.Report_ID=?
    ");
    $ds->execute([$rep['report_id']]);
    $rep['details']=$ds->fetchAll();
}

respond(true,'OK',['reports'=>$reports]);