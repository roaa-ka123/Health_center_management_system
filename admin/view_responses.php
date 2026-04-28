<?php
require_once 'includes/auth_check.php';
$current_page_name = 'view_responses';

require_once '../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

if (!isset($_GET['survey_id']) || !is_numeric($_GET['survey_id'])) {
    header('Location: surveys.php');
    exit;
}

$survey_id = (int)$_GET['survey_id'];


$stmt_survey = $conn->prepare("SELECT title, description, created_at, (SELECT full_name FROM staff WHERE id = created_by) as creator_name FROM surveys WHERE id = ?");
$stmt_survey->execute([$survey_id]);
$survey = $stmt_survey->fetch(PDO::FETCH_ASSOC);

if (!$survey) {
    echo "لم يتم العثور على الاستبيان.";
    exit;
}


$stmt_questions = $conn->prepare("SELECT id, question_text, question_type, options FROM survey_questions WHERE survey_id = ? ORDER BY order_num ASC, id ASC");
$stmt_questions->execute([$survey_id]);
$questions = $stmt_questions->fetchAll(PDO::FETCH_ASSOC);


$stmt_answers = $conn->prepare("
    SELECT 
        sa.patient_id,
        p.full_name as patient_name,
        sa.question_id,
        sa.answer,
        sa.created_at as answer_date
    FROM survey_answers sa
    JOIN patients p ON sa.patient_id = p.id
    WHERE sa.survey_id = ?
    ORDER BY p.id, sa.created_at
");
$stmt_answers->execute([$survey_id]);
$answers_raw = $stmt_answers->fetchAll(PDO::FETCH_ASSOC);


$responses_by_patient = [];
foreach ($answers_raw as $ans) {
    $pid = $ans['patient_id'];
    if (!isset($responses_by_patient[$pid])) {
        $responses_by_patient[$pid] = [
            'patient_name' => $ans['patient_name'],
            'answers' => [],
            'answer_date' => $ans['answer_date']
        ];
    }
    $responses_by_patient[$pid]['answers'][$ans['question_id']] = $ans['answer'];
}

$response_count = count($responses_by_patient);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ردود الاستبيان: <?= htmlspecialchars($survey['title']) ?> - المركز الصحي</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    .main-content { margin-right: 260px; padding: 30px; }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px; }
    
    .page-title { 
        font-size: 1.8rem; 
        font-weight: 700; 
        color: var(--text-color);
        display: flex; 
        align-items: center; 
        gap: 12px; 
    }
    
    .survey-info-card { 
        background: var(--border-color);
        border-radius: 12px; 
        padding: 20px; 
        margin-bottom: 30px; 
        border: 1px solid var(--border-color);
    }
    
    .survey-info-card h2 { 
        margin-top: 0; 
        font-size: 1.5rem; 
        color: var(--text-color);
    }
    
    .survey-info-card p { 
        color: var(--text-color);
        line-height: 1.6; 
    }
    
    .response-card { 
        background: var(--card-bg);
        border-radius: 16px; 
        padding: 25px; 
        margin-bottom: 20px; 
        box-shadow: 0 2px 8px var(--card-shadow);
    }
    
    .response-header { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        margin-bottom: 15px; 
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 10px; 
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .patient-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #0ea5e9;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .response-meta { 
        font-size: 0.9rem; 
        color: var(--muted-text);
    }
    
    .question-item {
        margin: 15px 0;
        padding: 10px;
        background: var(--bg-color);
        border-radius: 12px;
    }
    
    .question-text {
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--text-color);
    }
    
    .answer-text {
        color: var(--text-color);
        padding-right: 15px;
        border-right: 3px solid #0ea5e9;
        margin-top: 5px;
        line-height: 1.6;
    }
    
    .back-link { 
        margin-bottom: 20px; 
        display: inline-block; 
        color: #0ea5e9; 
        text-decoration: none; 
        font-weight: 600; 
    }
    
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
        .main-content { margin-right: 80px; }
        .response-header { flex-direction: column; align-items: flex-start; }
    }
</style>
    <?php include 'includes/theme_logic.php'; ?>
    <?php include 'includes/sidebar.php' ?>
</head>
<body>

    <div class="main-content">
        <a href="surveys.php" class="back-link">← العودة إلى الاستبيانات</a>
        <div class="page-header">
            <h1 class="page-title">
                <span class="icon">📋</span>
                ردود استبيان: <?= htmlspecialchars($survey['title']) ?> (<?= $response_count ?> مريض)
            </h1>
        </div>
        
        <div class="survey-info-card">
            <h2>وصف الاستبيان</h2>
            <p><?= nl2br(htmlspecialchars($survey['description'])) ?></p>
            <p style="margin-top: 10px; font-size:0.85rem;">تم الإنشاء بواسطة: <?= htmlspecialchars($survey['creator_name']) ?> | التاريخ: <?= date('Y-m-d', strtotime($survey['created_at'])) ?></p>
        </div>

        <?php if ($response_count > 0): ?>
            <?php foreach ($responses_by_patient as $patient): ?>
            <div class="response-card">
                <div class="response-header">
                    <div class="patient-name">
                        👤 <?= htmlspecialchars($patient['patient_name']) ?>
                    </div>
                    <div class="response-meta">
                        تاريخ الإجابة: <?= date('Y-m-d H:i', strtotime($patient['answer_date'])) ?>
                    </div>
                </div>
                <div class="responses-details">
                    <?php foreach ($questions as $question): ?>
                        <?php $answer = isset($patient['answers'][$question['id']]) ? $patient['answers'][$question['id']] : 'لم يجب'; ?>
                        <div class="question-item">
                            <div class="question-text">❓ <?= htmlspecialchars($question['question_text']) ?></div>
                            <div class="answer-text">
                                <?php if ($question['question_type'] == 'rating'): ?>
                                    ⭐ التقييم: <?= htmlspecialchars($answer) ?>/5
                                <?php elseif ($question['question_type'] == 'multiple_choice'): ?>
                                    📌 الإجابة: <?= htmlspecialchars($answer) ?>
                                <?php else: ?>
                                    📝 الإجابة: <?= nl2br(htmlspecialchars($answer)) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
        <div class="empty-state">
            <h3>
                <span class="icon">❌</span>
                لا توجد ردود حالياً لهذا الاستبيان
            </h3>
            <p>الانتظار حتى يتم إرسال ردود من قبل المستخدمين.</p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>