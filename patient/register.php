<?php
session_start();
require_once '../config/Database.php';
$database = new Database();
$pdo = $database->getConnection();

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name   = trim($_POST['full_name'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $phone       = trim($_POST['phone'] ?? '');
    $gender      = trim($_POST['gender'] ?? '');
    $dob         = trim($_POST['dob'] ?? '');
    $address     = trim($_POST['address'] ?? '');
    $password    = $_POST['password'] ?? '';
    $confirm_pwd = $_POST['confirm_password'] ?? '';

    if (empty($full_name) || empty($email) || empty($phone) || empty($gender) || empty($dob) || empty($address) || empty($password) || empty($confirm_pwd)) {
        $error_message = "جميع الحقول مطلوبة. يرجى تعبئة البيانات كاملة.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "البريد الإلكتروني غير صحيح.";
    } elseif (!preg_match('/^[0-9]{8,15}$/', $phone)) {
        $error_message = "رقم الهاتف يجب أن يحتوي على 8-15 رقماً فقط.";
    } elseif (!in_array($gender, ['ذكر', 'أنثى'])) {
        $error_message = "الجنس غير صالح.";
    } elseif (strtotime($dob) > time()) {
        $error_message = "تاريخ الميلاد لا يمكن أن يكون في المستقبل.";
    } elseif (strlen($password) < 8) {
        $error_message = "كلمة المرور يجب أن تكون 8 أحرف على الأقل.";
    } elseif ($password !== $confirm_pwd) {
        $error_message = "كلمة المرور وتأكيدها غير متطابقين.";
    } else {
        $gender_db = ($gender == 'ذكر') ? 'male' : 'female';
        $check = $pdo->prepare("SELECT id FROM patients WHERE email = ? OR phone = ?");
        $check->execute([$email, $phone]);
        if ($check->rowCount() > 0) {
            $error_message = "البريد الإلكتروني أو رقم الهاتف مسجل مسبقاً. يرجى استخدام بيانات أخرى.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO patients (full_name, email, phone, dob, gender, address, password) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$full_name, $email, $phone, $dob, $gender_db, $address, $hashed_password])) {
                $success_message = "تم إنشاء حساب المريض بنجاح! سيتم تحويلك إلى صفحة تسجيل الدخول خلال 3 ثوانٍ.";
                
                echo '<script>alert("' . addslashes($success_message) . '");</script>';
                echo '<meta http-equiv="refresh" content="3;url=../login.php">';
            } else {
                $error_message = "حدث خطأ أثناء إنشاء الحساب، يرجى المحاولة مرة أخرى.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب مريض - المركز الصحي المتقدم</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, #e0f2fe 0%, #f0fdf4 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px 20px;
        }

        .register-container {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            max-width: 1200px;
            width: 100%;
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .branding {
            background: linear-gradient(145deg, #0ea5e9 0%, #059669 100%);
            color: white;
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .branding h1 {
            font-size: 2rem;
            margin: 20px 0 12px;
            font-weight: 700;
        }

        .branding p {
            font-size: 1rem;
            opacity: 0.92;
            line-height: 1.7;
            margin-bottom: 25px;
        }

        .medical-icons {
            display: flex;
            gap: 20px;
            margin: 20px 0;
            flex-wrap: wrap;
            justify-content: center;
        }

        .icon {
            background: rgba(255, 255, 255, 0.2);
            width: 60px;
            height: 60px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            backdrop-filter: blur(2px);
        }

        .benefits-list {
            text-align: right;
            margin-top: 20px;
            font-size: 0.9rem;
            list-style: none;
        }
        .benefits-list li {
            margin: 12px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-section {
            padding: 40px 35px;
            background: white;
        }

        .form-section h2 {
            color: #0f172a;
            margin-bottom: 8px;
            font-size: 1.8rem;
        }

        .form-section p {
            color: #64748b;
            margin-bottom: 25px;
            font-size: 0.95rem;
        }

        .two-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .full-width {
            grid-column: span 2;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #334155;
            font-size: 0.85rem;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px 12px 44px;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            font-size: 0.95rem;
            transition: all 0.3s;
            font-family: 'Tajawal', sans-serif;
            background-color: #fefefe;
        }

        .form-group textarea {
            padding: 12px 16px;
            resize: vertical;
            min-height: 80px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #0ea5e9;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
        }

        .form-icon {
            position: absolute;
            left: 16px;
            top: 42px;
            color: #94a3b8;
            font-size: 18px;
        }

        .toggle-password {
            position: absolute;
            left: 50px;
            top: 42px;
            cursor: pointer;
            color: #94a3b8;
            font-size: 18px;
            user-select: none;
        }
        .toggle-password:hover {
            color: #0ea5e9;
        }

        .password-strength {
            font-size: 0.75rem;
            margin-top: 5px;
            text-align: right;
        }
        .weak { color: #dc2626; }
        .medium { color: #f59e0b; }
        .strong { color: #10b981; }

        .btn-register {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 10px;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.35);
        }

        .error {
            background: #fee2e2;
            color: #dc2626;
            padding: 12px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 20px;
            font-weight: 500;
            border: 1px solid #fecaca;
        }

        .success {
            background: #dcfce7;
            color: #15803d;
            padding: 12px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 20px;
            font-weight: 500;
            border: 1px solid #bbf7d0;
            font-size: 1rem;
        }

        .back-home {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #0ea5e9;
            text-decoration: none;
            font-weight: 600;
            transition: opacity 0.2s;
        }

        .back-home:hover {
            opacity: 0.8;
            text-decoration: underline;
        }

        @media (max-width: 850px) {
            .register-container {
                grid-template-columns: 1fr;
                max-width: 550px;
            }
            .two-columns {
                grid-template-columns: 1fr;
                gap: 0;
            }
            .full-width {
                grid-column: span 1;
            }
            .branding {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
<div class="register-container">
    <div class="branding">
        <div style="font-size: 52px;">🏥</div>
        <h1>المركز الصحي المتقدم</h1>
        <p>انضم إلى آلاف المرضى الذين يثقون بخدماتنا الطبية المتميزة</p>
        <div class="medical-icons">
            <div class="icon">🩺</div>
            <div class="icon">💉</div>
            <div class="icon">💊</div>
            <div class="icon">📋</div>
        </div>
        <ul class="benefits-list">
            <li>✓ استشارات طبية فورية</li>
            <li>✓ ملف صحي رقمي آمن</li>
            <li>✓ تذكير بالمواعيد والفحوصات</li>
            <li>✓ متابعة الأمراض المزمنة</li>
        </ul>
    </div>

    <div class="form-section">
        <h2>إنشاء حساب جديد</h2>
        <p>أدخل بياناتك لإنشاء حساب مريض والاستفادة من الخدمات</p>

        <form method="POST">
            <div class="two-columns">
                <div>
                    <div class="form-group">
                        <label for="full_name">الاسم الكامل</label>
                        <span class="form-icon">👤</span>
                        <input type="text" id="full_name" name="full_name" placeholder="أدخل الاسم الثلاثي" required value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="email">البريد الإلكتروني</label>
                        <span class="form-icon">📧</span>
                        <input type="email" id="email" name="email" placeholder="example@domain.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="phone">رقم الهاتف</label>
                        <span class="form-icon">📞</span>
                        <input type="tel" id="phone" name="phone" placeholder="09xxxxxxxx" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="gender">الجنس</label>
                        <span class="form-icon">⚥</span>
                        <select id="gender" name="gender" required>
                            <option value="" disabled selected>اختر الجنس</option>
                            <option value="ذكر" <?= (isset($_POST['gender']) && $_POST['gender'] == 'ذكر') ? 'selected' : '' ?>>♂ ذكر</option>
                            <option value="أنثى" <?= (isset($_POST['gender']) && $_POST['gender'] == 'أنثى') ? 'selected' : '' ?>>♀ أنثى</option>
                        </select>
                    </div>
                </div>

                <div>
                    <div class="form-group">
                        <label for="dob">تاريخ الميلاد</label>
                        <span class="form-icon">🎂</span>
                        <input type="date" id="dob" name="dob" required value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="password">كلمة المرور</label>
                        <span class="form-icon">🔒</span>
                        <input type="password" id="password" name="password" placeholder="8 أحرف على الأقل" required>
                        <span class="toggle-password" onclick="togglePassword('password', this)">👁️</span>
                        <div id="password-strength" class="password-strength"></div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">تأكيد كلمة المرور</label>
                        <span class="form-icon">✓</span>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="أعد كتابة كلمة المرور" required>
                        <span class="toggle-password" onclick="togglePassword('confirm_password', this)">👁️</span>
                    </div>
                </div>

                <div class="full-width">
                    <div class="form-group textarea-icon">
                        <label for="address">العنوان الكامل</label>
                        <span class="form-icon">📍</span>
                        <textarea id="address" name="address" placeholder="المدينة، الحي، الشارع، رقم المبنى..." required><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            
            <?php if ($error_message): ?>
                <div class="error"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="success"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>

            <button type="submit" class="btn-register">إنشاء حساب المريض</button>
        </form>

        <a href="../admin/login.php" class="back-home">← لديك حساب بالفعل؟ تسجيل الدخول</a>
        <a href="../index.php" class="back-home" style="margin-top: 10px;">← العودة إلى الصفحة الرئيسية</a>
    </div>
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

    function checkPasswordStrength() {
        const password = document.getElementById('password').value;
        const strengthDiv = document.getElementById('password-strength');
        if (password.length === 0) {
            strengthDiv.innerHTML = '';
            return;
        }
        let strength = 'ضعيفة';
        let strengthClass = 'weak';
        const hasUpperCase = /[A-Z]/.test(password);
        const hasLowerCase = /[a-z]/.test(password);
        const hasNumbers = /\d/.test(password);
        const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);
        let score = 0;
        if (password.length >= 8) score++;
        if (hasUpperCase || hasLowerCase) score++;
        if (hasNumbers) score++;
        if (hasSpecial) score++;
        
        if (score >= 3 && password.length >= 8) {
            strength = 'قوية';
            strengthClass = 'strong';
        } else if (score >= 2 && password.length >= 6) {
            strength = 'متوسطة';
            strengthClass = 'medium';
        } else {
            strength = 'ضعيفة';
            strengthClass = 'weak';
        }
        strengthDiv.innerHTML = `قوة كلمة المرور: <span class="${strengthClass}">${strength}</span>`;
    }

    document.getElementById('password').addEventListener('input', checkPasswordStrength);
</script>
</body>
</html>