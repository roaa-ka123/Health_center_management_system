<?php
// معالجة إجراءات الاستبيانات والأسئلة
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // إضافة استبيان جديد (بدون أسئلة بعد)
    if ($action === 'add_survey') {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $status = $_POST['status'];
        if (empty($title)) {
            $_SESSION['staff_error'] = "عنوان الاستبيان مطلوب.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO surveys (title, description, created_by, status) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$title, $description, $staff_id, $status])) {
                $_SESSION['staff_message'] = "تم إضافة الاستبيان بنجاح. يمكنك الآن إضافة أسئلة له.";
            } else {
                $_SESSION['staff_error'] = "فشل إضافة الاستبيان.";
            }
        }
        header("Location: staff_dashboard.php?page=surveys");
        exit;
    }
    
    // تعديل استبيان (العنوان والوصف والحالة فقط)
    elseif ($action === 'edit_survey') {
        $id = $_POST['id'];
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $status = $_POST['status'];
        $stmt = $pdo->prepare("UPDATE surveys SET title=?, description=?, status=? WHERE id=?");
        if ($stmt->execute([$title, $description, $status, $id])) {
            $_SESSION['staff_message'] = "تم تحديث الاستبيان.";
        } else {
            $_SESSION['staff_error'] = "فشل التحديث.";
        }
        header("Location: staff_dashboard.php?page=surveys");
        exit;
    }
    
    // حذف استبيان (سيتم حذف الأسئلة والإجابات تلقائياً بسبب ON DELETE CASCADE)
    elseif ($action === 'delete_survey') {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM surveys WHERE id=?");
        if ($stmt->execute([$id])) {
            $_SESSION['staff_message'] = "تم حذف الاستبيان.";
        } else {
            $_SESSION['staff_error'] = "فشل الحذف.";
        }
        header("Location: staff_dashboard.php?page=surveys");
        exit;
    }
    elseif ($action === 'delete_question') {
    $id = $_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM survey_questions WHERE id = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['staff_message'] = "تم حذف السؤال بنجاح.";
    } else {
        $_SESSION['staff_error'] = "فشل حذف السؤال.";
    }
    header("Location: staff_dashboard.php?page=surveys");
    exit;
}
    // تغيير حالة الاستبيان (نشط/مخفي)
    elseif ($action === 'toggle_survey_status') {
        $id = $_POST['id'];
        $new_status = $_POST['new_status'];
        $stmt = $pdo->prepare("UPDATE surveys SET status=? WHERE id=?");
        if ($stmt->execute([$new_status, $id])) {
            $_SESSION['staff_message'] = "تم تغيير حالة الاستبيان.";
        } else {
            $_SESSION['staff_error'] = "فشل تغيير الحالة.";
        }
        header("Location: staff_dashboard.php?page=surveys");
        exit;
    }
    
    // إضافة سؤال للاستبيان
    elseif ($action === 'add_question') {
        $survey_id = $_POST['survey_id'];
        $question_text = trim($_POST['question_text']);
        $question_type = $_POST['question_type'];
        $options = ($question_type === 'multiple_choice') ? trim($_POST['options']) : null;
        $order_index = (int)$_POST['order_index'];
        
        if (empty($question_text)) {
            $_SESSION['staff_error'] = "نص السؤال مطلوب.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO survey_questions (survey_id, question_text, question_type, options, order_index) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$survey_id, $question_text, $question_type, $options, $order_index])) {
                $_SESSION['staff_message'] = "تم إضافة السؤال.";
            } else {
                $_SESSION['staff_error'] = "فشل إضافة السؤال.";
            }
        }
        header("Location: staff_dashboard.php?page=surveys&edit_questions=" . $survey_id);
        exit;
    }
    
    // تعديل سؤال
    elseif ($action === 'edit_question') {
        $question_id = $_POST['question_id'];
        $question_text = trim($_POST['question_text']);
        $question_type = $_POST['question_type'];
        $options = ($question_type === 'multiple_choice') ? trim($_POST['options']) : null;
        $order_index = (int)$_POST['order_index'];
        
        $stmt = $pdo->prepare("UPDATE survey_questions SET question_text=?, question_type=?, options=?, order_index=? WHERE id=?");
        if ($stmt->execute([$question_text, $question_type, $options, $order_index, $question_id])) {
            $_SESSION['staff_message'] = "تم تحديث السؤال.";
        } else {
            $_SESSION['staff_error'] = "فشل التحديث.";
        }
        // الحصول على survey_id من السؤال الحالي
        $stmt2 = $pdo->prepare("SELECT survey_id FROM survey_questions WHERE id=?");
        $stmt2->execute([$question_id]);
        $survey_id = $stmt2->fetchColumn();
        header("Location: staff_dashboard.php?page=surveys&edit_questions=" . $survey_id);
        exit;
    }
    
    // حذف سؤال
    elseif ($action === 'delete_question') {
        $question_id = $_POST['question_id'];
        $stmt = $pdo->prepare("SELECT survey_id FROM survey_questions WHERE id=?");
        $stmt->execute([$question_id]);
        $survey_id = $stmt->fetchColumn();
        $stmt2 = $pdo->prepare("DELETE FROM survey_questions WHERE id=?");
        if ($stmt2->execute([$question_id])) {
            $_SESSION['staff_message'] = "تم حذف السؤال.";
        } else {
            $_SESSION['staff_error'] = "فشل الحذف.";
        }
        header("Location: staff_dashboard.php?page=surveys&edit_questions=" . $survey_id);
        exit;
    }
}

