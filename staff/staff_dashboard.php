<?php
require_once 'includes/auth_check_staff.php';
require_once '../config/Database.php';

$database = new Database();
$pdo = $database->getConnection();

$staff_id = $_SESSION['user_id'];
$staff_name = $_SESSION['user_name'];

$page = isset($_GET['page']) ? $_GET['page'] : 'departments';
$allowed_pages = ['departments', 'invoices', 'surveys', 'profile'];
if (!in_array($page, $allowed_pages)) {
    $page = 'departments';
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم الموظف - المركز الصحي المتقدم</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Tajawal', sans-serif; background: #f0f4f8; color: #1e293b; direction: rtl; }
        .dashboard-container { display: flex; min-height: 100vh; }
        
        .sidebar { width: 280px; background: linear-gradient(180deg, #0f172a, #1e293b); color: white; padding: 2rem 1.5rem; position: fixed; height: 100vh; overflow-y: auto; }
        .sidebar h2 { font-size: 1.5rem; text-align: center; margin-bottom: 2rem; border-bottom: 1px solid #334155; padding-bottom: 0.5rem; }
        .sidebar nav a { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: #cbd5e1; text-decoration: none; border-radius: 12px; margin-bottom: 8px; transition: 0.3s; }
        .sidebar nav a i { width: 24px; }
        .sidebar nav a:hover, .sidebar nav a.active { background: #0ea5e9; color: white; }
        .sidebar .logout-link { margin-top: 2rem; border-top: 1px solid #334155; padding-top: 1rem; }
        .sidebar .logout-link a { color: #f87171; }
        .main-content { flex: 1; margin-right: 280px; padding: 2rem; }
        .header { background: white; border-radius: 24px; padding: 1rem 2rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .welcome h2 { font-size: 1.5rem; }
        .user-info { display: flex; align-items: center; gap: 12px; }
        .user-avatar { width: 45px; height: 45px; background: linear-gradient(135deg, #0ea5e9, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; font-weight: bold; }
         .alert { padding: 12px; border-radius: 12px; margin-bottom: 20px; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        @media (max-width: 768px) { .sidebar { display: none; } .main-content { margin-right: 0; } }
    </style>
</head>
<body>
<div class="dashboard-container">
    
    <div class="sidebar">
        <h2>🏥 المركز الصحي</h2>
        <nav>
            <a href="?page=departments" class="<?= $page == 'departments' ? 'active' : '' ?>"><i class="fas fa-building"></i> الأقسام</a>
            <a href="?page=invoices" class="<?= $page == 'invoices' ? 'active' : '' ?>"><i class="fas fa-file-invoice-dollar"></i> الفواتير</a>
            <a href="?page=surveys" class="<?= $page == 'surveys' ? 'active' : '' ?>"><i class="fas fa-poll"></i> الاستبيانات</a>
            <a href="?page=profile" class="<?= $page == 'profile' ? 'active' : '' ?>"><i class="fas fa-user-circle"></i> ملفي الشخصي</a>
        </nav>
        <div class="logout-link">
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a>
        </div>
    </div>

    
    <div class="main-content">
        <div class="header">
            <div class="welcome">
                <h2>مرحباً، <?= htmlspecialchars($staff_name) ?></h2>
                <p>لوحة تحكم الموظف</p>
            </div>
            <div class="user-info">
                <div class="user-avatar"><?= mb_substr($staff_name, 0, 1) ?></div>
            </div>
        </div>

        
        <?php if (isset($_SESSION['staff_message'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['staff_message']) ?></div>
            <?php unset($_SESSION['staff_message']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['staff_error'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($_SESSION['staff_error']) ?></div>
            <?php unset($_SESSION['staff_error']); ?>
        <?php endif; ?>

        
        <?php include "modules/{$page}.php"; ?>
    </div>
</div>
</body>
</html>