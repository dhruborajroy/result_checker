<?php
// all bec students semester wise result
function getStudentResult($reg, $pro_id, $sess_id, $exam_id) {
    $url = 'https://cmc.du.ac.bd/ajax/get_program_by_exam.php';

    $postFields = http_build_query([
        'reg_no' => $reg,
        'pro_id' => $pro_id,
        'sess_id' => $sess_id,
        'exam_id' => $exam_id,
        'gdata' => 99
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

$students = [];
for ($reg = 835; $reg <= 890; $reg++) {
    $students[] = [
        'reg' => $reg, 
        'pro_id' => 12, 
        'sess_id' => 21,  
        'exam_id' => 1110 //just change the exam id
    ];
}


// Output table
echo "<table border='1' cellpadding='10' cellspacing='0'>";
echo "<tr><th>Name</th><th>Registration</th><th>GPA</th><th>CGPA</th></tr>";
echo "All bec students semester wise result";
foreach ($students as $student) {
    $result = getStudentResult($student['reg'], $student['pro_id'], $student['sess_id'], $student['exam_id']);
    
    // Extract the student's name, GPA, and CGPA from the result
    preg_match("/Student's Name<\/th><td>(.*?)<\/td>/", $result, $nameMatch);
    preg_match("/GPA:\s*([0-9.]+)/", $result, $gpaMatch);
    preg_match("/CGPA:\s*([0-9.]+)/", $result, $cgpaMatch);

    // Skip the row if the name is blank
    $name = $nameMatch[1] ?? '';
    if (empty($name)) {
        continue;
    }
    $gpa = $gpaMatch[1] ?? '';
    $cgpa = $cgpaMatch[1] ?? '';
    echo "<tr>";
    echo "<td>$name</td>";
    echo "<td>" . $student['reg'] . "</td>";
    echo "<td>$gpa</td>";
    echo "<td>$cgpa</td>";
    echo "</tr>";
}

echo "</table>";
?>
