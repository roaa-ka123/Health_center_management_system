<div class="card" style="background: white; border-radius: 24px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
    <h3 class="card-title" style="font-size: 1.3rem; margin-bottom: 1rem; border-right: 4px solid #0ea5e9; padding-right: 1rem;">ملخص سريع</h3>
    <div class="grid-2" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
        <div style="background:#e0f2fe; padding:1rem; border-radius:16px;">
            <p>📋 إجمالي المواعيد</p>
            <h2><?= count($appointments_list) ?></h2>
        </div>
        <div style="background:#dcfce7; padding:1rem; border-radius:16px;">
            <p>⭐ عدد التقييمات</p>
            <h2><?= count($ratings_list) ?></h2>
        </div>
    </div>
</div>