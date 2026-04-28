<?php
require_once 'includes/auth_check.php';
$current_page_name = 'settings'; 

require_once '../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

$message = '';
$message_type = '';


if ($_POST) {
    try {
        
        $updates = [
            'default_theme' => trim($_POST['default_theme']),
            'invoice_format' => trim($_POST['invoice_format']),
            'center_name_ar' => trim($_POST['center_name_ar'])
        ];

        $conn->beginTransaction();

        
        $stmt = $conn->prepare("UPDATE settings SET value = ? WHERE key_name = ?");

        foreach ($updates as $key => $value) {
            $stmt->execute([$value, $key]);
        }

        $conn->commit();
        $message = "تم تحديث إعدادات النظام بنجاح.";
        $message_type = "success";

    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        $message = "حدث خطأ أثناء التحديث: " . $e->getMessage();
        $message_type = "error";
    }
}


$settings_query = $conn->query("SELECT key_name, value, description FROM settings");
$settings_array = $settings_query->fetchAll(PDO::FETCH_ASSOC);

$settings = [];
foreach ($settings_array as $row) {
    $settings[$row['key_name']] = [
        'value' => $row['value'],
        'description' => $row['description']
    ];
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعدادات النظام - المركز الصحي المتقدم</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
    
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    .main-content { margin-right: 260px; padding: 30px; }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
    
    .page-title { 
        font-size: 1.8rem; 
        font-weight: 700; 
        color: var(--text-color); 
        display: flex; 
        align-items: center; 
        gap: 12px; 
    }
    
    .settings-card { 
        background: var(--card-bg); 
        border-radius: 18px; 
        padding: 30px; 
        box-shadow: 0 6px 15px var(--card-shadow); 
        max-width: 800px; 
        margin: 0 auto; 
    }
    
    .section-title { 
        font-size: 1.4rem; 
        margin-bottom: 25px; 
        color: var(--text-color); 
        font-weight: 700; 
    }
    
    .form-group { margin-bottom: 20px; }
    
    .form-group label { 
        display: block; 
        margin-bottom: 8px; 
        font-weight: 600; 
        color: var(--text-color); 
    }
    
    .form-group input, .form-group select {
        width: 100%;
        padding: 12px;
        border: 2px solid var(--border-color); 
        border-radius: 10px;
        font-family: 'Tajawal', sans-serif;
        font-size: 1rem;
        background: var(--card-bg);
        color: var(--text-color);
    }
    
    .form-group input:focus, .form-group select:focus {
        outline: none;
        border-color: #0ea5e9;
    }
    
    .form-actions { display: flex; justify-content: flex-end; margin-top: 30px; }
    
    .btn-submit {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        font-size: 1rem;
    }
    
    .message { padding: 12px; border-radius: 10px; margin-bottom: 25px; text-align: center; font-weight: 500; }
    .message.success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .message.error { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
    
    .form-group small {
        color: var(--muted-text) !important;
    }

    
    @media (max-width: 768px) {
        .main-content { margin-right: 80px; }
        .settings-card { padding: 20px; }
    }
</style>
    <?php include 'includes/theme_logic.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
</head>
<body>
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">
                <span class="icon">⚙️</span>
                إدارة إعدادات النظام
            </h1>
        </div>

        <?php if ($message): ?>
            <div class="message <?= $message_type ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="settings-card">
            <h2 class="section-title">إعدادات عامة وتخصيص</h2>
            <form method="POST">
                
                <div class="form-group">
                    <label for="center_name_ar">اسم المركز الرسمي (للفواتير/الرأسية)</label>
                    <input type="text" id="center_name_ar" name="center_name_ar" 
                           value="<?= htmlspecialchars($settings['center_name_ar']['value'] ?? '') ?>" required>
                    <small style="color: #64748b; display: block; margin-top: 5px;"><?= htmlspecialchars($settings['center_name_ar']['description'] ?? '') ?></small>
                </div>

                <div class="form-group">
                    <label for="default_theme">الوضع الافتراضي للواجهة</label>
                    <select id="default_theme" name="default_theme" required>
                        <option value="light" <?= ($settings['default_theme']['value'] ?? '') === 'light' ? 'selected' : '' ?>>نهاري (Light)</option>
                        <option value="dark" <?= ($settings['default_theme']['value'] ?? '') === 'dark' ? 'selected' : '' ?>>مظلم (Dark)</option>
                    </select>
                     <small style="color: #64748b; display: block; margin-top: 5px;"><?= htmlspecialchars($settings['default_theme']['description'] ?? '') ?></small>
                </div>

                <div class="form-group">
                    <label for="invoice_format">تنسيق طباعة الفواتير</label>
                    <select id="invoice_format" name="invoice_format" required>
                        <option value="A4_Standard" <?= ($settings['invoice_format']['value'] ?? '') === 'A4_Standard' ? 'selected' : '' ?>>A4 (قياسي)</option>
                        <option value="Thermal_Small" <?= ($settings['invoice_format']['value'] ?? '') === 'Thermal_Small' ? 'selected' : '' ?>>طابعة حرارية (صغير)</option>
                    </select>
                    <small style="color: #64748b; display: block; margin-top: 5px;"><?= htmlspecialchars($settings['invoice_format']['description'] ?? '') ?></small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">حفظ الإعدادات</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const themeSelect = document.getElementById('default_theme');
            const body = document.body;

            
            themeSelect.addEventListener('change', function() {
                const selectedTheme = this.value;

                if (selectedTheme === 'dark') {
                    body.classList.add('dark-mode');
                    localStorage.setItem('theme', 'dark');
                } else {
                    body.classList.remove('dark-mode');
                    localStorage.setItem('theme', 'light');
                }
            });
        });
    </script>
</body>
</html>