<?php
require_once 'includes/auth_check_patient.php';
require_once '../config/Database.php';

$database = new Database();
$pdo = $database->getConnection();

$patient_id = $_SESSION['user_id'];
$patient_name = $_SESSION['user_name'];
$message = '';
$error = '';


try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS appointments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        patient_id INT NOT NULL,
        doctor_name VARCHAR(100) NOT NULL,
        appointment_date DATE NOT NULL,
        appointment_time TIME NOT NULL,
        status ENUM('pending','confirmed','cancelled','completed') DEFAULT 'pending',
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
    )");
    $pdo->exec("CREATE TABLE IF NOT EXISTS medical_records (
        id INT AUTO_INCREMENT PRIMARY KEY,
        patient_id INT NOT NULL,
        record_date DATE NOT NULL,
        diagnosis TEXT,
        treatment TEXT,
        doctor_notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
    )");
} catch (PDOException $e) {  }


$doctors_stmt = $pdo->prepare("SELECT id, full_name FROM doctors WHERE status = 'approved' ORDER BY full_name");
$doctors_stmt->execute();
$doctors_list = $doctors_stmt->fetchAll();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'book_appointment') {
        $doctor_name = trim($_POST['doctor_name']);
        $appointment_date = $_POST['appointment_date'];
        $appointment_time = $_POST['appointment_time'];
        $notes = trim($_POST['notes']);
        if (empty($doctor_name) || empty($appointment_date) || empty($appointment_time)) {
            $error = "يرجى ملء جميع الحقول المطلوبة.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO appointments (patient_id, doctor_name, appointment_date, appointment_time, notes, status) VALUES (?, ?, ?, ?, ?, 'pending')");
            if ($stmt->execute([$patient_id, $doctor_name, $appointment_date, $appointment_time, $notes])) {
                $message = "تم حجز الموعد بنجاح.";
            } else {
                $error = "حدث خطأ أثناء حجز الموعد.";
            }
        }
        header("Location: patient_dashboard.php?page=book_appointment");
        exit;
    }
    elseif ($action === 'edit_appointment') {
        $appointment_id = $_POST['appointment_id'];
        $appointment_date = $_POST['appointment_date'];
        $appointment_time = $_POST['appointment_time'];
        $notes = trim($_POST['notes']);
        $stmt = $pdo->prepare("UPDATE appointments SET appointment_date = ?, appointment_time = ?, notes = ? WHERE id = ? AND patient_id = ? AND status != 'cancelled'");
        if ($stmt->execute([$appointment_date, $appointment_time, $notes, $appointment_id, $patient_id])) {
            $message = "تم تحديث الموعد بنجاح.";
        } else {
            $error = "حدث خطأ أثناء التعديل.";
        }
        header("Location: patient_dashboard.php?page=appointments");
        exit;
    }
    elseif ($action === 'cancel_appointment') {
        $appointment_id = $_POST['appointment_id'];
        $stmt = $pdo->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ? AND patient_id = ?");
        if ($stmt->execute([$appointment_id, $patient_id])) {
            $message = "تم إلغاء الموعد بنجاح.";
        } else {
            $error = "حدث خطأ أثناء الإلغاء.";
        }
        header("Location: patient_dashboard.php?page=appointments");
        exit;
    }
    elseif ($action === 'add_rating') {
        $entity_type = $_POST['entity_type'];
        $entity_id = (int)$_POST['entity_id'];
        $rating = (int)$_POST['rating'];
        $comment = trim($_POST['comment']);
        $stmt = $pdo->prepare("INSERT INTO ratings (patient_id, entity_type, entity_id, rating, comment) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$patient_id, $entity_type, $entity_id, $rating, $comment])) {
            $message = "تم إضافة التقييم بنجاح.";
        } else {
            $error = "حدث خطأ أثناء إضافة التقييم.";
        }
        header("Location: patient_dashboard.php?page=ratings");
        exit;
    }
    elseif ($action === 'update_rating') {
        $rating_id = (int)$_POST['rating_id'];
        $rating = (int)$_POST['rating'];
        $comment = trim($_POST['comment']);
        $stmt = $pdo->prepare("UPDATE ratings SET rating = ?, comment = ? WHERE id = ? AND patient_id = ?");
        if ($stmt->execute([$rating, $comment, $rating_id, $patient_id])) {
            $message = "تم تحديث التقييم بنجاح.";
        } else {
            $error = "حدث خطأ أثناء تحديث التقييم.";
        }
        header("Location: patient_dashboard.php?page=ratings");
        exit;
    }
    
    elseif ($action === 'submit_survey') {
        $survey_id = (int)$_POST['survey_id'];
        $score = (int)$_POST['score'];
        $response_text = trim($_POST['response_text']);
        $stmt = $pdo->prepare("INSERT INTO survey_responses (survey_id, patient_id, score, response_text) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$survey_id, $patient_id, $score, $response_text])) {
            $message = "تم إرسال الإجابة بنجاح.";
        } else {
            $error = "حدث خطأ أثناء إرسال الإجابة.";
        }
        header("Location: patient_dashboard.php?page=surveys");
        exit;
    }
    
elseif ($action === 'submit_survey_answers') {
    $survey_id = (int)$_POST['survey_id'];
    $answers = $_POST['answers'] ?? [];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM survey_answers WHERE survey_id = ? AND patient_id = ?");
    $stmt->execute([$survey_id, $patient_id]);
    if ($stmt->fetchColumn() > 0) {
        $error = "لقد قمت بالإجابة على هذا الاستبيان مسبقاً.";
    } else {
        $success = 0;
        foreach ($answers as $question_id => $answer) {
            if (!empty($answer)) {
                $stmt = $pdo->prepare("INSERT INTO survey_answers (survey_id, question_id, patient_id, answer) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$survey_id, $question_id, $patient_id, $answer])) {
                    $success++;
                }
            }
        }
        if ($success > 0) {
            $message = "تم إرسال إجاباتك بنجاح. شكراً لمشاركتك!";
            header("Location: patient_dashboard.php?page=surveys&msg=done");
            exit;
        } else {
            $error = "حدث خطأ أثناء إرسال الإجابات.";
        }
    }
}
    
