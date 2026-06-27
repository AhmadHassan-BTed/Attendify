<?php

include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'login':
        handleLogin($conn);
        break;
    case 'register':
        handleRegister($conn);
        break;
    case 'getTodayLeaves':
        handleGetTodayLeaves($conn);
        break;
    case 'getStudents':
        handleGetStudents($conn);
        break;
    case 'getTeacherCourses':
        handleGetTeacherCourses($conn);
        break;
    case 'markAttendance':
        handleMarkAttendance($conn);
        break;
    case 'getPendingLeaves':
        handleGetPendingLeaves($conn);
        break;
    case 'updateLeaveStatus':
        handleUpdateLeaveStatus($conn);
        break;
    case 'getDashboardData':
        handleGetDashboardData($conn);
        break;
    case 'getAllAttendance':
        handleGetAllAttendance($conn);
        break;
    case 'getAllLeavesHistory':
        handleGetAllLeavesHistory($conn);
        break;
    default:
        echo json_encode(["message" => "Invalid API Action", "received_action" => $action]);
        break;
}

$conn->close();

function handleGetDashboardData($conn) {
    $th_regId = $_GET['th_regId'] ?? '';
    $response = [
        'totals' => ['total_classes' => 0, 'total_present' => 0, 'total_absent' => 0],
        'status' => ['present' => 0, 'absent' => 0],
        'courses' => [],
        'trends' => []
    ];

    // 1. Get Totals
    $sql1 = "SELECT 
                COUNT(DISTINCT att_date, course_id) as total_classes,
                SUM(CASE WHEN att_status = 'Present' THEN 1 ELSE 0 END) as total_present,
                SUM(CASE WHEN att_status = 'Absent' THEN 1 ELSE 0 END) as total_absent
             FROM attendance WHERE th_regid = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("s", $th_regId);
    $stmt1->execute();
    $res1 = $stmt1->get_result()->fetch_assoc();
    if($res1) {
        $response['totals'] = $res1;
        $response['status']['present'] = $res1['total_present'];
        $response['status']['absent'] = $res1['total_absent'];
    }
    $stmt1->close();

    // 2. Get Course Breakdown
    $sql2 = "SELECT course_id, 
                SUM(CASE WHEN att_status = 'Present' THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN att_status = 'Absent' THEN 1 ELSE 0 END) as absent
             FROM attendance WHERE th_regid = ? GROUP BY course_id";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("s", $th_regId);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    while($row = $res2->fetch_assoc()) {
        $response['courses'][] = $row;
    }
    $stmt2->close();

    // 3. Get Trends (Last 7 distinct dates taught)
    $sql3 = "SELECT att_date, 
                SUM(CASE WHEN att_status = 'Present' THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN att_status = 'Absent' THEN 1 ELSE 0 END) as absent
             FROM attendance WHERE th_regid = ? 
             GROUP BY att_date ORDER BY att_date DESC LIMIT 7";
    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param("s", $th_regId);
    $stmt3->execute();
    $res3 = $stmt3->get_result();
    $tempTrends = [];
    while($row = $res3->fetch_assoc()) {
        $tempTrends[] = $row; 
    }
    $stmt3->close();
    
    // Reverse trends to show oldest to newest left-to-right on the graph
    $response['trends'] = array_reverse($tempTrends);

    echo json_encode($response);
}
function handleGetAllAttendance($conn) {
    $th_regId = $_GET['th_regId'] ?? '';

    // Join attendance with students to get the student's name
    $sql = "SELECT 
                a.att_id,
                a.att_date,
                a.att_timerecorded,
                a.course_id,
                s.stu_name,
                a.stu_regId,
                a.att_status,
                a.att_type
            FROM attendance a
            JOIN students s ON a.stu_regId = s.stu_regId
            WHERE a.th_regid = ?
            ORDER BY a.att_date DESC, a.att_timerecorded DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $th_regId);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
    } else {
        echo json_encode(["error" => "Query Failed: " . $stmt->error]);
    }
    $stmt->close();
}

