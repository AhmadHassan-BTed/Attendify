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
    <title>Attendance Report - AU Attendance Portal</title>

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
            font-family: 'Poppins', sans-serif;
            width: 100%;
            height: 100%;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .status-present { background-color: rgba(5, 205, 153, 0.15); color: var(--green); }
        .status-absent { background-color: rgba(238, 93, 80, 0.15); color: var(--red); }
        
        .type-badge {
            background: #F4F7FE;
            padding: 2px 8px;
            border-radius: 5px;
            font-size: 0.75rem;
            color: var(--text-grey);
        }

    </style>
</head>
<body>

    <nav class="sidebar">
        <div class="logo">
            Attendance Portal
        </div>

        <?php include 'sidebar.php'; ?>

    </nav>

    <div class="main-content">
        <header>
            <div class="header-left">
                <h2>Master Attendance Report</h2>
                <p>A complete log of all attendance records marked by you</p>
            </div>
            <div class="header-right">
                <button class="icon-btn" onclick="exportToCSV()" title="Export to CSV"><i class='bx bx-download' style="font-size: 1.2rem;"></i></button>
                <button class="icon-btn" onclick="fetchAllAttendance()"><i class='bx bx-refresh' style="font-size: 1.2rem;"></i></button>
            </div>
        </header>

        <div class="grid-container">
            <div class="grid-header">
                <div class="search-mini" style="background:#F4F7FE; padding:8px 15px; border-radius:20px; display: flex; align-items: center;">
                    <i class='bx bx-search' style="color:var(--text-grey);"></i>
                    <input type="text" id="filterTextBox" oninput="onFilterTextBoxChanged()" placeholder="Search Records..." style="border:none; background:transparent; outline:none; margin-left:5px;">
                </div>
            </div>
            <div id="attendanceGrid" class="ag-theme-quartz"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/ag-grid-community/dist/ag-grid-community.min.js"></script>

    <script>
        const th_regId = "<?php echo $loggedUserId; ?>";
        let gridApi = null;

        const columnDefs = [
            { headerName: 'Date', field: 'att_date', flex: 1, filter: true, sort: 'desc' },
            { headerName: 'Time', field: 'att_timerecorded', flex: 0.8 },
            { headerName: 'Course', field: 'course_id', flex: 1, filter: true },
            { headerName: 'Student Name', field: 'stu_name', flex: 1.5, filter: true },
            { headerName: 'Reg ID', field: 'stu_regId', flex: 1, filter: true },
            { 
                headerName: 'Type', 
                field: 'att_type', 
                flex: 0.8,
                cellRenderer: params => `<span class="type-badge">${params.value}</span>`
            },
            { 
                headerName: 'Status', 
                field: 'att_status',
                flex: 1,
                filter: true,
                cellRenderer: params => {
                    if (params.value === 'Present') {
                        return `<span class="status-badge status-present">Present</span>`;
                    } else {
                        return `<span class="status-badge status-absent">Absent</span>`;
                    }
                }
            }
        ];

        const gridOptions = {
            columnDefs: columnDefs,
            rowData: [],
            defaultColDef: {
                sortable: true,
                resizable: true,
            },
            rowHeight: 60,
            headerHeight: 50,
            pagination: true,
            paginationPageSize: 15
        };

        function onFilterTextBoxChanged() {
            gridApi.setGridOption('quickFilterText', document.getElementById('filterTextBox').value);
        }

        function exportToCSV() {
            if(gridApi) {
                gridApi.exportDataAsCsv({ fileName: 'Attendance_Report.csv' });
            }
        }

        function fetchAllAttendance() {
            const url = `api.php?action=getAllAttendance&th_regId=${th_regId}`;

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if(gridApi) {
                        gridApi.setGridOption('rowData', data);
                    }
                })
                .catch(err => console.error('Error fetching attendance report:', err));
        }

        document.addEventListener('DOMContentLoaded', () => {
            const gridDiv = document.querySelector('#attendanceGrid');
            gridApi = agGrid.createGrid(gridDiv, gridOptions);
            fetchAllAttendance();
        });
    </script>
</body>
</html>