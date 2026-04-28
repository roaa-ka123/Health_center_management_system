<?php

try {
    $stmt = $pdo->query("SHOW COLUMNS FROM survey_questions LIKE 'order_num'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE survey_questions ADD COLUMN order_num INT DEFAULT 0");
    }
} catch (PDOException $e) {
    
}


$selected_survey_id = isset($_GET['survey_id']) ? (int)$_GET['survey_id'] : 0;
$show_questions = false;
$questions = [];
$survey_title = '';

if ($selected_survey_id) {
    
    $stmt = $pdo->prepare("SELECT title FROM surveys WHERE id = ?");
    $stmt->execute([$selected_survey_id]);
    $survey_title = $stmt->fetchColumn();
    
   
    $stmt = $pdo->prepare("SELECT * FROM survey_questions WHERE survey_id = ? ORDER BY id ASC");
    $stmt->execute([$selected_survey_id]);
    $questions = $stmt->fetchAll();
    
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM survey_answers WHERE survey_id = ? AND patient_id = ?");
    $stmt->execute([$selected_survey_id, $patient_id]);
    $already_answered = $stmt->fetchColumn() > 0;
    
    if ($already_answered) {
        $error = "لقد قمت بالإجابة على هذا الاستبيان مسبقاً. شكراً لك.";
        $show_questions = false;
    } elseif (empty($questions)) {
    
    $error = "هذا الاستبيان لا يحتوي على أسئلة (استبيان وصفي فقط).";
    $show_questions = true; 
    } else {
        $show_questions = true;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_survey_answers'])) {
    $survey_id = (int)$_POST['survey_id'];
    $answers = $_POST['answers'] ?? [];
    
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM survey_answers WHERE survey_id = ? AND patient_id = ?");
    $stmt->execute([$survey_id, $patient_id]);
    if ($stmt->fetchColumn() > 0) {
        $error = "لقد قمت بالإجابة على هذا الاستبيان مسبقاً.";
    } else {
        $success_count = 0;
        foreach ($answers as $question_id => $answer) {
            if (!empty($answer)) {
                $stmt = $pdo->prepare("INSERT INTO survey_answers (survey_id, question_id, patient_id, answer) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$survey_id, $question_id, $patient_id, $answer])) {
                    $success_count++;
                }
            }
        }
        if ($success_count > 0) {
            $message = "تم إرسال إجاباتك بنجاح. شكراً لمشاركتك!";
            header("Location: patient_dashboard.php?page=surveys&msg=done");
            exit;
        } else {
            $error = "حدث خطأ أثناء إرسال الإجابات.";
        }
    }
}
?>

<style>
    .survey-card {
        background: white;
        border-radius: 24px;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        margin-bottom: 1.5rem;
    }
    .question-box {
        background: #f8fafc;
        border-radius: 16px;
        padding: 1rem;
        margin-bottom: 1rem;
        border-right: 4px solid #0ea5e9;
    }
    .question-text {
        font-weight: 600;
        margin-bottom: 0.8rem;
        font-size: 1rem;
    }
    .rating-stars {
        display: flex;
        gap: 12px;
        direction: ltr;
        justify-content: flex-start;
        flex-wrap: wrap;
    }
    .rating-stars label {
        cursor: pointer;
        font-size: 1.5rem;
        color: #cbd5e1;
        transition: color 0.2s;
    }
    .rating-stars input:checked + label {
        color: #f59e0b;
    }
    .rating-stars input {
        display: none;
    }
    .multiple-choice-option {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }
    .multiple-choice-option input {
        width: auto;
        margin: 0;
    }
    .btn-back {
        display: inline-block;
        background: #e2e8f0;
        color: #1e293b;
        padding: 8px 16px;
        border-radius: 30px;
        text-decoration: none;
        margin-bottom: 1rem;
    }
    .btn-back:hover {
        background: #cbd5e1;
    }
</style>

<div class="survey-card">
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'done'): ?>
        <div class="alert-success">✅ تم إرسال إجاباتك بنجاح. شكراً لمشاركتك!</div>
    <?php endif; ?>

    <?php if ($show_questions && $selected_survey_id): ?>
        
        <a href="patient_dashboard.php?page=surveys" class="btn-back">← العودة إلى قائمة الاستبيانات</a>
        <h3 style="border-right: 4px solid #0ea5e9; padding-right: 1rem; margin: 1rem 0;"><?= htmlspecialchars($survey_title) ?></h3>
        <form method="POST">
            <input type="hidden" name="action" value="submit_survey_answers">
            <input type="hidden" name="survey_id" value="<?= $selected_survey_id ?>">
            
            <?php foreach ($questions as $q): ?>
                <div class="question-box">
                    <div class="question-text"><?= htmlspecialchars($q['question_text']) ?></div>
                    
                    <?php if ($q['question_type'] === 'text'): ?>
                        <textarea name="answers[<?= $q['id'] ?>]" rows="3" style="width:100%; padding:10px; border:2px solid #e2e8f0; border-radius:12px;" required></textarea>
                    
                    <?php elseif ($q['question_type'] === 'rating'): ?>
                        <div class="rating-stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <input type="radio" name="answers[<?= $q['id'] ?>]" value="<?= $i ?>" id="star<?= $q['id'] ?>_<?= $i ?>" required>
                                <label for="star<?= $q['id'] ?>_<?= $i ?>">★</label>
                            <?php endfor; ?>
                        </div>
                    
                    <?php elseif ($q['question_type'] === 'multiple_choice'): ?>
                        <?php 
                        $options = explode(',', $q['options']);
                        foreach ($options as $opt):
                            $opt = trim($opt);
                        ?>
                            <div class="multiple-choice-option">
                                <input type="radio" name="answers[<?= $q['id'] ?>]" value="<?= htmlspecialchars($opt) ?>" required>
                                <label><?= htmlspecialchars($opt) ?></label>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
            <button type="submit" name="submit_survey_answers" style="background:#059669; color:white; border:none; padding:10px 24px; border-radius:40px; cursor:pointer;">إرسال الإجابات</button>
        </form>
    
    <?php else: ?>
        
        <h3 style="border-right: 4px solid #0ea5e9; padding-right: 1rem;"><i class="fas fa-poll"></i> الاستبيانات المتاحة</h3>
        
        <?php if (empty($surveys)): ?>
            <p>لا توجد استبيانات حالياً.</p>
        <?php else: ?>
            <?php foreach ($surveys as $survey): ?>
                <?php
                
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM survey_answers WHERE survey_id = ? AND patient_id = ?");
                $stmt->execute([$survey['id'], $patient_id]);
                $answered = $stmt->fetchColumn() > 0;
                ?>
                <div style="border:1px solid #e2e8f0; border-radius:16px; padding:1rem; margin-bottom:1rem;">
                    <h4><?= htmlspecialchars($survey['title']) ?></h4>
                    <p><?= nl2br(htmlspecialchars($survey['description'])) ?></p>
                    
                    <?php if ($answered): ?>
                        <span style="background:#dcfce7; color:#166534; padding:4px 12px; border-radius:20px; font-size:0.8rem;">✓ تم الإجابة مسبقاً</span>
                    <?php else: ?>
                        <a href="?page=surveys&survey_id=<?= $survey['id'] ?>" style="background:#0ea5e9; color:white; padding:8px 20px; border-radius:30px; text-decoration:none; display:inline-block;">ابدأ الاستبيان</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>