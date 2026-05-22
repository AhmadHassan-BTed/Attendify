<?php
session_start();

if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    header("Location: login.php");
    exit();
}

$loggedUserId = $_SESSION['LoggedUserId'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AU Attendance Portal</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community/styles/ag-grid.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community/styles/ag-theme-quartz.css">

    <style>
        :root {
            --primary-color: #1D2254;
            --bg-color: #F4F7FE;
            --white: #ffffff;
            --text-grey: #A3AED0;
            --text-dark: #2B3674;
            --green: #05CD99;
            --red: #EE5D50;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }

        body { background-color: var(--bg-color); height: 100vh; overflow: hidden; display: flex; }

        .sidebar {
            width: 250px;
            background: var(--white);
            height: 100%;
            padding: 20px;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #e0e0e0;
        }
        .logo { display: flex; align-items: center; margin-bottom: 30px; font-weight: 700; color: var(--primary-color); font-size: 1.2rem; }
        .logo img { width: 30px; margin-right: 10px; }

        .nav-links { list-style: none; flex-grow: 1; }
        .nav-links li { margin-bottom: 8px; }
        .nav-links a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: var(--text-grey);
            text-decoration: none;
            border-radius: 10px;
            transition: 0.3s;
            font-size: 0.95rem;
        }
        .nav-links a i { font-size: 1.4rem; margin-right: 12px; }
        .nav-links a:hover, .nav-links a.active {
            background-color: var(--primary-color);
            color: var(--white);
        }
        .nav-links a.active i { color: var(--white); }

        .main-content {
            flex: 1;
            padding: 20px 30px;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .header-left h2 { color: var(--text-dark); font-size: 1.5rem; }
        .header-left p { color: var(--text-grey); font-size: 0.85rem; }
        .header-right { display: flex; gap: 15px; align-items: center; }
        .icon-btn { background: var(--white); border: none; padding: 8px; border-radius: 50%; color: var(--text-grey); cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }

        .controls-bar {
            display: flex;
            background: var(--white);
            padding: 15px;
            border-radius: 15px;
            margin-bottom: 20px;
            align-items: center;
            gap: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.02);
        }
        select {
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            color: var(--text-grey);
            outline: none;
            background: transparent;
            min-width: 150px;
        }

        .toggle-container {
            display: flex;
            background: #F4F7FE;
            padding: 4px;
            border-radius: 20px;
        }
        .toggle-btn {
            padding: 8px 20px;
            border-radius: 16px;
            cursor: pointer;
            font-size: 0.9rem;
            color: var(--text-grey);
            transition: 0.3s;
            display: flex;
            align-items: center;
        }
        .toggle-btn.active {
            background: var(--primary-color);
            color: var(--white);
        }

        input[type="radio"] { display: none; }

        .grid-container {
            background: var(--white);
            border-radius: 20px;
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            box-shadow: 0 5px 15px rgba(0,0,0,0.02);
        }
        .grid-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }

        .ag-theme-quartz {
            --ag-header-background-color: transparent;
            --ag-header-foreground-color: var(--text-grey);
            --ag-row-hover-color: #F4F7FE;
            --ag-selected-row-background-color: #F4F7FE;
            font-family: 'Poppins', sans-serif;
            width: 100%;
            height: 100%;
        }

        .dot { height: 10px; width: 10px; border-radius: 50%; display: inline-block; margin-right: 5px; }
        .live-blue-dot { background-color: var(--primary-color); }
        .accepted-green-dot { background-color: var(--green); }
        .rejected-red-dot { background-color: var(--red); }
        .grey-dot { background-color: #ddd; }

        .right-sidebar {
            width: 300px;
            background: var(--bg-color);
            padding: 20px 20px 20px 0;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .summary-widget {
            background: var(--white);
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.02);
            height: 100%;
        }
        .progress-circle {
            width: 120px; height: 120px; border-radius: 50%;
            background: conic-gradient(var(--primary-color) 0%, #edf2f7 0deg);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px; position: relative;
        }
        .progress-circle-inner {
            width: 100px; height: 100px; background: white; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 1.2rem; color: var(--primary-color);
        }
        .stats-row { display: flex; justify-content: space-around; margin-bottom: 20px; }
        .stat-item h5 { font-size: 0.8rem; color: var(--text-grey); }
        .stat-item p { font-weight: 700; font-size: 1rem; }
        .stat-present { color: var(--green); }
        .stat-absent { color: var(--red); }

        .info-rows { text-align: left; font-size: 0.8rem; margin-bottom: 20px; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 8px; color: var(--text-grey); }
        .info-val { font-weight: 600; color: var(--text-dark); }

        .btn-main {
            width: 100%; background: var(--primary-color); color: white; padding: 12px;
            border: none; border-radius: 10px; font-weight: 500; cursor: pointer; transition: 0.2s;
        }
        .btn-main:hover { background: #151a40; }

        .update-btn { cursor: pointer; display: flex; align-items: center; color: var(--primary-color); }

        .leave-widget {
            margin-bottom: 20px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .leave-card {
            background: var(--white);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.02);
            width: 100%;
            position: relative;
            min-height: 220px;
            display: flex;
            flex-direction: column;
        }
        .leave-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        .leave-stu-name { font-weight: 700; font-size: 1rem; color: var(--text-dark); }
        .leave-title { font-weight: 600; font-size: 0.9rem; color: var(--text-dark); text-align: right; }
        .leave-id-badge {
            background: #F4F7FE; padding: 2px 8px; border-radius: 5px;
            font-size: 0.75rem; color: var(--text-grey); margin-top: 5px; display: inline-block;
        }
        .leave-message {
            font-size: 0.85rem; color: var(--text-grey); line-height: 1.4;
            margin-bottom: 20px; flex-grow: 1;
            overflow-y: auto; max-height: 100px;
        }
        .leave-footer {
            display: flex; justify-content: space-between; align-items: center;
            margin-top: auto;
        }
        .leave-date { font-size: 0.75rem; color: var(--text-grey); }
        .leave-actions { display: flex; gap: 10px; }
        .btn-accept { background: var(--green); color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; font-size: 0.8rem; transition:0.2s; }
        .btn-reject { background: var(--red); color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; font-size: 0.8rem; transition:0.2s; }
        .btn-accept:hover { opacity: 0.9; }
        .btn-reject:hover { opacity: 0.9; }

        .nav-arrow {
            background: var(--primary-color); color: white; border: none;
            width: 30px; height: 30px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; position: absolute; top: 50%; transform: translateY(-50%);
            z-index: 10;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .nav-prev { left: -10px; }
        .nav-next { right: -10px; }
    </style>
</head>
<body>

    <nav class="sidebar">
        <div class="logo">

            Attendance Portal
        </div>

        <ul class="nav-links">
            <li><a href="#"><i class='bx bx-search'></i> Search</a></li>
            <li><a href="#"><i class='bx bxs-dashboard'></i> Dashboard</a></li>
            <li><a href="#" class="active"><i class='bx bx-bookmark-plus'></i> Mark Attendance</a></li>
            <li><a href="#"><i class='bx bx-paperclip'></i> Leave Requests</a></li>
            <li><a href="#"><i class='bx bx-timer'></i> Requests History</a></li>
            <li><a href="#"><i class='bx bx-note'></i> Attendance Report</a></li>
            <li><a href="#"><i class='bx bx-cog'></i> Settings</a></li>
        </ul>

        <ul class="nav-links" style="flex-grow: 0;">
            <li><a href="login.php" class="logout"><i class='bx bx-log-out'></i> Log out</a></li>
        </ul>
    </nav>

    <div class="main-content">
        <header>
            <div class="header-left">
                <h2 id='headone'>Good Afternoon <?php echo htmlspecialchars($loggedUserId); ?></h2>
                <p id='headtwo'>Welcome to GC Uni Attendance Portal</p>
            </div>
            <div class="header-right">
                <button class="icon-btn"><i class='bx bx-bell' id='iconbell'></i></button>
                <button class="icon-btn"><i class='bx bx-cog' id='iconset'></i></button>
            </div>
        </header>

        <div class="controls-bar">
            <select class='course-select' id="courseSelect" name="Expsn">
                <option value="Null" disabled selected>Select Course</option>
            </select>

            <select class='section-select'>
                <option>Select Section</option>
            </select>

            <div class="toggle-container">
                <label class="toggle-btn active" id="lblRegular">
                    Regular
                    <input type="radio" name="att_radio_type" value="Regular" id="radioRegular" checked>
                </label>
                <label class="toggle-btn" id="lblMakeup">
                    Makeup
                    <input type="radio" name="att_radio_type" value="Makeup" id="radioMakeup">
                </label>
            </div>

            <div class="update-btn" onclick="fetchStudentsData()">
                <h4 id='headthree' style="margin-right: 5px;">Update</h4>
                <i class='bx bx-refresh' id='iconupdate' style="font-size:24px;"></i>
            </div>
        </div>

        <div class="grid-container">
            <div class="grid-header">
                <div class="search-mini" style="background:#F4F7FE; padding:8px 15px; border-radius:20px;">
                    <i class='bx bx-search' style="color:var(--text-grey);"></i>
                    <input type="text" placeholder="Search List" style="border:none; background:transparent; outline:none; margin-left:5px;">
                </div>
            </div>
            <div id="myGrid" class="ag-theme-quartz"></div>
        </div>
    </div>

    <div class="right-sidebar">

        <div class="leave-widget" id="leaveWidgetContainer" style="display:none;">
            <button class="nav-arrow nav-prev" onclick="prevLeave()"><i class='bx bx-chevron-left'></i></button>
            <div class="leave-card" id="leaveCard">

            </div>
            <button class="nav-arrow nav-next" onclick="nextLeave()"><i class='bx bx-chevron-right'></i></button>
        </div>

        <div class="summary-widget">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <i class='bx bx-chevron-left'></i>
                <h5 class='datetext'>Today <span id="currentDateDisplay"></span></h5>
                <i class='bx bx-chevron-right'></i>
            </div>

            <div class="progress-circle" id="progressCircle"><div class="progress-circle-inner" id="progressText">0%</div></div>

            <div class="stats-row">
                <div class="stat-item">
                    <h5 class='present'>Present</h5>
                    <p id="statPresent" class="stat-present">0</p>
                </div>
                <div class="stat-item">
                    <h5 class='total'>Total</h5>
                    <p id="statTotal">0</p>
                </div>
                <div class="stat-item">
                    <h5>Absent</h5>
                    <p id="statAbsent" style="color:var(--red);">0</p>
                </div>
            </div>

            <div class="info-rows">
                <div class="info-row">
                    <span>Course Code:</span>
                    <span class="info-val" id="displayCourseCode">SE40283</span>
                </div>
                <div class="info-row">
                    <span>Saved Time:</span>
                    <span class="info-val">No record found</span>
                </div>
                <div class="info-row">
                    <span>Current Time:</span>
                    <span class="info-val" id="currentTimeDisplay">12:00 | 12/9/2023</span>
                </div>
            </div>

            <button class='btn-main' id="markAttendanceBtn">Mark Attendance</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/ag-grid-community/dist/ag-grid-community.min.js"></script>

    <script>

        const th_regId = "<?php echo $loggedUserId; ?>";
        let course_id = "Null";
        let attendance_type = "Regular";
        let gridApi = null;

        const now = new Date();
        const dateStr = now.toLocaleDateString('en-CA'); 

        document.getElementById('currentDateDisplay').textContent = now.toDateString();
        document.getElementById('currentTimeDisplay').textContent = `${now.getHours()}:${String(now.getMinutes()).padStart(2, '0')} | ${now.toLocaleDateString()}`;

        const columnDefs = [
            { 
                headerName: 'Name', 
                field: 'stu_name', 
                headerCheckboxSelection: true, 
                checkboxSelection: true,
                flex: 1.5
            },
            { headerName: 'Registration ID', field: 'stuRegIds', flex: 1 },
            { 
                headerName: 'Attendance%', 
                field: 'attendance_percentage',
                flex: 1,
                cellStyle: params => {
                    if (params.value >= 100) return { color: 'green', fontWeight: 'bold' };
                    else if (params.value <= 75) return { color: '#EE5D50', fontWeight: 'bold' }; 

                    else return { color: 'black', fontWeight: 'bold' };
                },
                cellRenderer: params => {
                    return params.value + '<span style="color: rgb(184, 184, 184)">%</span>';
                }
            },
            { 
                headerName: 'Semester', 
                field: 'semester', 
                flex: 0.8,
                cellRenderer: params => {
                    const rom = {1:"I", 2:"II", 3:"III", 4:"IV", 5:"V", 6:"VI", 7:"VII", 8:"VIII"};
                    return rom[params.value] || params.value;
                }
            },
            { 
                headerName: 'Request Status', 
                field: 'requestStatus',
                flex: 1,
                cellRenderer: params => {
                    let dotClass = 'grey-dot';
                    let statusText = params.value || '';
                    if (params.value === 'Live') dotClass = 'live-blue-dot';
                    else if (params.value === 'Approved') dotClass = 'accepted-green-dot';
                    else if (params.value === 'Rejected') dotClass = 'rejected-red-dot';

                    return `<div style="display:flex; align-items:center;"><span class="dot ${dotClass}"></span> ${statusText}</div>`;
                }
            },
            { headerName: "LeaveId", field: "leave_id", hide: true, suppressToolPanel: true }
        ];

        const gridOptions = {
            columnDefs: columnDefs,
            rowData: [],
            defaultColDef: {
                flex: 1,
                editable: false,
                sortable: true,
                filter: true,
            },
            rowSelection: 'multiple',
            suppressRowClickSelection: true,
            rowHeight: 50,
            headerHeight: 50,
            onSelectionChanged: updateSummaryWidget
        };

        function updateSummaryWidget() {
            if (!gridApi) return;
            let total = 0;
            let selected = 0;
            gridApi.forEachNode(() => total++);
            gridApi.getSelectedNodes().forEach(() => selected++);
            const absent = total - selected;
            const pct = total > 0 ? ((selected / total) * 100).toFixed(1) : 0;

            document.getElementById('statPresent').textContent = selected;
            document.getElementById('statTotal').textContent = total;
            document.getElementById('statAbsent').textContent = absent;
            document.getElementById('progressText').textContent = pct + '%';
            document.getElementById('progressCircle').style.background =
                `conic-gradient(var(--primary-color) ${pct}%, #edf2f7 0deg)`;
        }

        function fetchTeacherRegisteredCourses() {
            const url = `api.php?action=getTeacherCourses&th_regId=${th_regId}&att_date=${dateStr}`;

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    const select = document.getElementById('courseSelect');

                    select.innerHTML = '<option value="Null" disabled selected>Select Course</option>';

                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.courseIDS; 

                        option.text = item.courseOption;
                        select.appendChild(option);
                    });
                })
                .catch(err => console.error('Error fetching courses:', err));
        }

        function fetchStudentsData() {
            if(course_id === "Null") return;

            console.log("Fetching students for:", course_id);
            document.getElementById('displayCourseCode').textContent = course_id;

            const url = `api.php?action=getStudents&course_id=${course_id}&th_regId=${th_regId}&leave_date=${dateStr}`;

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if(gridApi) {
                        gridApi.setGridOption('rowData', data);
                        updateSummaryWidget();
                    }
                })
                .catch(err => console.error('Error fetching students:', err));
        }

        function exportTableAsJson() {
            if(!gridApi) return;

            const exportData = [];
            const datetime = new Date();
            const date = datetime.toLocaleDateString('en-CA');
            const time = datetime.toTimeString().split(' ')[0];

            gridApi.forEachNode(node => {
                const isSelected = node.isSelected() ? 'Present' : 'Absent';
                const { requestStatus, stuRegIds, leave_id } = node.data;

                const rowDataWithVariables = {
                    course_id, 
                    th_regId, 
                    isSelected, 
                    date, 
                    time, 
                    Attendance_type: attendance_type, 
                    stuRegIds, 
                    leave_id
                };
                exportData.push(rowDataWithVariables);
            });

            fetch('api.php?action=markAttendance', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ data: exportData })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Success:', data);
                window.alert("Attendance Marked Successfully");
                fetchStudentsData(); 

                fetchTeacherRegisteredCourses(); 

            })
            .catch(error => {
                console.error('Error:', error);
                window.alert("Error marking attendance");
            });
        }

        document.addEventListener('DOMContentLoaded', () => {

            const gridDiv = document.querySelector('#myGrid');
            gridApi = agGrid.createGrid(gridDiv, gridOptions);

            fetchTeacherRegisteredCourses();

            document.getElementById('courseSelect').addEventListener('change', (e) => {
                course_id = e.target.value;
                fetchStudentsData();
            });

            const radios = document.getElementsByName('att_radio_type');
            const lblRegular = document.getElementById('lblRegular');
            const lblMakeup = document.getElementById('lblMakeup');

            radios.forEach(radio => {
                radio.addEventListener('change', (e) => {
                    attendance_type = e.target.value;

                    if(attendance_type === 'Regular') {
                        lblRegular.classList.add('active');
                        lblMakeup.classList.remove('active');
                    } else {
                        lblMakeup.classList.add('active');
                        lblRegular.classList.remove('active');
                    }
                });
            });

            document.getElementById('markAttendanceBtn').addEventListener('click', exportTableAsJson);

            fetchPendingLeaves();
        });

        let pendingLeaves = [];
        let currentLeaveIndex = 0;

        function fetchPendingLeaves() {
            fetch(`api.php?action=getPendingLeaves&th_regId=${th_regId}`)
                .then(res => res.json())
                .then(data => {
                    pendingLeaves = data;
                    currentLeaveIndex = 0;
                    renderLeaveCard();
                })
                .catch(err => console.error(err));
        }

        function renderLeaveCard() {
            const container = document.getElementById('leaveWidgetContainer');
            if (!pendingLeaves || pendingLeaves.length === 0) {
                container.style.display = 'none';
                return;
            }
            container.style.display = 'flex';

            const leave = pendingLeaves[currentLeaveIndex];
            const card = document.getElementById('leaveCard');

            card.innerHTML = `
                <div class="leave-header">
                    <div>
                        <div class="leave-stu-name">${leave.stu_name}</div>
                        <div class="leave-id-badge">${leave.stu_regId}</div>
                    </div>
                    <div class="leave-title">${leave.leave_title}</div>
                </div>
                <div class="leave-message">
                    ${leave.leave_message}
                </div>
                <div class="leave-footer">
                    <div class="leave-date">${leave.leave_date}</div>
                    <div class="leave-actions">
                        <button class="btn-accept" onclick="updateLeaveStatus(${leave.leave_id}, 'Approved')">Accept</button>
                        <button class="btn-reject" onclick="updateLeaveStatus(${leave.leave_id}, 'Rejected')">Reject</button>
                    </div>
                </div>
            `;
        }

        function nextLeave() {
            if (pendingLeaves.length === 0) return;
            currentLeaveIndex = (currentLeaveIndex + 1) % pendingLeaves.length;
            renderLeaveCard();
        }

        function prevLeave() {
            if (pendingLeaves.length === 0) return;
            currentLeaveIndex = (currentLeaveIndex - 1 + pendingLeaves.length) % pendingLeaves.length;
            renderLeaveCard();
        }

        function updateLeaveStatus(id, status) {
            fetch('api.php?action=updateLeaveStatus', {
                method: 'POST',
                body: JSON.stringify({ leave_id: id, status: status })
            })
            .then(res => res.json())
            .then(data => {
                pendingLeaves.splice(currentLeaveIndex, 1);
                if (currentLeaveIndex >= pendingLeaves.length) {
                    currentLeaveIndex = 0;
                }
                renderLeaveCard();
                fetchStudentsData();
            })
            .catch(err => console.error(err));
        }

    </script>
</body>
</html>