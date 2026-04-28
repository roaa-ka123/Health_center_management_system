<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add_department') {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $icon = trim($_POST['icon']);
        $status = $_POST['status'];
        if (empty($name)) {
            $_SESSION['staff_error'] = "اسم القسم مطلوب.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO departments (name, description, icon, status) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $description, $icon, $status])) {
                $_SESSION['staff_message'] = "تم إضافة القسم بنجاح.";
            } else {
                $_SESSION['staff_error'] = "فشل إضافة القسم.";
            }
        }
        header("Location: staff_dashboard.php?page=departments");
        exit;
    }
    elseif ($action === 'edit_department') {
        $id = $_POST['id'];
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $icon = trim($_POST['icon']);
        $status = $_POST['status'];
        $stmt = $pdo->prepare("UPDATE departments SET name=?, description=?, icon=?, status=? WHERE id=?");
        if ($stmt->execute([$name, $description, $icon, $status, $id])) {
            $_SESSION['staff_message'] = "تم تحديث القسم.";
        } else {
            $_SESSION['staff_error'] = "فشل التحديث.";
        }
        header("Location: staff_dashboard.php?page=departments");
        exit;
    }
    elseif ($action === 'delete_department') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM departments WHERE id=?");
        if ($stmt->execute([$id])) {
            $_SESSION['staff_message'] = "تم حذف القسم.";
        } else {
            $_SESSION['staff_error'] = "فشل الحذف.";
        }
        header("Location: staff_dashboard.php?page=departments");
        exit;
    }
    elseif ($action === 'toggle_department_status') {
        $id = $_POST['id'];
        $new_status = $_POST['new_status'];
        $stmt = $pdo->prepare("UPDATE departments SET status=? WHERE id=?");
        if ($stmt->execute([$new_status, $id])) {
            $_SESSION['staff_message'] = "تم تغيير حالة القسم.";
        } else {
            $_SESSION['staff_error'] = "فشل تغيير الحالة.";
        }
        header("Location: staff_dashboard.php?page=departments");
        exit;
    }
}


$departments = $pdo->query("SELECT * FROM departments ORDER BY created_at DESC")->fetchAll();
?>

<div class="card" style="background: white; border-radius: 24px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 10px;">
        <h3 style="border-right: 4px solid #0ea5e9; padding-right: 1rem;"><i class="fas fa-building"></i> إدارة الأقسام</h3>
        <button class="btn btn-primary" onclick="openAddModal()" style="background: linear-gradient(135deg, #0ea5e9, #059669); color: white; border: none; padding: 10px 20px; border-radius: 40px; cursor: pointer; font-weight: bold; box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: 0.2s;">
            <i class="fas fa-plus"></i> قسم جديد
        </button>
    </div>
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="padding: 14px 12px; text-align: right; border-bottom: 2px solid #e2e8f0;">القسم</th>
                    <th style="padding: 14px 12px; text-align: right; border-bottom: 2px solid #e2e8f0;">الوصف</th>
                    <th style="padding: 14px 12px; text-align: right; border-bottom: 2px solid #e2e8f0;">الحالة</th>
                    <th style="padding: 14px 12px; text-align: right; border-bottom: 2px solid #e2e8f0;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($departments as $dept): ?>
                <tr>
                    <td style="padding: 14px 12px; border-bottom: 1px solid #e2e8f0; vertical-align: middle;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <span style="font-size: 1.6rem;"><?= htmlspecialchars($dept['icon']) ?></span>
                            <span style="font-weight: 600;"><?= htmlspecialchars($dept['name']) ?></span>
                        </div>
                    </td>
                    <td style="padding: 14px 12px; border-bottom: 1px solid #e2e8f0;"><?= htmlspecialchars($dept['description']) ?></td>
                    <td style="padding: 14px 12px; border-bottom: 1px solid #e2e8f0;">
                        <span style="padding: 6px 14px; border-radius: 40px; font-size: 0.75rem; font-weight: bold; background: <?= $dept['status'] == 'active' ? '#dcfce7' : '#fee2e2' ?>; color: <?= $dept['status'] == 'active' ? '#166534' : '#991b1b' ?>;">
                            <?= $dept['status'] == 'active' ? 'فعال' : 'غير فعال' ?>
                        </span>
                    </td>
                    <td style="padding: 14px 12px; border-bottom: 1px solid #e2e8f0;">
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <button onclick='editDept(<?= $dept['id'] ?>, <?= json_encode($dept['name']) ?>, <?= json_encode($dept['description']) ?>, <?= json_encode($dept['icon']) ?>, <?= json_encode($dept['status']) ?>)' style="background: #f59e0b; color: white; border: none; padding: 6px 14px; border-radius: 30px; cursor: pointer; font-size: 0.8rem;"><i class="fas fa-edit"></i> تعديل</button>
                            <button onclick="deleteDept(<?= $dept['id'] ?>)" style="background: #dc2626; color: white; border: none; padding: 6px 14px; border-radius: 30px; cursor: pointer; font-size: 0.8rem;"><i class="fas fa-trash"></i> حذف</button>
                            <button onclick="toggleDeptStatus(<?= $dept['id'] ?>, '<?= $dept['status'] == 'active' ? 'inactive' : 'active' ?>')" style="background: #6c757d; color: white; border: none; padding: 6px 14px; border-radius: 30px; cursor: pointer; font-size: 0.8rem;"><?= $dept['status'] == 'active' ? 'تعطيل' : 'تفعيل' ?></button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


