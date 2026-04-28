<div class="card" style="background: white; border-radius: 24px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
    <h3 class="card-title" style="font-size: 1.3rem; margin-bottom: 1rem; border-right: 4px solid #0ea5e9; padding-right: 1rem;">سجلاتي الطبية</h3>
    <?php if (empty($medical_list)): ?>
        <p>لا توجد سجلات طبية متاحة حالياً.</p>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr><th style="padding: 12px; text-align: right; border-bottom: 1px solid #e2e8f0;">التاريخ</th><th style="padding: 12px; text-align: right; border-bottom: 1px solid #e2e8f0;">التشخيص</th><th style="padding: 12px; text-align: right; border-bottom: 1px solid #e2e8f0;">العلاج</th><th style="padding: 12px; text-align: right; border-bottom: 1px solid #e2e8f0;">ملاحظات الطبيب</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($medical_list as $record): ?>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;"><?= htmlspecialchars($record['record_date']) ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;"><?= nl2br(htmlspecialchars($record['diagnosis'])) ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;"><?= nl2br(htmlspecialchars($record['treatment'])) ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #e2e8f0;"><?= nl2br(htmlspecialchars($record['doctor_notes'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>