// جلب قائمة الاستبيانات
$surveys = $pdo->query("SELECT * FROM surveys ORDER BY created_at DESC")->fetchAll();

// عرض الردود على استبيان معين (لكل سؤال)
$show_responses = false;
$responses_by_question = [];
$selected_survey_title = '';
$selected_survey_id = 0;
if (isset($_GET['view_responses']) && is_numeric($_GET['view_responses'])) {
    $survey_id = (int)$_GET['view_responses'];
    $selected_survey_id = $survey_id;
    // جلب الأسئلة
    $questions = $pdo->prepare("SELECT * FROM survey_questions WHERE survey_id = ? ORDER BY order_index");
    $questions->execute([$survey_id]);
    $questions_list = $questions->fetchAll();
    // جلب الإجابات لكل سؤال
    foreach ($questions_list as $q) {
        $stmt = $pdo->prepare("SELECT sa.*, p.full_name as patient_name FROM survey_answers sa LEFT JOIN patients p ON sa.patient_id = p.id WHERE sa.question_id = ? ORDER BY sa.created_at DESC");
        $stmt->execute([$q['id']]);
        $responses_by_question[$q['id']] = [
            'question' => $q,
            'answers' => $stmt->fetchAll()
        ];
    }
    $stmt2 = $pdo->prepare("SELECT title FROM surveys WHERE id = ?");
    $stmt2->execute([$survey_id]);
    $selected_survey_title = $stmt2->fetchColumn();
    $show_responses = true;
}

// عرض نموذج إدارة الأسئلة لاستبيان معين
$edit_questions = false;
$current_survey_questions = [];
$current_survey_id = 0;
$current_survey_title = '';
if (isset($_GET['edit_questions']) && is_numeric($_GET['edit_questions'])) {
    $survey_id = (int)$_GET['edit_questions'];
    $current_survey_id = $survey_id;
    $stmt = $pdo->prepare("SELECT * FROM survey_questions WHERE survey_id = ? ORDER BY order_index");
    $stmt->execute([$survey_id]);
    $current_survey_questions = $stmt->fetchAll();
    $stmt2 = $pdo->prepare("SELECT title FROM surveys WHERE id = ?");
    $stmt2->execute([$survey_id]);
    $current_survey_title = $stmt2->fetchColumn();
    $edit_questions = true;
}
?>

