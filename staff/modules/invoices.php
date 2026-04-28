<?php
// معالجة إجراءات الفواتير
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // منع إعادة الإرسال المتكرر (نفس الطلب خلال 3 ثوانٍ)
    if (isset($_SESSION['last_invoice_action']) && $_SESSION['last_invoice_action'] === $action && (time() - $_SESSION['last_invoice_time']) < 3) {
        $_SESSION['staff_error'] = "الرجاء الانتظار قبل تنفيذ هذا الإجراء مرة أخرى.";
        header("Location: staff_dashboard.php?page=invoices");
        exit;
    }
    $_SESSION['last_invoice_action'] = $action;
    $_SESSION['last_invoice_time'] = time();

    if ($action === 'add_invoice') {
        $patient_name = trim($_POST['patient_name']);
        $amount = floatval($_POST['amount']);
        $currency = trim($_POST['currency']);
        $description = trim($_POST['description']);
        $status = $_POST['status'];
        if (empty($patient_name) || $amount <= 0) {
            $_SESSION['staff_error'] = "اسم المريض والمبلغ مطلوبان.";
        } elseif (!in_array($currency, ['USD', 'SYP'])) {
            $_SESSION['staff_error'] = "عملة غير صالحة.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO invoices (staff_id, patient_name, amount, currency, description, status) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$staff_id, $patient_name, $amount, $currency, $description, $status])) {
                $_SESSION['staff_message'] = "تم إضافة الفاتورة بنجاح.";
            } else {
                $_SESSION['staff_error'] = "فشل إضافة الفاتورة.";
            }
        }
        header("Location: staff_dashboard.php?page=invoices");
        exit;
    }
    elseif ($action === 'edit_invoice') {
        $id = $_POST['id'];
        $patient_name = trim($_POST['patient_name']);
        $amount = floatval($_POST['amount']);
        $currency = trim($_POST['currency']);
        $description = trim($_POST['description']);
        $status = $_POST['status'];
        $stmt = $pdo->prepare("UPDATE invoices SET patient_name=?, amount=?, currency=?, description=?, status=? WHERE id=? AND staff_id=?");
        if ($stmt->execute([$patient_name, $amount, $currency, $description, $status, $id, $staff_id])) {
            $_SESSION['staff_message'] = "تم تحديث الفاتورة.";
        } else {
            $_SESSION['staff_error'] = "فشل التحديث.";
        }
        header("Location: staff_dashboard.php?page=invoices");
        exit;
    }
    elseif ($action === 'delete_invoice') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM invoices WHERE id=? AND staff_id=?");
        if ($stmt->execute([$id, $staff_id])) {
            $_SESSION['staff_message'] = "تم حذف الفاتورة.";
        } else {
            $_SESSION['staff_error'] = "فشل الحذف.";
        }
        header("Location: staff_dashboard.php?page=invoices");
        exit;
    }
}

// جلب قائمة الفواتير للموظف الحالي
$invoices = $pdo->prepare("SELECT * FROM invoices WHERE staff_id = ? ORDER BY created_at DESC");
$invoices->execute([$staff_id]);
$invoices_list = $invoices->fetchAll();
?>

