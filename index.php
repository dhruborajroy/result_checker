<?php
// indivisual Detailed semester result
function getStudentResult($reg, $pro_id, $sess_id, $exam_id) {
    $url = 'https://cmc.du.ac.bd/ajax/get_program_by_exam.php';

    $postFields = http_build_query([
        'reg_no' => $reg,
        'pro_id' => $pro_id,
        'sess_id' => $sess_id,
        'exam_id' => $exam_id,
        'gdata' => 99 // as per their JavaScript
    ]);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    return $response;
}

// Student info
$reg = 862;
$pro_id = 12;
$sess_id = 21;
$exam_id = 1110;

// Fetch result
$result = getStudentResult($reg, $pro_id, $sess_id, $exam_id);

// Output result
echo "<h3>Result for Reg: $reg</h3>";
echo "<div>$result</div>";
?>