<style>
    .survey-card { transition: all 0.2s; border-radius: 28px; }
    .btn-gradient { background: linear-gradient(135deg, #0ea5e9, #8b5cf6); transition: 0.3s; }
    .btn-gradient:hover { transform: translateY(-2px); box-shadow: 0 5px 12px rgba(0,0,0,0.15); }
    .badge-active { background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 40px; font-size: 0.75rem; font-weight: bold; }
    .badge-hidden { background: #f1f5f9; color: #475569; padding: 4px 12px; border-radius: 40px; font-size: 0.75rem; font-weight: bold; }
    .question-item { background: #f8fafc; border-radius: 20px; padding: 1rem; margin-bottom: 1rem; border: 1px solid #e2e8f0; transition: 0.2s; }
    .question-item:hover { border-color: #8b5cf6; box-shadow: 0 2px 8px rgba(139,92,246,0.1); }
    .responses-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    .responses-table th, .responses-table td { padding: 12px; text-align: right; border-bottom: 1px solid #e2e8f0; }
    .responses-table th { background: #f1f5f9; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    .modal-survey { animation: fadeIn 0.2s ease; }
</style>

<div class="card survey-card" style="background: white; border-radius: 28px; padding: 1.8rem; box-shadow: 0 8px 20px rgba(0,0,0,0.05);">
    
    <!-- رأس الصفحة مع الأزرار حسب الوضع -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.8rem; flex-wrap: wrap; gap: 15px;">
        <h3 style="border-right: 5px solid #8b5cf6; padding-right: 1rem;">
            <i class="fas fa-poll" style="color: #8b5cf6;"></i> 
            <?php if ($edit_questions): ?>
                إدارة أسئلة استبيان: <?= htmlspecialchars($current_survey_title) ?>
            <?php elseif ($show_responses): ?>
                الردود على استبيان: <?= htmlspecialchars($selected_survey_title) ?>
            <?php else: ?>
                إدارة الاستبيانات
            <?php endif; ?>
        </h3>
        <div style="display: flex; gap: 12px;">
            <?php if ($edit_questions): ?>
                <a href="staff_dashboard.php?page=surveys" class="btn-gradient" style="color: white; padding: 8px 20px; border-radius: 40px; text-decoration: none;"><i class="fas fa-arrow-right"></i> العودة للاستبيانات</a>
                <button onclick="openAddQuestionModal()" style="background: #059669; color: white; border: none; padding: 8px 20px; border-radius: 40px; cursor: pointer;"><i class="fas fa-plus"></i> إضافة سؤال</button>
            <?php elseif ($show_responses): ?>
                <a href="staff_dashboard.php?page=surveys" class="btn-gradient" style="color: white; padding: 8px 20px; border-radius: 40px; text-decoration: none;"><i class="fas fa-arrow-right"></i> العودة للاستبيانات</a>
            <?php else: ?>
                <button onclick="openAddSurveyModal()" class="btn-gradient" style="color: white; border: none; padding: 8px 22px; border-radius: 40px; cursor: pointer;"><i class="fas fa-plus-circle"></i> استبيان جديد</button>
            <?php endif; ?>
        </div>
    </div>

    <!-- ==================== عرض الردود ==================== -->
    <?php if ($show_responses): ?>
        <?php if (empty($responses_by_question)): ?>
            <div style="text-align: center; padding: 40px; background: #f8fafc; border-radius: 24px;">
                <i class="fas fa-inbox fa-3x" style="color: #cbd5e1;"></i>
                <p>لا توجد أسئلة في هذا الاستبيان بعد.</p>
            </div>
        <?php else: ?>
            <?php foreach ($responses_by_question as $item): ?>
                <div style="margin-top: 2rem;">
                    <h4 style="background: #f1f5f9; padding: 12px 16px; border-radius: 16px;">
                        <?= htmlspecialchars($item['question']['question_text']) ?>
                        <small style="font-weight: normal;">(<?= $item['question']['question_type'] === 'rating' ? 'تقييم 1-10' : ($item['question']['question_type'] === 'multiple_choice' ? 'اختيار من متعدد' : 'نص حر') ?>)</small>
                    </h4>
                    <?php if (empty($item['answers'])): ?>
                        <p style="color: #64748b; padding: 12px;">لا توجد إجابات بعد.</p>
                    <?php else: ?>
                        <table class="responses-table">
                            <thead><tr><th>المريض</th><th>الإجابة</th><th>التاريخ</th></tr></thead>
                            <tbody>
                                <?php foreach ($item['answers'] as $ans): ?>
                                <tr>
                                    <td><?= htmlspecialchars($ans['patient_name'] ?? 'غير معروف') ?></td>
                                    <td><?= nl2br(htmlspecialchars($ans['answer'])) ?></td>
                                    <td><?= date('Y-m-d', strtotime($ans['created_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    <!-- ==================== إدارة الأسئلة ==================== -->
    <?php elseif ($edit_questions): ?>
        <div style="margin-bottom: 1.5rem;">
            <p><strong>الأسئلة الحالية:</strong> (اسحب لتغيير الترتيب - سيتم إضافته لاحقاً)</p>
            <?php if (empty($current_survey_questions)): ?>
                <div style="text-align: center; padding: 30px; background: #f8fafc; border-radius: 24px;">
                    <i class="fas fa-question-circle fa-2x" style="color: #cbd5e1;"></i>
                    <p>لا توجد أسئلة لهذا الاستبيان. أضف أسئلة الآن.</p>
                </div>
            <?php else: ?>
                <?php foreach ($current_survey_questions as $q): ?>
                <div class="question-item" id="question-<?= $q['id'] ?>">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div style="flex: 1;">
                            <strong><?= htmlspecialchars($q['question_text']) ?></strong>
                            <div style="font-size: 0.8rem; color: #64748b; margin-top: 4px;">
                                النوع: <?= $q['question_type'] === 'rating' ? 'تقييم 1-10' : ($q['question_type'] === 'multiple_choice' ? 'اختيار من متعدد' : 'نص حر') ?>
                                <?php if ($q['question_type'] === 'multiple_choice' && $q['options']): ?>
                                    - الخيارات: <?= htmlspecialchars($q['options']) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button onclick='editQuestion(<?= $q['id'] ?>, <?= json_encode($q['question_text']) ?>, <?= json_encode($q['question_type']) ?>, <?= json_encode($q['options']) ?>, <?= $q['order_index'] ?>)' style="background: #f59e0b; color: white; border: none; padding: 5px 12px; border-radius: 20px; cursor: pointer;"><i class="fas fa-edit"></i></button>
                            <button onclick="deleteQuestion(<?= $q['id'] ?>)" style="background: #dc2626; color: white; border: none; padding: 5px 12px; border-radius: 20px; cursor: pointer;"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    <!-- ==================== عرض قائمة الاستبيانات ==================== -->
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr><th style="padding: 15px 12px; text-align: right; border-bottom: 2px solid #e2e8f0;">العنوان</th><th style="padding: 15px 12px; text-align: right; border-bottom: 2px solid #e2e8f0;">الوصف</th><th style="padding: 15px 12px; text-align: right; border-bottom: 2px solid #e2e8f0;">الحالة</th><th style="padding: 15px 12px; text-align: right; border-bottom: 2px solid #e2e8f0;">تاريخ الإضافة</th><th style="padding: 15px 12px; text-align: right; border-bottom: 2px solid #e2e8f0;">الإجراءات</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($surveys as $survey): ?>
                    <tr>
                        <td style="padding: 14px 12px; border-bottom: 1px solid #e2e8f0;"><?= htmlspecialchars($survey['title']) ?></td>
                        <td style="padding: 14px 12px; border-bottom: 1px solid #e2e8f0;"><?= htmlspecialchars($survey['description']) ?></td>
                        <td style="padding: 14px 12px; border-bottom: 1px solid #e2e8f0;">
                            <?= ($survey['status'] ?? 'active') === 'active' ? '<span class="badge-active"><i class="fas fa-eye"></i> نشط</span>' : '<span class="badge-hidden"><i class="fas fa-eye-slash"></i> مخفي</span>' ?>
                        </td>
                        <td style="padding: 14px 12px; border-bottom: 1px solid #e2e8f0;"><?= date('Y-m-d', strtotime($survey['created_at'])) ?></td>
                        <td style="padding: 14px 12px; border-bottom: 1px solid #e2e8f0;">
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <a href="?page=surveys&edit_questions=<?= $survey['id'] ?>" style="background: #8b5cf6; color: white; padding: 6px 14px; border-radius: 30px; text-decoration: none;"><i class="fas fa-list"></i> أسئلة</a>
                                <button onclick='editSurvey(<?= $survey['id'] ?>, <?= json_encode($survey['title']) ?>, <?= json_encode($survey['description']) ?>, <?= json_encode($survey['status'] ?? 'active') ?>)' style="background: #f59e0b; color: white; border: none; padding: 6px 14px; border-radius: 30px;"><i class="fas fa-edit"></i></button>
                                <button onclick="deleteSurvey(<?= $survey['id'] ?>)" style="background: #dc2626; color: white; border: none; padding: 6px 14px; border-radius: 30px;"><i class="fas fa-trash"></i></button>
                                <button onclick="toggleSurveyStatus(<?= $survey['id'] ?>, '<?= ($survey['status'] ?? 'active') === 'active' ? 'hidden' : 'active' ?>')" style="background: #6c757d; color: white; border: none; padding: 6px 14px; border-radius: 30px;"><?= ($survey['status'] ?? 'active') === 'active' ? 'إخفاء' : 'نشر' ?></button>
                                <a href="?page=surveys&view_responses=<?= $survey['id'] ?>" style="background: #0ea5e9; color: white; padding: 6px 14px; border-radius: 30px; text-decoration: none;"><i class="fas fa-comments"></i> الردود</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($surveys)): ?>
                    <tr><td colspan="5" style="text-align: center; padding: 40px;">لا توجد استبيانات مسجلة.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- ========== نماذج مودال ========== -->
<!-- إضافة استبيان جديد -->
<div id="addSurveyModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(3px); justify-content: center; align-items: center; z-index: 1000;">
    <div class="modal-survey" style="background: white; border-radius: 32px; padding: 2rem; width: 90%; max-width: 520px;">
        <h3 style="margin-bottom: 1.5rem;"><i class="fas fa-plus-circle"></i> استبيان جديد</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add_survey">
            <div style="margin-bottom: 1rem;"><label>العنوان</label><input type="text" name="title" required style="width:100%; padding:12px; border-radius:16px; border:2px solid #e2e8f0;"></div>
            <div style="margin-bottom: 1rem;"><label>الوصف</label><textarea name="description" rows="3" style="width:100%; padding:12px; border-radius:16px; border:2px solid #e2e8f0;"></textarea></div>
            <div style="margin-bottom: 1rem;"><label>الحالة</label><select name="status" style="width:100%; padding:12px; border-radius:16px;"><option value="active">نشط</option><option value="hidden">مخفي</option></select></div>
            <div style="display: flex; gap: 1rem; justify-content: flex-end;"><button type="submit" style="background:#059669; color:white; padding:10px 24px; border-radius:40px;">حفظ</button><button type="button" onclick="closeAddSurveyModal()" style="background:#dc2626; color:white; padding:10px 24px; border-radius:40px;">إلغاء</button></div>
        </form>
    </div>
</div>

<!-- مودال إضافة سؤال -->
<div id="addQuestionModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(3px); justify-content: center; align-items: center; z-index: 1000;">
    <div class="modal-survey" style="background: white; border-radius: 32px; padding: 2rem; width: 90%; max-width: 550px;">
        <h3 style="margin-bottom: 1.5rem;"><i class="fas fa-question-circle"></i> إضافة سؤال</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add_question">
            <input type="hidden" name="survey_id" value="<?= $current_survey_id ?>">
            <div style="margin-bottom: 1rem;"><label>نص السؤال</label><input type="text" name="question_text" required style="width:100%; padding:12px; border-radius:16px; border:2px solid #e2e8f0;"></div>
            <div style="margin-bottom: 1rem;"><label>نوع السؤال</label>
                <select name="question_type" id="questionTypeSelect" style="width:100%; padding:12px; border-radius:16px;">
                    <option value="text">نص حر</option>
                    <option value="rating">تقييم رقمي (1-10)</option>
                    <option value="multiple_choice">اختيار من متعدد</option>
                </select>
            </div>
            <div id="optionsField" style="margin-bottom: 1rem; display: none;">
                <label>الخيارات (مفصولة بفواصل)</label>
                <input type="text" name="options" placeholder="مثال: ممتاز,جيد,ضعيف" style="width:100%; padding:12px; border-radius:16px; border:2px solid #e2e8f0;">
            </div>
            <div style="margin-bottom: 1rem;"><label>ترتيب السؤال</label><input type="number" name="order_index" value="0" style="width:100%; padding:12px; border-radius:16px;"></div>
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="submit" style="background:#059669; color:white; padding:10px 24px; border-radius:40px;">حفظ</button>
                <button type="button" onclick="closeAddQuestionModal()" style="background:#dc2626; color:white; padding:10px 24px; border-radius:40px;">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddSurveyModal() { document.getElementById('addSurveyModal').style.display = 'flex'; }
    function closeAddSurveyModal() { document.getElementById('addSurveyModal').style.display = 'none'; }
    function openAddQuestionModal() { document.getElementById('addQuestionModal').style.display = 'flex'; }
    function closeAddQuestionModal() { document.getElementById('addQuestionModal').style.display = 'none'; }
    
    // إظهار/إخفاء حقل الخيارات حسب نوع السؤال
    document.getElementById('questionTypeSelect')?.addEventListener('change', function() {
        let optionsField = document.getElementById('optionsField');
        if (this.value === 'multiple_choice') optionsField.style.display = 'block';
        else optionsField.style.display = 'none';
    });
    
    // تعديل استبيان (مودال)
    function editSurvey(id, title, desc, status) {
        let modal = document.createElement('div');
        modal.className = 'modal-survey';
        modal.style.cssText = 'position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); backdrop-filter:blur(3px); display:flex; justify-content:center; align-items:center; z-index:1001;';
        modal.innerHTML = `
            <div style="background:white; border-radius:32px; padding:2rem; width:90%; max-width:520px;">
                <h3>تعديل الاستبيان</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="edit_survey">
                    <input type="hidden" name="id" value="${id}">
                    <div style="margin-bottom:1rem;"><label>العنوان</label><input type="text" name="title" value="${escapeHtml(title)}" required style="width:100%; padding:12px; border-radius:16px;"></div>
                    <div style="margin-bottom:1rem;"><label>الوصف</label><textarea name="description" rows="3" style="width:100%; padding:12px; border-radius:16px;">${escapeHtml(desc)}</textarea></div>
                    <div style="margin-bottom:1rem;"><label>الحالة</label><select name="status" style="width:100%; padding:12px;"><option value="active" ${status==='active'?'selected':''}>نشط</option><option value="hidden" ${status==='hidden'?'selected':''}>مخفي</option></select></div>
                    <div style="display:flex; gap:1rem; justify-content:flex-end;"><button type="submit" style="background:#059669; color:white; padding:10px 24px; border-radius:40px;">حفظ</button><button type="button" onclick="this.closest('.modal-survey').remove()" style="background:#dc2626; color:white; padding:10px 24px; border-radius:40px;">إلغاء</button></div>
                </form>
            </div>
        `;
        document.body.appendChild(modal);
    }
    
    // تعديل سؤال (مودال)
    function editQuestion(id, text, type, options, order) {
        let modal = document.createElement('div');
        modal.className = 'modal-survey';
        modal.style.cssText = 'position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); backdrop-filter:blur(3px); display:flex; justify-content:center; align-items:center; z-index:1001;';
        let optionsHtml = (type === 'multiple_choice') ? `<div style="margin-bottom:1rem;"><label>الخيارات (مفصولة بفواصل)</label><input type="text" name="options" value="${escapeHtml(options)}" style="width:100%; padding:12px; border-radius:16px;"></div>` : '';
        modal.innerHTML = `
            <div style="background:white; border-radius:32px; padding:2rem; width:90%; max-width:550px;">
                <h3>تعديل السؤال</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="edit_question">
                    <input type="hidden" name="question_id" value="${id}">
                    <div style="margin-bottom:1rem;"><label>نص السؤال</label><input type="text" name="question_text" value="${escapeHtml(text)}" required style="width:100%; padding:12px; border-radius:16px;"></div>
                    <div style="margin-bottom:1rem;"><label>نوع السؤال</label>
                        <select name="question_type" style="width:100%; padding:12px; border-radius:16px;">
                            <option value="text" ${type==='text'?'selected':''}>نص حر</option>
                            <option value="rating" ${type==='rating'?'selected':''}>تقييم رقمي (1-10)</option>
                            <option value="multiple_choice" ${type==='multiple_choice'?'selected':''}>اختيار من متعدد</option>
                        </select>
                    </div>
                    ${optionsHtml}
                    <div style="margin-bottom:1rem;"><label>ترتيب السؤال</label><input type="number" name="order_index" value="${order}" style="width:100%; padding:12px;"></div>
                    <div style="display:flex; gap:1rem; justify-content:flex-end;"><button type="submit" style="background:#059669; color:white; padding:10px 24px; border-radius:40px;">حفظ</button><button type="button" onclick="this.closest('.modal-survey').remove()" style="background:#dc2626; color:white; padding:10px 24px; border-radius:40px;">إلغاء</button></div>
                </form>
            </div>
        `;
        document.body.appendChild(modal);
    }
    
    function deleteSurvey(id) { if(confirm('حذف الاستبيان نهائياً؟')) submitAction('delete_survey', id); }
    function deleteQuestion(id) { if(confirm('حذف هذا السؤال نهائياً؟')) submitAction('delete_question', id); }
    function toggleSurveyStatus(id, newStatus) { if(confirm('تغيير حالة الاستبيان؟')) { let f=document.createElement('form'); f.method='POST'; f.innerHTML=`<input name="action" value="toggle_survey_status"><input name="id" value="${id}"><input name="new_status" value="${newStatus}">`; document.body.appendChild(f); f.submit(); } }
    
    function submitAction(action, id) {
        let f = document.createElement('form');
        f.method = 'POST';
        f.innerHTML = `<input name="action" value="${action}"><input name="id" value="${id}">`;
        document.body.appendChild(f);
        f.submit();
    }
    
    function escapeHtml(str) { if(!str) return ''; return str.replace(/[&<>]/g, m => m==='&'?'&amp;':m==='<'?'&lt;':'&gt;'); }
</script>