function handleGetAllLeavesHistory($conn) {
    $th_regId = $_GET['th_regId'] ?? '';

    // No date filter here, just grab everything for this teacher
    $sql = "SELECT 
                la.leave_id, 
                la.stu_regId, 
                s.stu_name, 
                la.course_id,
                la.leave_title, 
                la.leave_message, 
                la.leave_date,
                la.request_status
            FROM leave_application la
            JOIN students s ON la.stu_regId = s.stu_regId
            JOIN teacherregisteredcourses trc ON la.course_id = trc.course_id
            WHERE trc.th_regId = ?
            ORDER BY la.leave_date DESC"; // Descending order puts newest at the top

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $th_regId);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
    } else {
        echo json_encode(["error" => "Query Failed: " . $stmt->error]);
    }
    $stmt->close();
}
function handleGetTodayLeaves($conn) {
    $th_regId = $_GET['th_regId'] ?? '';
    $leave_date = $_GET['leave_date'] ?? '';

    $sql = "SELECT 
                la.leave_id, 
                la.stu_regId, 
                s.stu_name, 
                la.course_id,
                la.leave_title, 
                la.leave_message, 
                la.request_status
            FROM leave_application la
            JOIN students s ON la.stu_regId = s.stu_regId
            JOIN teacherregisteredcourses trc ON la.course_id = trc.course_id
            WHERE trc.th_regId = ? AND la.leave_date = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $th_regId, $leave_date);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
    } else {
        echo json_encode(["error" => "Query Failed: " . $stmt->error]);
    }
    $stmt->close();
}

