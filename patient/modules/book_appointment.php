<?php
$today = date('Y-m-d');
?>

<div class="card" style="background: white; border-radius: 24px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
    <h3 class="card-title" style="font-size: 1.3rem; margin-bottom: 1rem; border-right: 4px solid #0ea5e9; padding-right: 1rem;">حجز موعد جديد</h3>
    <form method="POST">
        <input type="hidden" name="action" value="book_appointment">
        <div class="form-group">
            <label>اختر الطبيب</label>
            <select name="doctor_name" required style="width:100%; padding:10px; border:2px solid #e2e8f0; border-radius:12px;">
                <option value="" disabled selected>-- اختر الطبيب --</option>
                <?php foreach ($doctors_list as $doctor): ?>
                    <option value="<?= htmlspecialchars($doctor['full_name']) ?>"><?= htmlspecialchars($doctor['full_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>التاريخ</label>
            <input type="date" name="appointment_date" id="appointment_date" required 
                   min="<?= $today ?>" 
                   style="width:100%; padding:10px; border:2px solid #e2e8f0; border-radius:12px;">
        </div>
        <div class="form-group">
            <label>الوقت</label>
            <input type="time" name="appointment_time" required style="width:100%; padding:10px; border:2px solid #e2e8f0; border-radius:12px;">
        </div>
        <div class="form-group">
            <label>ملاحظات إضافية</label>
            <textarea name="notes" rows="3" style="width:100%; padding:10px; border:2px solid #e2e8f0; border-radius:12px;"></textarea>
        </div>
        <button type="submit" style="background:#0ea5e9; color:white; border:none; padding:10px 20px; border-radius:12px;">حجز الموعد</button>
    </form>
    <?php if (empty($doctors_list)): ?>
        <div class="alert-error" style="margin-top:1rem;">لا يوجد أطباء متاحون حالياً للحجز.</div>
    <?php endif; ?>
</div>

<script>
    
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        const dateInput = document.getElementById('appointment_date');
        if (dateInput) {
            dateInput.setAttribute('min', today);
        }
    });
</script>