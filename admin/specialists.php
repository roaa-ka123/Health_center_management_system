<?php
require_once 'includes/auth_check.php';
$current_page_name = 'specialists';

require_once '../config/Database.php';
$database = new Database();
$conn = $database->getConnection();


if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    
    $status = match($action) {
        'approve' => 'approved',
        'reject' => 'rejected',
        'suspend' => 'suspended',
        'reactivate' => 'approved', 
        default => 'pending'
    };
    
    $stmt = $conn->prepare("UPDATE specialists SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    header('Location: specialists.php');
    exit;
}


$stmt = $conn->query("SELECT id, full_name, email, phone, field, status FROM specialists ORDER BY created_at DESC");
$specialists = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الأخصائيين - المركز الصحي المتقدم</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700&display=swap" rel="stylesheet">
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

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .page-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--text-color);
    }

    
    .specialists-table {
        width: 100%;
        border-collapse: collapse;
        background: var(--card-bg);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 15px var(--card-shadow);
    }

    .specialists-table th {
        background: var(--border-color);
        padding: 16px;
        text-align: right;
        font-weight: 700;
        color: var(--text-color);
    }

    .specialists-table td {
        padding: 16px;
        text-align: right;
        border-bottom: 1px solid var(--border-color);
    }

    .specialists-table tr:last-child td {
        border-bottom: none;
    }

    .status {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .status-pending { background: #fef3c7; color: #d97706; }
    .status-approved { background: #dcfce7; color: #166534; }
    .status-rejected, .status-suspended { background: #fee2e2; color: #dc2626; }

    .actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-action {
        padding: 8px 14px;
        border-radius: 8px;
        color: white;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .approve { background: linear-gradient(135deg, #10b981, #047857); }
    .reject { background: linear-gradient(135deg, #ef4444, #b91c1c); }
    .suspend { background: linear-gradient(135deg, #f59e0b, #b45309); }
    .reactivate { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

    
    .empty-state {
        background: var(--card-bg);
        padding: 40px;
        border-radius: 16px;
        text-align: center;
        box-shadow: 0 4px 15px var(--card-shadow);
    }

    .empty-state h3 {
        color: var(--text-color);
        margin-bottom: 10px;
    }

    .empty-state p {
        color: var(--muted-text);
    }

    
    @media (max-width: 768px) {
        .main-content {
            margin-right: 80px;
        }
        .page-header {
            flex-direction: column;
            align-items: stretch;
        }
        .actions {
            justify-content: center;
        }
    }
</style>
    <?php include 'includes/theme_logic.php'; ?>
    <?php include 'includes/sidebar.php' ?>
</head>
<body>
    
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">إدارة الأخصائيين (<?= count($specialists) ?>)</h1>
        </div>

        
        <?php if (count($specialists) > 0): ?>
        <table class="specialists-table">
            <thead>
                <tr>
                    <th>الاسم الكامل</th>
                    <th>التخصص</th>
                    <th>رقم الجوال</th>
                    <th>البريد الإلكتروني</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($specialists as $spec): ?>
                <tr>
                    <td><?= htmlspecialchars($spec['full_name']) ?></td>
                    <td><?= htmlspecialchars($spec['field']) ?></td>
                    <td><?= htmlspecialchars($spec['phone'] ?? 'غير متوفر') ?></td>
                    <td><?= htmlspecialchars($spec['email']) ?></td>
                    <td>
                        <span class="status status-<?= $spec['status'] ?>"><?= 
                            match($spec['status']) {
                                'pending' => 'بانتظار الموافقة',
                                'approved' => 'معتمد',
                                'rejected' => 'مرفوض',
                                'suspended' => 'معلق',
                                default => 'غير معروف'
                            }
                        ?></span>
                    </td>
                    <td class="actions">
                        <?php if ($spec['status'] === 'pending'): ?>
                            
                            <a href="?action=approve&id=<?= $spec['id'] ?>" class="btn-action approve">موافقة</a>
                            <a href="?action=reject&id=<?= $spec['id'] ?>" class="btn-action reject">رفض</a>
                        <?php elseif ($spec['status'] === 'approved'): ?>
                            
                            <a href="?action=suspend&id=<?= $spec['id'] ?>" class="btn-action suspend">تعليق</a>
                        <?php elseif ($spec['status'] === 'suspended'): ?>
                            
                            <a href="?action=reactivate&id=<?= $spec['id'] ?>" class="btn-action reactivate">إعادة التفعيل</a>
                        <?php elseif ($spec['status'] === 'rejected'): ?>
                            
                            <a href="?action=approve&id=<?= $spec['id'] ?>" class="btn-action approve">إعادة الموافقة</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <h3>لا يوجد أخصائيين مسجلين حاليًا</h3>
            <p>يمكنك إضافة أخصائيين عبر قاعدة البيانات مباشرةً باستخدام أدوات مثل phpMyAdmin.</p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
