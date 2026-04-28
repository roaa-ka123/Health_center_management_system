<div class="card" style="background: white; border-radius: 24px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
    <h3 class="card-title" style="font-size: 1.3rem; margin-bottom: 1rem; border-right: 4px solid #0ea5e9; padding-right: 1rem;">سجل المواعيد وحالتها</h3>
    <?php if (empty($appointments_list)): ?>
        <p>لا توجد مواعيد مسجلة.</p>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr><th style="padding: 12px; text-align: right; border-bottom: 1px solid #e2e8f0;">الطبيب</th><th style="padding: 12px; text-align: right; border-bottom: 1px solid #e2e8f0;">التاريخ</th><th style="padding: 12px; text-align: right; border-bottom: 1px solid #e2e8f0;">الوقت</th><th style="padding: 12px; text-align: right; border-bottom: 1px solid #e2e8f0;">الحالة</th><th style="padding: 12px; text-align: right; border-bottom: 1px solid #e2e8f0;">ملاحظات</th><th style="padding: 12px; text-align: right; border-bottom: 1px solid #e2e8f0;">إجراءات</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments_list as $app): ?>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;"><?= htmlspecialchars($app['doctor_name']) ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;"><?= htmlspecialchars($app['appointment_date']) ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;"><?= htmlspecialchars($app['appointment_time']) ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;" class="status-<?= $app['status'] ?>"><?= ['pending'=>'قيد الانتظار', 'confirmed'=>'مؤكد', 'cancelled'=>'ملغي', 'completed'=>'مكتمل'][$app['status']] ?? $app['status'] ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;"><?= htmlspecialchars($app['notes']) ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                            <?php if ($app['status'] != 'cancelled'): ?>
                                <button onclick="editAppointment(<?= $app['id'] ?>, '<?= $app['appointment_date'] ?>', '<?= $app['appointment_time'] ?>', '<?= addslashes($app['notes']) ?>')" style="background:#f59e0b; color:white; border:none; padding:4px 10px; border-radius:8px;">تعديل</button>
                                <button onclick="cancelAppointment(<?= $app['id'] ?>)" style="background:#dc2626; color:white; border:none; padding:4px 10px; border-radius:8px;">إلغاء</button>
                            <?php else: ?>
                                <span>ملغي</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>


<div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:1000;">
    <div style="background:white; padding:2rem; border-radius:24px; width:90%; max-width:500px;">
        <h3>تعديل الموعد</h3>
        <form method="POST" id="editForm">
            <input type="hidden" name="action" value="edit_appointment">
            <input type="hidden" name="appointment_id" id="edit_id">
            <div class="form-group">
                <label>التاريخ</label>
                <input type="date" name="appointment_date" id="edit_date" required style="width:100%; padding:10px; border:2px solid #e2e8f0; border-radius:12px;">
            </div>
            <div class="form-group">
                <label>الوقت</label>
                <input type="time" name="appointment_time" id="edit_time" required style="width:100%; padding:10px; border:2px solid #e2e8f0; border-radius:12px;">
            </div>
            <div class="form-group">
                <label>ملاحظات</label>
                <textarea name="notes" id="edit_notes" rows="2" style="width:100%; padding:10px; border:2px solid #e2e8f0; border-radius:12px;"></textarea>
            </div>
            <button type="submit" style="background:#0ea5e9; color:white; border:none; padding:8px 16px; border-radius:12px;">حفظ التعديل</button>
            <button type="button" onclick="closeModal()" style="background:#dc2626; color:white; border:none; padding:8px 16px; border-radius:12px;">إلغاء</button>
        </form>
    </div>
</div>


<div id="cancelModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
    <div style="background:white; padding:2rem; border-radius:24px; text-align:center;">
        <p>هل أنت متأكد من إلغاء هذا الموعد؟</p>
        <form method="POST" id="cancelForm">
            <input type="hidden" name="action" value="cancel_appointment">
            <input type="hidden" name="appointment_id" id="cancel_id">
            <button type="submit" style="background:#dc2626; color:white; border:none; padding:8px 16px; border-radius:12px;">نعم، إلغاء</button>
            <button type="button" onclick="closeCancelModal()" style="background:#6c757d; color:white; border:none; padding:8px 16px; border-radius:12px;">تراجع</button>
        </form>
    </div>
</div>

<script>
    
    function getTodayDate() {
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        return `${yyyy}-${mm}-${dd}`;
    }

    function editAppointment(id, date, time, notes) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_date').value = date;
        document.getElementById('edit_time').value = time;
        document.getElementById('edit_notes').value = notes;
        
       
        const today = getTodayDate();
        const dateInput = document.getElementById('edit_date');
        dateInput.setAttribute('min', today);
        
        
        if (date < today) {
            
            console.log('تنبيه: هذا الموعد بتاريخ قديم، يمكنك تحديثه إلى تاريخ مستقبلي.');
        }
        
        document.getElementById('editModal').style.display = 'flex';
    }
    
    function cancelAppointment(id) {
        document.getElementById('cancel_id').value = id;
        document.getElementById('cancelModal').style.display = 'flex';
    }
    
    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
    }
    
    function closeCancelModal() {
        document.getElementById('cancelModal').style.display = 'none';
    }
    
    
    document.addEventListener('DOMContentLoaded', function() {
        const editForm = document.getElementById('editForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                const dateInput = document.getElementById('edit_date');
                const selectedDate = dateInput.value;
                const today = getTodayDate();
                if (selectedDate < today) {
                    e.preventDefault();
                    alert('لا يمكنك تحديد تاريخ سابق. الرجاء اختيار تاريخ اليوم أو مستقبلي.');
                    return false;
                }
            });
        }
    });
</script>