<?php
$orderBy = isset($_GET['order']) ? $_GET['order'] : 'gpa'; // Default order by GPA

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
for ($reg = 2022054658; $reg <= 2022654711; $reg++) {
    $students[] = [
        'reg' => $reg, 
        'pro_id' => 12, 
        'sess_id' => 23, 
        'exam_id' => 1208
    ];
}

$results = [];
foreach ($students as $student) {
    $reg = $student['reg'];

    // Detect Department
    if ($reg >= 713 && $reg <= 800) {
        $dept = 'MEC';
    } elseif ($reg >= 835 && $reg <= 890) {
        $dept = 'BEC';
    } elseif ($reg >= 1036 && $reg <= 1090) {
        $dept = 'FEC';
    } else {
        $dept = '';
    }

    $result = getStudentResult($reg, $student['pro_id'], $student['sess_id'], $student['exam_id']);
    
    preg_match("/Student's Name<\/th><td>(.*?)<\/td>/", $result, $nameMatch);
    preg_match("/College Name<\/th><td>(.*?)<\/td>/", $result, $collegeMatch);
    preg_match("/Session<\/th><td>(.*?)<\/td>/", $result, $sessionMatch);
    preg_match("/Program<\/th><td>(.*?)<\/td>/", $result, $programMatch);
    preg_match("/Exam Roll<\/th><td>(.*?)<\/td>/", $result, $examRollMatch);
    preg_match("/Class Roll<\/th><td>(.*?)<\/td>/", $result, $classRollMatch);
    preg_match("/Exam Year<\/th><td>(.*?)<\/td>/", $result, $examYearMatch);
    preg_match("/Result Publication Date<\/th><td>(.*?)<\/td>/", $result, $resultDateMatch);
    preg_match("/GPA:\s*([0-9.]+)/", $result, $gpaMatch);
    preg_match("/CGPA:\s*([0-9.]+)/", $result, $cgpaMatch);

    $name = $nameMatch[1] ?? '';
    if (empty($name)) {
        continue;
    }

    $college = $collegeMatch[1] ?? '';
    $session = $sessionMatch[1] ?? '';
    $program = $programMatch[1] ?? '';
    $examRoll = $examRollMatch[1] ?? '';
    $classRoll = $classRollMatch[1] ?? '';
    $examYear = $examYearMatch[1] ?? '';
    $resultDate = $resultDateMatch[1] ?? '';
    $gpa = isset($gpaMatch[1]) ? floatval($gpaMatch[1]) : 0;
    $cgpa = isset($cgpaMatch[1]) ? floatval($cgpaMatch[1]) : 0;

    $results[] = [
        'college' => $college,
        'name' => $name,
        'reg' => $reg,
        'session' => $session,
        'program' => $program,
        'exam_roll' => $examRoll,
        'class_roll' => $classRoll,
        'exam_year' => $examYear,
        'result_date' => $resultDate,
        'gpa' => $gpa,
        'cgpa' => $cgpa,
        'dept' => $dept
    ];
}

// Sorting
usort($results, function($a, $b) use ($orderBy) {
    return $b[$orderBy] <=> $a[$orderBy];
});


echo "<div style='margin: 20px;'>";
echo "<form method='GET' style='margin-bottom: 20px;'>
        <label>Order by:</label>
        <select name='order' onchange='this.form.submit()'>
            <option value='gpa' " . ($orderBy == 'gpa' ? 'selected' : '') . ">GPA</option>
            <option value='cgpa' " . ($orderBy == 'cgpa' ? 'selected' : '') . ">CGPA</option>
        </select>
      </form>";

// Table start
echo "<div style='overflow-x: auto;'>";
echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
echo "<thead style='background-color: #f2f2f2;'>";
echo "<tr>
        <th>SL</th>
        <th>Student's Name</th>
        <th>Registration</th>
        <th>GPA</th>
        <th>CGPA</th>
        <th>College Name</th>
      </tr>";
echo "</thead><tbody>";

$i = 1;
foreach ($results as $student) {
    echo "<tr>";
    echo "<td>{$i}</td>";
    $i++;
    echo "<td>{$student['name']}</td>";
    echo "<td>{$student['reg']}</td>";
    echo "<td>{$student['gpa']}</td>";
    echo "<td>{$student['cgpa']}</td>";
    echo "<td>{$student['college']}</td>";
    echo "</tr>";
}

echo "</tbody></table></div></div>";
?>
