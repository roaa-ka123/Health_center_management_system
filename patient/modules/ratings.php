<?php

$doctors_list = $pdo->query("SELECT id, full_name FROM doctors WHERE status = 'approved' ORDER BY full_name")->fetchAll();
$specialists_list = $pdo->query("SELECT id, full_name FROM specialists WHERE status = 'approved' ORDER BY full_name")->fetchAll();
?>

<style>
    .rating-form-group { margin-bottom: 1rem; }
    .rating-form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
    .rating-form-group select, .rating-form-group input, .rating-form-group textarea {
        width: 100%; padding: 10px; border: 2px solid #e2e8f0; border-radius: 12px; font-family: inherit;
    }
</style>


<div class="card" style="background: white; border-radius: 24px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 2rem;">
    <h3 class="card-title" style="font-size: 1.3rem; margin-bottom: 1rem; border-right: 4px solid #0ea5e9; padding-right: 1rem;">إضافة تقييم جديد</h3>
    <form method="POST" id="ratingForm">
        <input type="hidden" name="action" value="add_rating">
        <input type="hidden" name="entity_id" id="entity_id">

        <div class="rating-form-group">
            <label>نوع الخدمة</label>
            <select name="entity_type" id="entity_type" required>
                <option value="center">المركز ككل</option>
                <option value="doctor">طبيب</option>
                <option value="specialist">أخصائي</option>
            </select>
        </div>

        
        <div id="dynamic_select_container" class="rating-form-group"></div>

        <div class="rating-form-group">
            <label>التقييم (1-5)</label>
            <input type="number" name="rating" min="1" max="5" required>
        </div>
        <div class="rating-form-group">
            <label>تعليقك</label>
            <textarea name="comment" rows="3" required></textarea>
        </div>
        <button type="submit" style="background:#0ea5e9; color:white; border:none; padding:10px 20px; border-radius:12px;">إضافة تقييم</button>
    </form>
</div>


<div class="card" style="background: white; border-radius: 24px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
    <h3 class="card-title" style="font-size: 1.3rem; margin-bottom: 1rem; border-right: 4px solid #0ea5e9; padding-right: 1rem;">تقييماتي السابقة</h3>
    <?php if (empty($ratings_list)): ?>
        <p>لم تقم بإضافة أي تقييم بعد.</p>
    <?php else: ?>
        <?php foreach ($ratings_list as $rate):
            
            $entity_name = '';
            if ($rate['entity_type'] == 'center') $entity_name = 'المركز الصحي المتقدم';
            elseif ($rate['entity_type'] == 'doctor') {
                $st = $pdo->prepare("SELECT full_name FROM doctors WHERE id = ?");
                $st->execute([$rate['entity_id']]);
                $entity_name = $st->fetchColumn() ?: 'طبيب غير معروف';
            } else {
                $st = $pdo->prepare("SELECT full_name FROM specialists WHERE id = ?");
                $st->execute([$rate['entity_id']]);
                $entity_name = $st->fetchColumn() ?: 'أخصائي غير معروف';
            }
        ?>
            <div style="border-bottom:1px solid #e2e8f0; padding:1rem 0;">
                <p><strong>النوع:</strong> <?= $rate['entity_type'] == 'center' ? 'المركز' : ($rate['entity_type'] == 'doctor' ? 'طبيب' : 'أخصائي') ?> |
                <strong>الجهة:</strong> <?= htmlspecialchars($entity_name) ?> |
                <strong>التقييم:</strong> <?= $rate['rating'] ?>/5</p>
                <p><?= nl2br(htmlspecialchars($rate['comment'])) ?></p>
                <button onclick="showUpdateRating(<?= $rate['id'] ?>, <?= $rate['rating'] ?>, '<?= addslashes($rate['comment']) ?>', '<?= $rate['entity_type'] ?>', <?= $rate['entity_id'] ?>)" style="background:#0ea5e9; color:white; border:none; padding:6px 12px; border-radius:8px;">تحديث</button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>