function handleLogin($conn) {
    $data = json_decode(file_get_contents("php://input"));

    if(isset($data->LoginUserName) && isset($data->LoginPassword)) {
        $stmt = $conn->prepare("SELECT th_regId FROM login WHERE l_username = ? AND l_password = ?");
        $stmt->bind_param("ss", $data->LoginUserName, $data->LoginPassword);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $rows = [];
            while($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            echo json_encode($rows);
        } else {
            echo json_encode(["message" => "Credentials Dont Match"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["message" => "Invalid Input"]);
    }
}

function handleRegister($conn) {
    $data = json_decode(file_get_contents("php://input"));

    if(isset($data->Email)) {
        $stmt = $conn->prepare("INSERT INTO login(th_regId, l_username, l_password, l_recmail) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss", $data->UserRegId, $data->UserName, $data->Password, $data->Email);

        if ($stmt->execute()) {
            echo json_encode(["message" => "User Added"]);
        } else {
            echo json_encode(["error" => "Registration Failed: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "Missing Email"]);
    }
}

function handleGetStudents($conn) {
    $course_id = $_GET['course_id'] ?? '';
    $leave_date = $_GET['leave_date'] ?? '';
    $th_regId = $_GET['th_regId'] ?? '';

    $sql = "SELECT 
                students.stu_name, 
                sub_query.IDs AS stuRegIds, 
                sub_query.attendance_percentage, 
                2026 - students.stu_batch AS semester, 
                sub_query.RQs AS requestStatus,
                sub_query.lids AS leave_id
            FROM students
            LEFT JOIN (
                SELECT 
                    studentregisteredcourses.stu_regId AS IDs,
                    ROUND(
                        COUNT(DISTINCT CASE WHEN attendance.att_status = 'Present' THEN attendance.att_date END) * 100.0
                        / GREATEST(COUNT(DISTINCT attendance.att_date), 1)
                    , 2) AS attendance_percentage, 
                    COALESCE(MAX(leave_application.request_status), 'No Request') AS RQs,
                    MAX(leave_application.leave_id) AS lids
                FROM studentregisteredcourses
                INNER JOIN students ON studentregisteredcourses.stu_regId = students.stu_regId
                LEFT JOIN attendance ON studentregisteredcourses.stu_regId = attendance.stu_regId
                    AND studentregisteredcourses.course_id = attendance.course_id
                    AND attendance.th_regid = ?
                LEFT JOIN leave_application ON students.stu_regId = leave_application.stu_regId 
                    AND leave_application.leave_date = ?
                WHERE studentregisteredcourses.course_id = ?
                GROUP BY studentregisteredcourses.stu_regId
            ) AS sub_query ON students.stu_regId = sub_query.IDs
            WHERE sub_query.IDs IS NOT NULL";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("sss", $th_regId, $leave_date, $course_id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
    } else {
        echo json_encode(["error" => "Query Failed: " . $stmt->error]);
    }
    $stmt->close();
}

function handleGetTeacherCourses($conn) {
    $th_regId = $_GET['th_regId'] ?? '';
    $att_date = $_GET['att_date'] ?? '';

    $sql = "SELECT courses.course_id AS courseIDS, CONCAT(courses.course_id, ' - ', courses.course_name) AS courseOption 
            FROM (
                SELECT trc.course_id, crs.course_name 
                FROM teacherregisteredcourses trc 
                JOIN courses crs ON trc.course_id = crs.course_id 
                WHERE trc.th_regId = ?
            ) AS courses
            LEFT JOIN attendance at ON courses.course_id = at.course_id AND at.att_date = ?
            WHERE at.course_id IS NULL";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $th_regId, $att_date);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
    } else {
        echo json_encode(["error" => "Query Failed: " . $stmt->error]);
    }
    $stmt->close();
}

function handleMarkAttendance($conn) {

    $input = json_decode(file_get_contents("php://input"), true);
    $data = $input['data'] ?? null;

    if (!is_array($data)) {
        http_response_code(400);
        die(json_encode(["error" => "Invalid data format"]));
    }

    $conn->begin_transaction();

    try {

        $stmtInsert = $conn->prepare("INSERT INTO attendance (course_id, th_regid, att_status, att_date, att_timerecorded, att_type, stu_regId, leave_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        foreach ($data as $item) {
            $stmtInsert->bind_param("sssssssi", 
                $item['course_id'], 
                $item['th_regId'], 
                $item['isSelected'], 
                $item['date'], 
                $item['time'], 
                $item['Attendance_type'], 
                $item['stuRegIds'], 
                $item['leave_id']
            );
            if (!$stmtInsert->execute()) {
                throw new Exception("Insert Attendance Failed: " . $stmtInsert->error);
            }
        }
        $stmtInsert->close();

        $uniqueCourses = [];
        foreach ($data as $item) {
            $key = $item['th_regId'] . '-' . $item['course_id'];
            $uniqueCourses[$key] = [
                'th_regId' => $item['th_regId'], 
                'course_id' => $item['course_id']
            ];
        }

        $stmtUpdateTeacher = $conn->prepare("UPDATE teacherregisteredcourses SET trc_totalClassesTaken = trc_totalClassesTaken + 1 WHERE th_regId = ? AND course_id = ?");
        foreach ($uniqueCourses as $course) {
            $stmtUpdateTeacher->bind_param("ss", $course['th_regId'], $course['course_id']);
            if (!$stmtUpdateTeacher->execute()) {
                throw new Exception("Update Teacher Failed: " . $stmtUpdateTeacher->error);
            }
        }
        $stmtUpdateTeacher->close();

        $presentStudents = [];
        foreach ($data as $item) {
            if ($item['isSelected'] === 'Present') {
                $key = $item['stuRegIds'] . '-' . $item['course_id'];
                $presentStudents[$key] = [
                    'stuRegIds' => $item['stuRegIds'], 
                    'course_id' => $item['course_id']
                ];
            }
        }

        $stmtUpdateStudent = $conn->prepare("UPDATE studentregisteredcourses SET src_totalPresent = src_totalPresent + 1 WHERE stu_RegId = ? AND course_id = ?");
        foreach ($presentStudents as $student) {
            $stmtUpdateStudent->bind_param("ss", $student['stuRegIds'], $student['course_id']);
            if (!$stmtUpdateStudent->execute()) {
                throw new Exception("Update Student Failed: " . $stmtUpdateStudent->error);
            }
        }
        $stmtUpdateStudent->close();

        $conn->commit();
        echo json_encode(["message" => "Data inserted and incremented successfully"]);

    } catch (Exception $e) {

        $conn->rollback();
        http_response_code(500);
        echo json_encode(["error" => "Transaction failed: " . $e->getMessage()]);
    }
}

function handleGetPendingLeaves($conn) {
    $th_regId = $_GET['th_regId'] ?? '';

    $sql = "SELECT 
                la.leave_id, 
                la.stu_regId, 
                s.stu_name, 
                la.leave_title, 
                la.leave_message, 
                la.leave_date,
                la.course_id
            FROM leave_application la
            JOIN students s ON la.stu_regId = s.stu_regId
            JOIN teacherregisteredcourses trc ON la.course_id = trc.course_id
            WHERE trc.th_regId = ? AND la.request_status = 'Pending'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $th_regId);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
    } else {
        echo json_encode(["error" => "Query Failed: " . $stmt->error]);
    }
    $stmt->close();
}

function handleUpdateLeaveStatus($conn) {
    $data = json_decode(file_get_contents("php://input"));

    if (isset($data->leave_id) && isset($data->status)) {
        $stmt = $conn->prepare("UPDATE leave_application SET request_status = ? WHERE leave_id = ?");
        $stmt->bind_param("si", $data->status, $data->leave_id);

        if ($stmt->execute()) {
            // Add "success" => true right here:
            echo json_encode(["success" => true, "message" => "Leave status updated to " . $data->status]);
        } else {
            echo json_encode(["success" => false, "error" => "Update Failed: " . $stmt->error]);
        }

        // if ($stmt->execute()) {
        //     echo json_encode(["message" => "Leave status updated to " . $data->status]);
        // } else {
        //     echo json_encode(["error" => "Update Failed: " . $stmt->error]);
        // }
        $stmt->close();
    } else {
        echo json_encode(["error" => "Invalid Input"]);
    }
}
?>