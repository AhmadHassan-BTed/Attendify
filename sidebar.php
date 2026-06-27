<?php 
$currentPage = basename($_SERVER['PHP_SELF']); 
?>

<nav class="sidebar">
    <div class="logo">
        Attendance Portal
    </div>

    <ul class="nav-links">
        <li><a href="dashboard.php" class="<?php echo ($currentPage == 'dashboard.php') ? 'active' : ''; ?>"><i class='bx bxs-dashboard'></i> Dashboard</a></li>
        <li><a href="portal.php" class="<?php echo ($currentPage == 'portal.php') ? 'active' : ''; ?>"><i class='bx bx-bookmark-plus'></i> Mark Attendance</a></li>
        <li><a href="leaveRequests.php" class="<?php echo ($currentPage == 'leaveRequests.php') ? 'active' : ''; ?>"><i class='bx bx-paperclip'></i> Leave Requests</a></li>
        <li><a href="requestsHistory.php" class="<?php echo ($currentPage == 'requestsHistory.php') ? 'active' : ''; ?>"><i class='bx bx-timer'></i> Requests History</a></li>
        <li><a href="attendanceReport.php" class="<?php echo ($currentPage == 'attendanceReport.php') ? 'active' : ''; ?>"><i class='bx bx-note'></i> Attendance Report</a></li>
    </ul>

    <ul class="nav-links" style="flex-grow: 0;">
        <li><a href="login.php" class="logout"><i class='bx bx-log-out'></i> Log out</a></li>
    </ul>
</nav>