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

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        :root {
            --primary-color: #1D2254;
            --bg-color: #F4F7FE;
            --white: #ffffff;
            --text-grey: #A3AED0;
            --text-dark: #2B3674;
            --green: #05CD99;
            --red: #EE5D50;
            --blue: #4318FF;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }

        body { background-color: var(--bg-color); height: 100vh; overflow: hidden; display: flex; }

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

        /* Dashboard Grid Layout */
        .dash-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        /* Summary Cards */
        .summary-cards-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            grid-column: span 3;
        }
        
        .stat-card {
            background: var(--white);
            border-radius: 20px;
            padding: 20px;
            display: flex;
            align-items: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.02);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 15px;
        }
        
        .icon-blue { background: rgba(67, 24, 255, 0.1); color: var(--blue); }
        .icon-green { background: rgba(5, 205, 153, 0.1); color: var(--green); }
        .icon-red { background: rgba(238, 93, 80, 0.1); color: var(--red); }

        .stat-details h4 { font-size: 0.85rem; color: var(--text-grey); font-weight: 500; }
        .stat-details h2 { font-size: 1.4rem; color: var(--text-dark); font-weight: 700; }

        /* Chart Containers */
        /* Sidebar Styling */
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
        
        .chart-card {
            background: var(--white);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.02);
        }
        
        .col-span-2 { grid-column: span 2; }
        .col-span-1 { grid-column: span 1; }
        .col-span-3 { grid-column: span 3; }
        
        .chart-title { font-size: 1.1rem; color: var(--text-dark); font-weight: 600; margin-bottom: 15px; }

    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <header>
            <div class="header-left">
                <h2>Dashboard Overview</h2>
                <p>Welcome back! Here is your teaching attendance summary.</p>
            </div>
            <div class="header-right">
                <button class="icon-btn" onclick="fetchDashboardData()"><i class='bx bx-refresh' style="font-size: 1.2rem;"></i></button>
                <button class="icon-btn"><i class='bx bx-bell'></i></button>
            </div>
        </header>

        <div class="summary-cards-container">
            <div class="stat-card">
                <div class="stat-icon icon-blue"><i class='bx bx-chalkboard'></i></div>
                <div class="stat-details">
                    <h4>Total Classes Taught</h4>
                    <h2 id="totalClasses">0</h2>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-green"><i class='bx bx-user-check'></i></div>
                <div class="stat-details">
                    <h4>Overall Present</h4>
                    <h2 id="totalPresent">0</h2>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-red"><i class='bx bx-user-x'></i></div>
                <div class="stat-details">
                    <h4>Overall Absent</h4>
                    <h2 id="totalAbsent">0</h2>
                </div>
            </div>
        </div>

        <div class="dash-grid">
            <div class="chart-card col-span-2">
                <h3 class="chart-title">Attendance Trends (Last 7 Days)</h3>
                <div id="trendChart"></div>
            </div>
            
            <div class="chart-card col-span-1">
                <h3 class="chart-title">Overall Status</h3>
                <div id="statusChart"></div>
            </div>

            <div class="chart-card col-span-3">
                <h3 class="chart-title">Attendance by Course (%)</h3>
                <div id="courseChart"></div>
            </div>
        </div>
    </div>

    <script>
        const th_regId = "<?php echo $loggedUserId; ?>";
        let trendChartObj, statusChartObj, courseChartObj;

        function fetchDashboardData() {
            fetch(`api.php?action=getDashboardData&th_regId=${th_regId}`)
                .then(res => res.json())
                .then(data => {
                    if(data.error) {
                        console.error(data.error);
                        return;
                    }
                    updateCards(data.totals);
                    renderStatusChart(data.status);
                    renderCourseChart(data.courses);
                    renderTrendChart(data.trends);
                })
                .catch(err => console.error("Error fetching dashboard data:", err));
        }

        function updateCards(totals) {
            document.getElementById('totalClasses').textContent = totals.total_classes || 0;
            document.getElementById('totalPresent').textContent = totals.total_present || 0;
            document.getElementById('totalAbsent').textContent = totals.total_absent || 0;
        }

        function renderStatusChart(statusData) {
            const options = {
                series: [parseInt(statusData.present || 0), parseInt(statusData.absent || 0)],
                labels: ['Present', 'Absent'],
                chart: { type: 'donut', height: 280, fontFamily: 'Poppins, sans-serif' },
                colors: ['#05CD99', '#EE5D50'],
                dataLabels: { enabled: false },
                plotOptions: { pie: { donut: { size: '70%' } } },
                legend: { position: 'bottom' }
            };

            if(statusChartObj) statusChartObj.destroy();
            statusChartObj = new ApexCharts(document.querySelector("#statusChart"), options);
            statusChartObj.render();
        }

        function renderCourseChart(courseData) {
            const categories = courseData.map(item => item.course_id);
            const percentages = courseData.map(item => {
                const total = parseInt(item.present) + parseInt(item.absent);
                return total > 0 ? Math.round((parseInt(item.present) / total) * 100) : 0;
            });

            const options = {
                series: [{ name: 'Attendance %', data: percentages }],
                chart: { type: 'bar', height: 250, fontFamily: 'Poppins, sans-serif', toolbar: { show: false } },
                colors: ['#4318FF'],
                plotOptions: { bar: { borderRadius: 6, columnWidth: '40%' } },
                dataLabels: { enabled: false },
                xaxis: { categories: categories },
                yaxis: { max: 100 }
            };

            if(courseChartObj) courseChartObj.destroy();
            courseChartObj = new ApexCharts(document.querySelector("#courseChart"), options);
            courseChartObj.render();
        }

        function renderTrendChart(trendData) {
            const dates = trendData.map(item => item.att_date);
            const presents = trendData.map(item => item.present);
            const absents = trendData.map(item => item.absent);

            const options = {
                series: [
                    { name: 'Present', data: presents },
                    { name: 'Absent', data: absents }
                ],
                chart: { type: 'area', height: 280, fontFamily: 'Poppins, sans-serif', toolbar: { show: false } },
                colors: ['#05CD99', '#EE5D50'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                xaxis: { categories: dates },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 100] } }
            };

            if(trendChartObj) trendChartObj.destroy();
            trendChartObj = new ApexCharts(document.querySelector("#trendChart"), options);
            trendChartObj.render();
        }

        document.addEventListener('DOMContentLoaded', fetchDashboardData);
    </script>
</body>
</html>