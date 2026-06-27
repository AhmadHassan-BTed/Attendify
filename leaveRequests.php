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
    <title>Leave Requests - AU Attendance Portal</title>

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
            --yellow: #FFB547;
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

        .dot { height: 10px; width: 10px; border-radius: 50%; display: inline-block; margin-right: 5px; }
        .accepted-green-dot { background-color: var(--green); }
        .rejected-red-dot { background-color: var(--red); }
        .pending-yellow-dot { background-color: var(--yellow); }

        .action-btns { display: flex; gap: 8px; align-items: center; height: 100%; }
        .btn-accept-grid { background: var(--green); color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 0.75rem; transition: 0.2s; }
        .btn-reject-grid { background: var(--red); color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 0.75rem; transition: 0.2s; }
        .btn-accept-grid:hover, .btn-reject-grid:hover { opacity: 0.8; }
        .btn-disabled { background: #e0e0e0; cursor: not-allowed; color: #a0a0a0; }

    </style>
</head>
<body>

    <nav class="sidebar">
        <?php include 'sidebar.php'; ?>

    </nav>

    <div class="main-content">
        <header>
            <div class="header-left">
                <h2>Today's Leave Requests</h2>
                <p>Manage leave applications for <span id="headerDateDisplay" style="font-weight: 600; color: var(--text-dark);"></span></p>
            </div>
            <div class="header-right">
                <button class="icon-btn" onclick="fetchTodayLeaves()"><i class='bx bx-refresh' style="font-size: 1.2rem;"></i></button>
                <button class="icon-btn"><i class='bx bx-bell'></i></button>
            </div>
        </header>

        <div class="grid-container">
            <div class="grid-header">
                <div class="search-mini" style="background:#F4F7FE; padding:8px 15px; border-radius:20px; display: flex; align-items: center;">
                    <i class='bx bx-search' style="color:var(--text-grey);"></i>
                    <input type="text" id="filterTextBox" oninput="onFilterTextBoxChanged()" placeholder="Search Requests..." style="border:none; background:transparent; outline:none; margin-left:5px;">
                </div>
            </div>
            <div id="leavesGrid" class="ag-theme-quartz"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/ag-grid-community/dist/ag-grid-community.min.js"></script>

    <script>
        const th_regId = "<?php echo $loggedUserId; ?>";
        let gridApi = null;
        
        const now = new Date();
        const dateStr = now.toLocaleDateString('en-CA'); 
        document.getElementById('headerDateDisplay').textContent = now.toDateString();

        const columnDefs = [
            { headerName: 'Student Name', field: 'stu_name', flex: 1.2, filter: true },
            { headerName: 'Reg ID', field: 'stu_regId', flex: 0.8, filter: true },
            { headerName: 'Course', field: 'course_id', flex: 0.8, filter: true },
            { headerName: 'Title', field: 'leave_title', flex: 1 },
            { headerName: 'Message', field: 'leave_message', flex: 2, tooltipField: 'leave_message' },
            { 
                headerName: 'Status', 
                field: 'request_status',
                flex: 0.8,
                cellRenderer: params => {
                    let dotClass = 'pending-yellow-dot';
                    let statusText = params.value || 'Pending';
                    if (params.value === 'Approved') dotClass = 'accepted-green-dot';
                    else if (params.value === 'Rejected') dotClass = 'rejected-red-dot';

                    return `<div style="display:flex; align-items:center;"><span class="dot ${dotClass}"></span> ${statusText}</div>`;
                }
            },
            {
                headerName: 'Actions',
                field: 'leave_id',
                flex: 1.2,
                cellRenderer: params => {
                    const id = params.value;
                    const status = params.data.request_status;
                    
                    if(status === 'Approved' || status === 'Rejected') {
                        return `<div class="action-btns"><button class="btn-disabled" disabled>Resolved</button></div>`;
                    }

                    return `
                        <div class="action-btns">
                            <button class="btn-accept-grid" onclick="updateLeaveStatus(${id}, 'Approved')">Accept</button>
                            <button class="btn-reject-grid" onclick="updateLeaveStatus(${id}, 'Rejected')">Reject</button>
                        </div>
                    `;
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
            enableBrowserTooltips: true,
            pagination: true,
            paginationPageSize: 10
        };

        function onFilterTextBoxChanged() {
            gridApi.setGridOption('quickFilterText', document.getElementById('filterTextBox').value);
        }

        function fetchTodayLeaves() {
            // Fetch leaves where date matches today and teacher is associated
            const url = `api.php?action=getTodayLeaves&th_regId=${th_regId}&leave_date=${dateStr}`;

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if(gridApi) {
                        gridApi.setGridOption('rowData', data);
                    }
                })
                .catch(err => console.error('Error fetching leaves:', err));
        }

        function updateLeaveStatus(leave_id, status) {
            fetch('api.php?action=updateLeaveStatus', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ leave_id: leave_id, status: status })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    // Refresh the grid to show updated status
                    fetchTodayLeaves();
                } else {
                    alert('Failed to update leave status.');
                }
            })
            .catch(err => console.error(err));
        }

        document.addEventListener('DOMContentLoaded', () => {
            const gridDiv = document.querySelector('#leavesGrid');
            gridApi = agGrid.createGrid(gridDiv, gridOptions);
            fetchTodayLeaves();
        });
    </script>
</body>
</html>