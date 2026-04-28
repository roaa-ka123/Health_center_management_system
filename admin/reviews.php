<?php
require_once 'includes/auth_check.php';
$current_page_name = 'reviews';

require_once '../config/Database.php';
$database = new Database();
$conn = $database->getConnection();


$query = "
    SELECT 
        r.id,
        r.rating,
        r.comment,
        r.entity_type,
        r.created_at,
        p.full_name as patient_name,
        CASE r.entity_type
            WHEN 'doctor' THEN d.full_name
            WHEN 'specialist' THEN s.full_name
            WHEN 'center' THEN 'المركز الصحي العام'
            ELSE 'غير معروف'
        END as rated_entity_name,
        r.entity_id
    FROM ratings r
    JOIN patients p ON r.patient_id = p.id
    LEFT JOIN doctors d ON r.entity_type = 'doctor' AND r.entity_id = d.id
    LEFT JOIN specialists s ON r.entity_type = 'specialist' AND r.entity_id = s.id
    ORDER BY r.created_at DESC
";
$stmt = $conn->query($query);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

$review_count = count($reviews);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقييمات المرضى - المركز الصحي المتقدم</title>
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
    
    .reviews-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
    
    .review-card { 
        background: var(--card-bg);
        border-radius: 16px; 
        padding: 25px; 
        box-shadow: 0 4px 15px var(--card-shadow);
        display: flex; 
        flex-direction: column; 
        justify-content: space-between; 
    }
    
    .review-header { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        margin-bottom: 15px; 
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 10px; 
    }
    
    .rating-stars { color: #facc15; font-size: 1.2rem; }
    
    .rated-entity { 
        font-weight: 600; 
        color: var(--text-color);
        display: flex; 
        align-items: center; 
        gap: 8px; 
    }
    
    .review-comment { 
        line-height: 1.7; 
        color: var(--text-color);
        margin-bottom: 15px; 
        flex-grow: 1; 
    }
    
    .reviewer-info { 
        display: flex; 
        justify-content: space-between; 
        font-size: 0.9rem; 
        color: var(--muted-text);
    }
    
    .entity-type-badge { padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; }
    .badge-doctor { background: #dcfce7; color: #166534; }
    .badge-specialist { background: #fef3c7; color: #d97706; }
    .badge-center { background: #e0f2fe; color: #0ea5e9; }
    
    
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
        .reviews-grid { grid-template-columns: 1fr; }
    }
</style>
    <?php include 'includes/theme_logic.php'; ?>
    <?php include 'includes/sidebar.php' ?>
</head>
<body>
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">
                <span class="icon">⭐</span>
                تقييمات المرضى (<?= $review_count ?>)
            </h1>
        </div>

        <?php if ($review_count > 0): ?>
        <div class="reviews-grid">
            <?php foreach ($reviews as $review): ?>
            <div class="review-card">
                <div>
                    <div class="review-header">
                        <div class="rated-entity">
                            <span class="entity-type-badge badge-<?= $review['entity_type'] ?>">
                                <?= match($review['entity_type']) { 'doctor' => 'طبيب', 'specialist' => 'أخصائي', 'center' => 'المركز العام' } ?>
                            </span>
                             <?= htmlspecialchars($review['rated_entity_name']) ?>
                        </div>
                        <div class="rating-stars">
                            <?= str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']) ?>
                        </div>
                    </div>
                    <p class="review-comment">"<?= nl2br(htmlspecialchars($review['comment'] ?? 'لا يوجد تعليق مكتوب.')) ?>"</p>
                </div>
                <div class="reviewer-info">
                    <span>الناشر: <?= htmlspecialchars($review['patient_name']) ?></span>
                    <span>التاريخ: <?= date('Y-m-d', strtotime($review['created_at'])) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <h3>لا توجد تقييمات مرضى حاليًا</h3>
            <p>عندما يضيف المرضى تقييمات، ستظهر هنا.</p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>