<div id="updateRatingModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:1000;">
    <div style="background:white; padding:2rem; border-radius:24px; width:90%; max-width:500px;">
        <h3>تحديث التقييم</h3>
        <form method="POST" id="updateRatingForm">
            <input type="hidden" name="action" value="update_rating">
            <input type="hidden" name="rating_id" id="update_rating_id">
            <input type="hidden" name="entity_type" id="update_entity_type">
            <input type="hidden" name="entity_id" id="update_entity_id">
            <div id="update_dynamic_container" class="rating-form-group"></div>
            <div class="rating-form-group"><label>التقييم (1-5)</label><input type="number" name="rating" id="update_rating_value" min="1" max="5" required></div>
            <div class="rating-form-group"><label>تعليقك</label><textarea name="comment" id="update_rating_comment" rows="3" required></textarea></div>
            <button type="submit" style="background:#0ea5e9; color:white; border:none; padding:8px 16px; border-radius:12px;">تحديث</button>
            <button type="button" onclick="closeUpdateModal()" style="background:#dc2626; color:white; border:none; padding:8px 16px; border-radius:12px;">إلغاء</button>
        </form>
    </div>
</div>

<script>
    
    var doctors = <?= json_encode($doctors_list) ?>;
    var specialists = <?= json_encode($specialists_list) ?>;

    function updateDynamicField(entityType, containerId, hiddenId, selectedId = null) {
        var container = document.getElementById(containerId);
        var hidden = document.getElementById(hiddenId);
        if (entityType === 'center') {
            container.innerHTML = '<div class="rating-form-group"><label>المركز الصحي المتقدم</label><input type="text" value="المركز الصحي المتقدم" disabled style="background:#f1f5f9;"></div>';
            if (hidden) hidden.value = 0;
        } else if (entityType === 'doctor') {
            var options = '<option value="">-- اختر الطبيب --</option>';
            for (var i = 0; i < doctors.length; i++) {
                var selected = (selectedId == doctors[i].id) ? 'selected' : '';
                options += `<option value="${doctors[i].id}" ${selected}>${doctors[i].full_name}</option>`;
            }
            container.innerHTML = `<div class="rating-form-group"><label>اختر الطبيب</label><select id="${containerId}_select" class="dynamic-select">${options}</select></div>`;
            var select = document.getElementById(containerId + '_select');
            select.addEventListener('change', function() { if (hidden) hidden.value = this.value; });
            if (hidden && !selectedId) hidden.value = '';
        } else if (entityType === 'specialist') {
            var options = '<option value="">-- اختر الأخصائي --</option>';
            for (var i = 0; i < specialists.length; i++) {
                var selected = (selectedId == specialists[i].id) ? 'selected' : '';
                options += `<option value="${specialists[i].id}" ${selected}>${specialists[i].full_name}</option>`;
            }
            container.innerHTML = `<div class="rating-form-group"><label>اختر الأخصائي</label><select id="${containerId}_select" class="dynamic-select">${options}</select></div>`;
            var select = document.getElementById(containerId + '_select');
            select.addEventListener('change', function() { if (hidden) hidden.value = this.value; });
            if (hidden && !selectedId) hidden.value = '';
        }
    }

    
    document.getElementById('entity_type').addEventListener('change', function() {
        updateDynamicField(this.value, 'dynamic_select_container', 'entity_id');
    });
    
    updateDynamicField('center', 'dynamic_select_container', 'entity_id');

    
    function showUpdateRating(id, rating, comment, entityType, entityId) {
        document.getElementById('update_rating_id').value = id;
        document.getElementById('update_rating_value').value = rating;
        document.getElementById('update_rating_comment').value = comment;
        document.getElementById('update_entity_type').value = entityType;
        document.getElementById('update_entity_id').value = entityId;
        updateDynamicField(entityType, 'update_dynamic_container', 'update_entity_id', entityId);
        document.getElementById('updateRatingModal').style.display = 'flex';
    }
    function closeUpdateModal() { document.getElementById('updateRatingModal').style.display = 'none'; }
</script>