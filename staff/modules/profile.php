<?php
// جلب بيانات الموظف الحالية
$stmt = $pdo->prepare("SELECT full_name, email, phone, password FROM staff WHERE id = ?");
$stmt->execute([$staff_id]);
$staff = $stmt->fetch(PDO::FETCH_ASSOC);

$profile_message = '';
$profile_error = '';

// معالجة تحديث الملف الشخصي (بيانات أساسية)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    if (empty($full_name) || empty($email)) {
        $profile_error = "الاسم الكامل والبريد الإلكتروني مطلوبان.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $profile_error = "البريد الإلكتروني غير صحيح.";
    } elseif (!preg_match('/^[0-9]{8,15}$/', $phone)) {
        $profile_error = "رقم الهاتف يجب أن يحتوي على 8-15 رقماً.";
    } else {
        // التحقق من عدم استخدام البريد الإلكتروني من قبل موظف آخر
        $check = $pdo->prepare("SELECT id FROM staff WHERE email = ? AND id != ?");
        $check->execute([$email, $staff_id]);
        if ($check->rowCount() > 0) {
            $profile_error = "البريد الإلكتروني مستخدم من قبل موظف آخر.";
        } else {
            $update = $pdo->prepare("UPDATE staff SET full_name = ?, email = ?, phone = ? WHERE id = ?");
            if ($update->execute([$full_name, $email, $phone, $staff_id])) {
                $_SESSION['user_name'] = $full_name;
                $profile_message = "تم تحديث بياناتك بنجاح.";
                // تحديث المتغيرات المعروضة
                $staff['full_name'] = $full_name;
                $staff['email'] = $email;
                $staff['phone'] = $phone;
            } else {
                $profile_error = "حدث خطأ أثناء تحديث البيانات.";
            }
        }
    }
}

// معالجة تغيير كلمة المرور
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $profile_error = "جميع حقول كلمة المرور مطلوبة.";
    } elseif (!password_verify($current_password, $staff['password'])) {
        $profile_error = "كلمة المرور الحالية غير صحيحة.";
    } elseif ($new_password !== $confirm_password) {
        $profile_error = "كلمة المرور الجديدة وتأكيدها غير متطابقين.";
    } elseif (strlen($new_password) < 6) {
        $profile_error = "كلمة المرور الجديدة يجب أن تكون 6 أحرف على الأقل.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE staff SET password = ? WHERE id = ?");
        if ($update->execute([$hashed_password, $staff_id])) {
            $profile_message = "تم تغيير كلمة المرور بنجاح.";
            $staff['password'] = $hashed_password;
        } else {
            $profile_error = "حدث خطأ أثناء تغيير كلمة المرور.";
        }
    }
}
?>

<style>
    .profile-card {
        background: white;
        border-radius: 24px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .profile-card h3 {
        font-size: 1.3rem;
        margin-bottom: 1rem;
        border-right: 4px solid #0ea5e9;
        padding-right: 1rem;
    }
    .profile-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.2rem;
    }
    .profile-field {
        margin-bottom: 1rem;
        position: relative;
    }
    .profile-field label {
        display: block;
        font-weight: 600;
        margin-bottom: 0.3rem;
        color: #334155;
    }
    .profile-field input {
        width: 100%;
        padding: 10px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-family: 'Tajawal', sans-serif;
    }
    .profile-field input:focus {
        outline: none;
        border-color: #0ea5e9;
    }
    .password-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }
    .password-wrapper input {
        flex: 1;
        padding-left: 40px;
    }
    .toggle-password {
        position: absolute;
        left: 12px;
        cursor: pointer;
        color: #94a3b8;
        font-size: 1.1rem;
    }
    .toggle-password:hover {
        color: #0ea5e9;
    }
    .btn-save {
        background: linear-gradient(135deg, #0ea5e9, #059669);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 40px;
        cursor: pointer;
        font-weight: bold;
        margin-top: 0.5rem;
    }
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .alert-profile-success {
        background: #dcfce7;
        color: #166534;
        padding: 12px;
        border-radius: 12px;
        margin-bottom: 1rem;
    }
    .alert-profile-error {
        background: #fee2e2;
        color: #991b1b;
        padding: 12px;
        border-radius: 12px;
        margin-bottom: 1rem;
    }
    @media (max-width: 768px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="profile-card">
    <h3><i class="fas fa-user-edit"></i> معلوماتي الشخصية</h3>

    <?php if ($profile_message): ?>
        <div class="alert-profile-success">✅ <?= htmlspecialchars($profile_message) ?></div>
    <?php endif; ?>
    <?php if ($profile_error): ?>
        <div class="alert-profile-error">❌ <?= htmlspecialchars($profile_error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="profile-grid">
            <div class="profile-field">
                <label>الاسم الكامل</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($staff['full_name']) ?>" required>
            </div>
            <div class="profile-field">
                <label>البريد الإلكتروني</label>
                <input type="email" name="email" value="<?= htmlspecialchars($staff['email']) ?>" required>
            </div>
            <div class="profile-field">
                <label>رقم الهاتف</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($staff['phone'] ?? '') ?>">
            </div>
        </div>
        <button type="submit" name="update_profile" class="btn-save"><i class="fas fa-save"></i> حفظ التغييرات</button>
    </form>
</div>

<!-- قسم تغيير كلمة المرور -->
<div class="profile-card">
    <h3><i class="fas fa-lock"></i> تغيير كلمة المرور</h3>
    <form method="POST">
        <div class="profile-grid">
            <div class="profile-field">
                <label>كلمة المرور الحالية</label>
                <div class="password-wrapper">
                    <input type="password" name="current_password" id="current_password" required>
                    <span class="toggle-password" onclick="togglePassword('current_password', this)">👁️</span>
                </div>
            </div>
            <div class="profile-field">
                <label>كلمة المرور الجديدة</label>
                <div class="password-wrapper">
                    <input type="password" name="new_password" id="new_password" required minlength="6">
                    <span class="toggle-password" onclick="togglePassword('new_password', this)">👁️</span>
                </div>
            </div>
            <div class="profile-field">
                <label>تأكيد كلمة المرور الجديدة</label>
                <div class="password-wrapper">
                    <input type="password" name="confirm_password" id="confirm_password" required minlength="6">
                    <span class="toggle-password" onclick="togglePassword('confirm_password', this)">👁️</span>
                </div>
            </div>
        </div>
        <button type="submit" name="change_password" class="btn-save"><i class="fas fa-key"></i> تغيير كلمة المرور</button>
    </form>
</div>

<script>
    function togglePassword(fieldId, element) {
        const input = document.getElementById(fieldId);
        if (input.type === "password") {
            input.type = "text";
            element.textContent = "🙈";
        } else {
            input.type = "password";
            element.textContent = "👁️";
        }
    }
</script>