<div id="addDeptModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000;">
    <div style="background: white; border-radius: 28px; padding: 2rem; width: 90%; max-width: 500px; box-shadow: 0 20px 35px rgba(0,0,0,0.2);">
        <h3 style="margin-bottom: 1.5rem; border-right: 4px solid #0ea5e9; padding-right: 1rem;">إضافة قسم جديد</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add_department">
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">اسم القسم</label>
                <input type="text" name="name" required style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 16px; font-size: 1rem;">
            </div>
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">الوصف</label>
                <textarea name="description" rows="3" style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 16px; font-size: 1rem;"></textarea>
            </div>
            <div style="margin-bottom: 1.2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">الأيقونة (رمز تعبيري)</label>
                <input type="text" name="icon" placeholder="مثال: 🩺" style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 16px; font-size: 1rem;">
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">الحالة</label>
                <select name="status" style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 16px;">
                    <option value="active">نشط</option>
                    <option value="inactive">غير نشط</option>
                </select>
            </div>
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="submit" style="background: #059669; color: white; border: none; padding: 10px 24px; border-radius: 40px; cursor: pointer; font-weight: bold;">حفظ</button>
                <button type="button" onclick="closeModal('addDeptModal')" style="background: #dc2626; color: white; border: none; padding: 10px 24px; border-radius: 40px; cursor: pointer;">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('addDeptModal').style.display = 'flex';
    }
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    // دالة تعديل القسم (تعرض مودال منفصل مع البيانات)
    function editDept(id, name, desc, icon, status) {
        // إنشاء مودال التعديل ديناميكياً
        let modalDiv = document.createElement('div');
        modalDiv.id = 'editModalDynamic';
        modalDiv.style.cssText = 'position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); display:flex; justify-content:center; align-items:center; z-index:1001;';
        modalDiv.innerHTML = `
            <div style="background: white; border-radius: 28px; padding: 2rem; width: 90%; max-width: 500px; box-shadow: 0 20px 35px rgba(0,0,0,0.2);">
                <h3 style="margin-bottom: 1.5rem; border-right: 4px solid #0ea5e9; padding-right: 1rem;">تعديل القسم</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="edit_department">
                    <input type="hidden" name="id" value="${id}">
                    <div style="margin-bottom: 1.2rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">اسم القسم</label>
                        <input type="text" name="name" value="${escapeHtml(name)}" required style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 16px; font-size: 1rem;">
                    </div>
                    <div style="margin-bottom: 1.2rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">الوصف</label>
                        <textarea name="description" rows="3" style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 16px; font-size: 1rem;">${escapeHtml(desc)}</textarea>
                    </div>
                    <div style="margin-bottom: 1.2rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">الأيقونة (رمز تعبيري)</label>
                        <input type="text" name="icon" value="${escapeHtml(icon)}" style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 16px; font-size: 1rem;">
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">الحالة</label>
                        <select name="status" style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 16px;">
                            <option value="active" ${status === 'active' ? 'selected' : ''}>نشط</option>
                            <option value="inactive" ${status === 'inactive' ? 'selected' : ''}>غير نشط</option>
                        </select>
                    </div>
                    <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                        <button type="submit" style="background: #059669; color: white; border: none; padding: 10px 24px; border-radius: 40px; cursor: pointer; font-weight: bold;">حفظ التغييرات</button>
                        <button type="button" onclick="this.closest('#editModalDynamic').remove()" style="background: #dc2626; color: white; border: none; padding: 10px 24px; border-radius: 40px; cursor: pointer;">إلغاء</button>
                    </div>
                </form>
            </div>
        `;
        document.body.appendChild(modalDiv);
    }

    
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        }).replace(/[\uD800-\uDBFF][\uDC00-\uDFFF]/g, function(c) {
            return c;
        });
    }

    function deleteDept(id) {
        if (confirm('هل أنت متأكد من حذف هذا القسم؟')) {
            let form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `<input name="action" value="delete_department"><input name="id" value="${id}">`;
            document.body.appendChild(form);
            form.submit();
        }
    }

    function toggleDeptStatus(id, newStatus) {
        let form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `<input name="action" value="toggle_department_status"><input name="id" value="${id}"><input name="new_status" value="${newStatus}">`;
        document.body.appendChild(form);
        form.submit();
    }
</script>