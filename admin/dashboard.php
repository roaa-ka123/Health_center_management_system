<?php

$current_page_name = 'dashboard';
require_once 'includes/auth_check.php';
require_once '../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

$stats = [
    'doctors' => $conn->query("SELECT COUNT(*) FROM doctors")->fetchColumn(),
    'patients' => $conn->query("SELECT COUNT(*) FROM patients")->fetchColumn(),
    'staff' => $conn->query("SELECT COUNT(*) FROM staff")->fetchColumn(),
    'articles' => $conn->query("SELECT COUNT(*) FROM articles")->fetchColumn(),
    'specialists' => $conn->query("SELECT COUNT(*) FROM specialists")->fetchColumn(),
    'surveys' => $conn->query("SELECT COUNT(*) FROM surveys")->fetchColumn(),
    'departments' => $conn->query("SELECT COUNT(*) FROM departments")->fetchColumn()
];


$stmt = $conn->query("SELECT id, name, description, icon, status FROM departments ORDER BY created_at ASC");
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - المركز الصحي المتقدم</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <?php include 'includes/theme_logic.php'; ?>
    <?php include 'includes/sidebar.php'; ?> 

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        

        
        .main-content {
            margin-right: 260px; 
            padding: 30px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .welcome {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-color);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--header-bg);
            padding: 12px 20px;
            border-radius: 16px;
            box-shadow: 0 4px 12px var(--header-shadow);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #0ea5e9, #059669);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        
        .theme-toggle {
            background: var(--toggle-bg);
            border: none;
            width: 50px;
            height: 26px;
            border-radius: 13px;
            position: relative;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .theme-toggle::before {
            content: '☀️';
            position: absolute;
            top: 2px;
            left: 2px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--toggle-thumb);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            transition: transform 0.3s ease;
        }

        .dark-mode .theme-toggle::before {
            content: '🌙';
            transform: translateX(24px);
        }

        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: 18px;
            padding: 20px;
            box-shadow: 0 6px 15px var(--card-shadow);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-top: 4px solid #0ea5e9;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px var(--card-shadow);
        }

        .stat-card.doctors { border-top-color: #0ea5e9; }
        .stat-card.patients { border-top-color: #8b5cf6; }
        .stat-card.staff { border-top-color: #059669; }
        .stat-card.articles { border-top-color: #ec4899; }
        .stat-card.specialists { border-top-color: #f59e0b; }
        .stat-card.surveys { border-top-color: #ef4444; }
        .stat-card.departments { border-top-color: #8b5cf6; }

        .stat-icon {
            font-size: 28px;
            margin-bottom: 12px;
        }

        .stat-title {
            font-size: 1rem;
            color: var(--stat-title-color);
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-color);
        }

        
        .departments-section {
            background: var(--card-bg);
            border-radius: 18px;
            padding: 25px;
            margin-bottom: 40px;
            box-shadow: 0 6px 15px var(--card-shadow);
        }

        .departments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .department-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 12px var(--card-shadow);
            transition: transform 0.2s ease;
        }

        .department-card:hover {
            transform: translateY(-3px);
        }

        .department-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }

        .department-icon {
            font-size: 28px;
        }

        .department-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-color);
        }

        .department-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-right: auto;
        }

        .status-active {
            background: var(--department-active);
            color: var(--department-active-text);
        }

        .status-inactive {
            background: var(--department-inactive);
            color: var(--department-inactive-text);
        }

        .department-description {
            color: var(--muted-text);
            line-height: 1.6;
            font-size: 0.95rem;
        }

        
        .recent-activity {
            background: var(--card-bg);
            border-radius: 18px;
            padding: 25px;
            box-shadow: 0 6px 15px var(--card-shadow);
        }

        .section-title {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: var(--text-color);
            font-weight: 700;
        }

        .recent-activity p {
            color: var(--muted-text);
        }

        
        @media (max-width: 992px) {
            .main-content {
                margin-right: 80px;
            }
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .departments-grid {
                grid-template-columns: 1fr;
            }
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
            .header-actions {
                width: 100%;
                justify-content: space-between;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="header">
            <div class="welcome">مرحباً بك في لوحة التحكم</div>
            <div class="header-actions">
                <button class="theme-toggle" id="theme-toggle" aria-label="تبديل الوضع"></button>
                <div class="user-info">
                    <div class="user-avatar">
                        <?= mb_substr($_SESSION['user_name'], 0, 1, 'UTF-8') ?>
                    </div>
                    <div class="user-name"><?= htmlspecialchars($_SESSION['user_name']) ?></div>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card doctors">
                <div class="stat-icon">👨‍⚕️</div>
                <div class="stat-title">عدد الأطباء</div>
                <div class="stat-value"><?= $stats['doctors'] ?></div>
            </div>
            <div class="stat-card specialists">
                <div class="stat-icon">👩‍⚕️</div>
                <div class="stat-title">عدد الأخصائيين</div>
                <div class="stat-value"><?= $stats['specialists'] ?></div>
            </div>
            <div class="stat-card patients">
                <div class="stat-icon">🧑‍🤝‍🧑</div>
                <div class="stat-title">عدد المرضى</div>
                <div class="stat-value"><?= $stats['patients'] ?></div>
            </div>
            <div class="stat-card staff">
                <div class="stat-icon">👥</div>
                <div class="stat-title">عدد الموظفين</div>
                <div class="stat-value"><?= $stats['staff'] ?></div>
            </div>
            <div class="stat-card departments">
                <div class="stat-icon">🏢</div>
                <div class="stat-title">أقسام المركز</div>
                <div class="stat-value"><?= $stats['departments'] ?></div>
            </div>
            <div class="stat-card articles">
                <div class="stat-icon">📰</div>
                <div class="stat-title">عدد المقالات</div>
                <div class="stat-value"><?= $stats['articles'] ?></div>
            </div>
            <div class="stat-card surveys">
                <div class="stat-icon">📊</div>
                <div class="stat-title">عدد الاستبيانات</div>
                <div class="stat-value"><?= $stats['surveys'] ?></div>
            </div>
        </div>

        <div class="departments-section">
            <div class="section-title">أقسام المركز الصحي</div>
            <div class="departments-grid">
                <?php foreach ($departments as $dept): ?>
                <div class="department-card">
                    <div class="department-header">
                        <span class="department-icon"><?= htmlspecialchars($dept['icon']) ?></span>
                        <h3 class="department-name"><?= htmlspecialchars($dept['name']) ?></h3>
                        <span class="department-status status-<?= $dept['status'] ?>">
                            <?= $dept['status'] === 'active' ? 'نشط' : 'غير نشط' ?>
                        </span>
                    </div>
                    <p class="department-description"><?= htmlspecialchars($dept['description']) ?></p>
                </div>
                <?php endforeach; ?>
                
                <?php if (empty($departments)): ?>
                <div class="department-card" style="text-align: center; padding: 40px;">
                    <div class="department-icon" style="font-size: 48px; margin-bottom: 20px;">🏢</div>
                    <p style="color: var(--muted-text);">لا توجد أقسام مسجلة حاليًا</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="recent-activity">
            <div class="section-title">النشاطات الأخيرة</div>
            <p>هنا يمكنك عرض النشاطات الأخيرة للمستخدمين والإجراءات التي تمت في النظام.</p>
        </div>
    </div>
</body>
</html>