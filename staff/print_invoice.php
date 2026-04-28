<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    exit('غير مصرح');
}
require_once '../config/Database.php';
$db = new Database();
$pdo = $db->getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    exit('رقم الفاتورة غير صحيح.');
}

$stmt = $pdo->prepare("SELECT * FROM invoices WHERE id = ?");
$stmt->execute([$id]);
$inv = $stmt->fetch();

if (!$inv) {
    exit('الفاتورة غير موجودة.');
}

// تنسيق العملة
$currency_symbol = $inv['currency'] === 'USD' ? '$' : 'ل.س';
$amount_formatted = number_format($inv['amount'], 2);
$status_text = $inv['status'] == 'paid' ? 'مدفوعة' : ($inv['status'] == 'unpaid' ? 'غير مدفوعة' : 'ملغاة');
$status_class = $inv['status'] == 'paid' ? '#166534' : ($inv['status'] == 'unpaid' ? '#991b1b' : '#475569');
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>فاتورة رقم <?= $inv['id'] ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Tajawal', Arial, sans-serif;
            background: #f1f5f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .invoice-wrapper {
            max-width: 800px;
            width: 100%;
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            animation: fadeIn 0.4s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .invoice-header {
            background: linear-gradient(135deg, #0ea5e9, #059669);
            color: white;
            padding: 1.5rem 2rem;
            text-align: center;
        }
        .invoice-header h1 {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        .invoice-header p {
            opacity: 0.9;
        }
        .invoice-body {
            padding: 2rem;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px dashed #e2e8f0;
        }
        .info-label {
            font-weight: 700;
            color: #334155;
        }
        .info-value {
            color: #1e293b;
        }
        .amount-box {
            background: #f8fafc;
            padding: 1rem;
            border-radius: 16px;
            margin: 1.5rem 0;
            text-align: center;
            border-right: 4px solid #0ea5e9;
        }
        .amount-label {
            font-size: 1rem;
            color: #64748b;
        }
        .amount-value {
            font-size: 2.2rem;
            font-weight: 800;
            color: #0f172a;
            direction: ltr;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 40px;
            font-weight: bold;
            background: <?= $inv['status'] == 'paid' ? '#dcfce7' : ($inv['status'] == 'unpaid' ? '#fee2e2' : '#f1f5f9') ?>;
            color: <?= $status_class ?>;
        }
        .footer {
            text-align: center;
            padding: 1rem 2rem;
            background: #f8fafc;
            color: #64748b;
            font-size: 0.85rem;
            border-top: 1px solid #e2e8f0;
        }
        .print-btn {
            display: block;
            width: 200px;
            margin: 20px auto 0;
            padding: 10px;
            background: #0ea5e9;
            color: white;
            border: none;
            border-radius: 40px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            transition: 0.2s;
        }
        .print-btn:hover {
            background: #0284c7;
            transform: scale(1.02);
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .print-btn {
                display: none;
            }
            .invoice-wrapper {
                box-shadow: none;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>
<div class="invoice-wrapper">
    <div class="invoice-header">
        <h1>🏥 المركز الصحي المتقدم</h1>
        <p>فاتورة رسمية</p>
    </div>
    <div class="invoice-body">
        <div class="info-row">
            <span class="info-label">رقم الفاتورة:</span>
            <span class="info-value">#<?= $inv['id'] ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">التاريخ:</span>
            <span class="info-value"><?= date('Y-m-d', strtotime($inv['created_at'])) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">اسم المريض:</span>
            <span class="info-value"><?= htmlspecialchars($inv['patient_name']) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">الوصف:</span>
            <span class="info-value"><?= nl2br(htmlspecialchars($inv['description'])) ?></span>
        </div>
        <div class="amount-box">
            <div class="amount-label">المبلغ الإجمالي</div>
            <div class="amount-value"><?= $amount_formatted ?> <?= $currency_symbol ?></div>
        </div>
        <div class="info-row">
            <span class="info-label">حالة الدفع:</span>
            <span class="status-badge"><?= $status_text ?></span>
        </div>
    </div>
    <div class="footer">
        <p>شكراً لثقتكم بنا - المركز الصحي المتقدم</p>
        <p>هذه فاتورة إلكترونية صالحة قانونياً</p>
    </div>
    <button class="print-btn" onclick="window.print();"><i class="fas fa-print"></i> طباعة الفاتورة</button>
</div>
</body>
</html>