<style>
    /* أنماط إضافية لهذه الصفحة فقط */
    .invoice-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .invoice-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.1);
    }
    .btn-gradient {
        background: linear-gradient(135deg, #0ea5e9, #059669);
        transition: all 0.3s;
    }
    .btn-gradient:hover {
        background: linear-gradient(135deg, #0284c7, #047857);
        transform: translateY(-2px);
        box-shadow: 0 5px 12px rgba(0,0,0,0.15);
    }
    .table-hover tbody tr:hover {
        background: #f8fafc;
        transition: 0.2s;
    }
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 40px;
        font-size: 0.75rem;
        font-weight: bold;
    }
    .badge-paid { background: #dcfce7; color: #166534; }
    .badge-unpaid { background: #fee2e2; color: #991b1b; }
    .badge-cancelled { background: #f1f5f9; color: #475569; }
    .amount-display {
        font-weight: 700;
        direction: ltr;
        display: inline-block;
    }
</style>

<div class="card invoice-card" style="background: white; border-radius: 28px; padding: 1.8rem; box-shadow: 0 8px 20px rgba(0,0,0,0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.8rem; flex-wrap: wrap; gap: 15px;">
        <h3 style="border-right: 5px solid #0ea5e9; padding-right: 1rem;"><i class="fas fa-file-invoice-dollar" style="color: #0ea5e9;"></i> إدارة الفواتير</h3>
        <button onclick="openAddInvoiceModal()" class="btn-gradient" style="color: white; border: none; padding: 10px 22px; border-radius: 40px; cursor: pointer; font-weight: bold; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-plus-circle"></i> فاتورة جديدة
        </button>
    </div>
    <div style="overflow-x: auto;">
        <table class="table-hover" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="padding: 15px 12px; text-align: right; border-bottom: 2px solid #e2e8f0;">المريض</th>
                    <th style="padding: 15px 12px; text-align: right; border-bottom: 2px solid #e2e8f0;">المبلغ</th>
                    <th style="padding: 15px 12px; text-align: right; border-bottom: 2px solid #e2e8f0;">الوصف</th>
                    <th style="padding: 15px 12px; text-align: right; border-bottom: 2px solid #e2e8f0;">الحالة</th>
                    <th style="padding: 15px 12px; text-align: right; border-bottom: 2px solid #e2e8f0;">التاريخ</th>
                    <th style="padding: 15px 12px; text-align: right; border-bottom: 2px solid #e2e8f0;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoices_list as $inv): ?>
                <tr>
                    <td style="padding: 14px 12px; border-bottom: 1px solid #e2e8f0;"><?= htmlspecialchars($inv['patient_name']) ?></td>
                    <td style="padding: 14px 12px; border-bottom: 1px solid #e2e8f0;">
                        <span class="amount-display">
                            <?= number_format($inv['amount'], 2) ?>
                            <?= $inv['currency'] === 'USD' ? '$' : 'ل.س' ?>
                        </span>
                    </td>
                    <td style="padding: 14px 12px; border-bottom: 1px solid #e2e8f0;"><?= htmlspecialchars($inv['description']) ?></td>
                    <td style="padding: 14px 12px; border-bottom: 1px solid #e2e8f0;">
                        <span class="badge <?= $inv['status'] == 'paid' ? 'badge-paid' : ($inv['status'] == 'unpaid' ? 'badge-unpaid' : 'badge-cancelled') ?>">
                            <i class="fas <?= $inv['status'] == 'paid' ? 'fa-check-circle' : ($inv['status'] == 'unpaid' ? 'fa-clock' : 'fa-times-circle') ?>"></i>
                            <?= $inv['status'] == 'paid' ? 'مدفوعة' : ($inv['status'] == 'unpaid' ? 'غير مدفوعة' : 'ملغاة') ?>
                        </span>
                    </td>
                    <td style="padding: 14px 12px; border-bottom: 1px solid #e2e8f0;"><?= date('Y-m-d', strtotime($inv['created_at'])) ?></td>
                    <td style="padding: 14px 12px; border-bottom: 1px solid #e2e8f0;">
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <button onclick='editInvoice(<?= $inv['id'] ?>, <?= json_encode($inv['patient_name']) ?>, <?= $inv['amount'] ?>, <?= json_encode($inv['currency']) ?>, <?= json_encode($inv['description']) ?>, <?= json_encode($inv['status']) ?>)' style="background: #f59e0b; color: white; border: none; padding: 7px 14px; border-radius: 30px; cursor: pointer; font-size: 0.8rem;"><i class="fas fa-edit"></i> تعديل</button>
                            <button onclick="deleteInvoice(<?= $inv['id'] ?>)" style="background: #dc2626; color: white; border: none; padding: 7px 14px; border-radius: 30px; cursor: pointer; font-size: 0.8rem;"><i class="fas fa-trash-alt"></i> حذف</button>
                            <button onclick="printInvoice(<?= $inv['id'] ?>)" style="background: #059669; color: white; border: none; padding: 7px 14px; border-radius: 30px; cursor: pointer; font-size: 0.8rem;"><i class="fas fa-print"></i> طباعة</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($invoices_list)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px;">لا توجد فواتير مسجلة حالياً.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- مودال إضافة فاتورة (بتصميم جديد) -->
<div id="addInvoiceModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(3px); justify-content: center; align-items: center; z-index: 1000;">
    <div style="background: white; border-radius: 32px; padding: 2rem; width: 90%; max-width: 520px; box-shadow: 0 25px 45px rgba(0,0,0,0.2); animation: fadeInUp 0.3s;">
        <style>
            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>
        <h3 style="margin-bottom: 1.5rem; border-right: 4px solid #0ea5e9; padding-right: 1rem;"><i class="fas fa-plus-circle"></i> فاتورة جديدة</h3>
        <form method="POST" id="addInvoiceForm">
            <input type="hidden" name="action" value="add_invoice">
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;"><i class="fas fa-user"></i> اسم المريض</label>
                <input type="text" name="patient_name" required style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 16px; font-size: 1rem;">
            </div>
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;"><i class="fas fa-money-bill-wave"></i> المبلغ والعملة</label>
                <div style="display: flex; gap: 10px;">
                    <input type="number" step="0.01" name="amount" required style="flex: 2; padding: 12px; border: 2px solid #e2e8f0; border-radius: 16px; font-size: 1rem;">
                    <select name="currency" required style="flex: 1; padding: 12px; border: 2px solid #e2e8f0; border-radius: 16px; background: white;">
                        <option value="USD">دولار $</option>
                        <option value="SYP">ليرة سورية ل.س</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;"><i class="fas fa-align-left"></i> الوصف</label>
                <textarea name="description" rows="3" style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 16px;"></textarea>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;"><i class="fas fa-tag"></i> الحالة</label>
                <select name="status" style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 16px;">
                    <option value="unpaid">غير مدفوعة</option>
                    <option value="paid">مدفوعة</option>
                    <option value="cancelled">ملغاة</option>
                </select>
            </div>
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="submit" style="background: #059669; color: white; border: none; padding: 10px 28px; border-radius: 40px; cursor: pointer; font-weight: bold;"><i class="fas fa-save"></i> حفظ</button>
                <button type="button" onclick="closeInvoiceModal()" style="background: #dc2626; color: white; border: none; padding: 10px 28px; border-radius: 40px; cursor: pointer;"><i class="fas fa-times"></i> إلغاء</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddInvoiceModal() { document.getElementById('addInvoiceModal').style.display = 'flex'; }
    function closeInvoiceModal() { document.getElementById('addInvoiceModal').style.display = 'none'; }

    // تعديل الفاتورة (مودال ديناميكي)
    function editInvoice(id, patient, amount, currency, desc, status) {
        let modalHtml = `
            <div id="editInvModal" style="position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); backdrop-filter:blur(3px); display:flex; justify-content:center; align-items:center; z-index:1001;">
                <div style="background:white; border-radius:32px; padding:2rem; width:90%; max-width:520px; animation:fadeInUp 0.3s;">
                    <h3 style="margin-bottom:1.5rem; border-right:4px solid #0ea5e9; padding-right:1rem;"><i class="fas fa-edit"></i> تعديل الفاتورة</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="edit_invoice">
                        <input type="hidden" name="id" value="${id}">
                        <div style="margin-bottom:1.2rem;">
                            <label style="display:block; margin-bottom:0.5rem; font-weight:600;">اسم المريض</label>
                            <input type="text" name="patient_name" value="${escapeHtml(patient)}" required style="width:100%; padding:12px; border:2px solid #e2e8f0; border-radius:16px;">
                        </div>
                        <div style="margin-bottom:1.2rem;">
                            <label style="display:block; margin-bottom:0.5rem; font-weight:600;">المبلغ والعملة</label>
                            <div style="display:flex; gap:10px;">
                                <input type="number" step="0.01" name="amount" value="${amount}" required style="flex:2; padding:12px; border:2px solid #e2e8f0; border-radius:16px;">
                                <select name="currency" style="flex:1; padding:12px; border:2px solid #e2e8f0; border-radius:16px;">
                                    <option value="USD" ${currency === 'USD' ? 'selected' : ''}>دولار $</option>
                                    <option value="SYP" ${currency === 'SYP' ? 'selected' : ''}>ليرة سورية ل.س</option>
                                </select>
                            </div>
                        </div>
                        <div style="margin-bottom:1.2rem;">
                            <label style="display:block; margin-bottom:0.5rem; font-weight:600;">الوصف</label>
                            <textarea name="description" rows="3" style="width:100%; padding:12px; border:2px solid #e2e8f0; border-radius:16px;">${escapeHtml(desc)}</textarea>
                        </div>
                        <div style="margin-bottom:1.5rem;">
                            <label style="display:block; margin-bottom:0.5rem; font-weight:600;">الحالة</label>
                            <select name="status" style="width:100%; padding:12px; border:2px solid #e2e8f0; border-radius:16px;">
                                <option value="unpaid" ${status === 'unpaid' ? 'selected' : ''}>غير مدفوعة</option>
                                <option value="paid" ${status === 'paid' ? 'selected' : ''}>مدفوعة</option>
                                <option value="cancelled" ${status === 'cancelled' ? 'selected' : ''}>ملغاة</option>
                            </select>
                        </div>
                        <div style="display:flex; gap:1rem; justify-content:flex-end;">
                            <button type="submit" style="background:#059669; color:white; border:none; padding:10px 28px; border-radius:40px; cursor:pointer; font-weight:bold;">حفظ التغييرات</button>
                            <button type="button" onclick="this.closest('#editInvModal').remove()" style="background:#dc2626; color:white; border:none; padding:10px 28px; border-radius:40px; cursor:pointer;">إلغاء</button>
                        </div>
                    </form>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    function deleteInvoice(id) {
        if (confirm('هل أنت متأكد من حذف هذه الفاتورة؟ لا يمكن التراجع.')) {
            let form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `<input name="action" value="delete_invoice"><input name="id" value="${id}">`;
            document.body.appendChild(form);
            form.submit();
        }
    }

    function printInvoice(id) {
        window.open('print_invoice.php?id=' + id, '_blank');
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }
</script>