elseif ($action === 'submit_survey_answers') {
    $survey_id = (int)$_POST['survey_id'];
    $answers = $_POST['answers'] ?? [];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM survey_answers WHERE survey_id = ? AND patient_id = ?");
    $stmt->execute([$survey_id, $patient_id]);
    if ($stmt->fetchColumn() > 0) {
        $error = "لقد قمت بالإجابة على هذا الاستبيان مسبقاً.";
    } else {
        $success = 0;
        foreach ($answers as $question_id => $answer) {
            if (!empty($answer)) {
                $stmt = $pdo->prepare("INSERT INTO survey_answers (survey_id, question_id, patient_id, answer) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$survey_id, $question_id, $patient_id, $answer])) {
                    $success++;
                }
            }
        }
        if ($success > 0) {
            $message = "تم إرسال إجاباتك بنجاح. شكراً لمشاركتك!";
            header("Location: patient_dashboard.php?page=surveys&msg=done");
            exit;
        } else {
            $error = "حدث خطأ أثناء إرسال الإجابات.";
        }
    }
}
}



$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$allowed_pages = ['dashboard', 'medical_records', 'appointments', 'book_appointment', 'surveys', 'ratings', 'profile'];
if (!in_array($page, $allowed_pages)) {
    $page = 'dashboard';
}


$appointments = $pdo->prepare("SELECT * FROM appointments WHERE patient_id = ? ORDER BY appointment_date DESC, appointment_time DESC");
$appointments->execute([$patient_id]);
$appointments_list = $appointments->fetchAll();

$medical_records = $pdo->prepare("SELECT * FROM medical_records WHERE patient_id = ? ORDER BY record_date DESC");
$medical_records->execute([$patient_id]);
$medical_list = $medical_records->fetchAll();

$surveys = $pdo->query("SELECT * FROM surveys ORDER BY created_at DESC")->fetchAll();

$ratings = $pdo->prepare("SELECT * FROM ratings WHERE patient_id = ?");
$ratings->execute([$patient_id]);
$ratings_list = $ratings->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم المريض - المركز الصحي المتقدم</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Tajawal', sans-serif; background: #f1f5f9; color: #1e293b; }
        .dashboard-wrapper { display: flex; min-height: 100vh; }
        .sidebar { width: 280px; background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%); color: white; padding: 2rem 1.5rem; position: fixed; height: 100vh; overflow-y: auto; }
        .sidebar h3 { font-size: 1.5rem; margin-bottom: 2rem; text-align: center; border-bottom: 1px solid #334155; padding-bottom: 0.5rem; }
        .sidebar nav a { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: #cbd5e1; text-decoration: none; border-radius: 12px; margin-bottom: 8px; transition: all 0.3s; }
        .sidebar nav a i { width: 24px; }
        .sidebar nav a:hover, .sidebar nav a.active { background: #0ea5e9; color: white; }
        .logout { margin-top: 2rem; border-top: 1px solid #334155; padding-top: 1rem; }
        .main-content { flex: 1; margin-right: 280px; padding: 2rem; }
        .header { background: white; border-radius: 24px; padding: 1rem 2rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .welcome h2 { font-size: 1.5rem; }
        .welcome p { color: #64748b; }
        .alert-success { background: #dcfce7; color: #166534; padding: 12px; border-radius: 12px; margin-bottom: 1rem; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 12px; margin-bottom: 1rem; border: 1px solid #fecaca; }
        @media (max-width: 768px) { .sidebar { display: none; } .main-content { margin-right: 0; } }
    </style>
</head>
<body>
<div class="dashboard-wrapper">
    <div class="sidebar">
        <h3>🏥 المركز الصحي</h3>
        <nav>
            <a href="?page=dashboard" class="<?= $page == 'dashboard' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> لوحة التحكم</a>
            <a href="?page=medical_records" class="<?= $page == 'medical_records' ? 'active' : '' ?>"><i class="fas fa-notes-medical"></i> السجلات الطبية</a>
            <a href="?page=appointments" class="<?= $page == 'appointments' ? 'active' : '' ?>"><i class="fas fa-calendar-check"></i> المواعيد</a>
            <a href="?page=book_appointment" class="<?= $page == 'book_appointment' ? 'active' : '' ?>"><i class="fas fa-plus-circle"></i> حجز موعد جديد</a>
            <a href="?page=surveys" class="<?= $page == 'surveys' ? 'active' : '' ?>"><i class="fas fa-poll"></i> الاستبيانات</a>
            <a href="?page=ratings" class="<?= $page == 'ratings' ? 'active' : '' ?>"><i class="fas fa-star"></i> التقييمات</a>
            <a href="?page=profile" class="<?= $page == 'profile' ? 'active' : '' ?>"><i class="fas fa-user-circle"></i> ملفي الشخصي</a>
        </nav>
        <div class="logout">
            <a href="logout.php" style="color:#f87171;"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <div class="welcome">
                <h2>مرحباً، <?= htmlspecialchars($patient_name) ?></h2>
                <p>نتمنى لك دوام الصحة والعافية</p>
            </div>
            <div><i class="fas fa-user-circle fa-2x" style="color:#0ea5e9;"></i></div>
        </div>

        <?php if ($message): ?><div class="alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <?php include "modules/{$page}.php"; ?>
    </div>
</div>
</body>
</html>