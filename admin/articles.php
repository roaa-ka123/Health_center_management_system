<?php
require_once 'includes/auth_check.php';
$current_page_name = 'articles';

require_once '../config/Database.php';
$database = new Database();
$conn = $database->getConnection();

$query = "
    SELECT 
        a.id,
        a.title,
        a.content,
        a.published_at,
        a.author_type,
        CASE 
            WHEN a.author_type = 'doctor' THEN d.full_name
            WHEN a.author_type = 'specialist' THEN s.full_name
            ELSE 'مؤلف غير معروف'
        END as author_name,
        CASE 
            WHEN a.author_type = 'doctor' THEN '👨‍⚕️'
            WHEN a.author_type = 'specialist' THEN '👩‍⚕️'
            ELSE '👤'
        END as author_icon
    FROM articles a
    LEFT JOIN doctors d ON a.author_id = d.id AND a.author_type = 'doctor'
    LEFT JOIN specialists s ON a.author_id = s.id AND a.author_type = 'specialist'
    ORDER BY a.published_at DESC
";

$stmt = $conn->query($query);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المقالات الصحية - المركز الصحي المتقدم</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .main-content {
        margin-right: 260px;
        padding: 30px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .page-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--text-color);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-title .icon {
        font-size: 28px;
    }

    
    .article-card {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 4px 15px var(--card-shadow);
        transition: transform 0.2s ease;
    }

    .article-card:hover {
        transform: translateY(-3px);
    }

    .article-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .article-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-color);
        margin-bottom: 10px;
    }

    .article-meta {
        display: flex;
        align-items: center;
        gap: 20px;
        color: var(--muted-text);
        font-size: 0.95rem;
    }

    .author {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .author-icon {
        font-size: 18px;
    }

    .date {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .date-icon {
        color: var(--muted-text);
    }

    .article-content {
        line-height: 1.8;
        color: var(--text-color);
        font-size: 1.05rem;
    }

    .article-content p {
        margin-bottom: 15px;
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
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .empty-state .icon {
        font-size: 32px;
    }

    .empty-state p {
        color: var(--muted-text);
    }

    
    @media (max-width: 768px) {
        .main-content {
            margin-right: 80px;
        }
        .page-header {
            flex-direction: column;
            align-items: stretch;
        }
        .article-header {
            flex-direction: column;
            align-items: flex-start;
        }
        .article-title {
            font-size: 1.3rem;
        }
    }
</style>
    <?php include 'includes/theme_logic.php'; ?>
    <?php include 'includes/sidebar.php'; ?> 
</head>
<body>
    
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">
                <span class="icon">📝</span>
                المقالات الصحية (<?= count($articles) ?>)
            </h1>
        </div>

        
        <?php if (count($articles) > 0): ?>
            <?php foreach ($articles as $article): ?>
            <div class="article-card">
                <div class="article-header">
                    <h2 class="article-title"><?= htmlspecialchars($article['title']) ?></h2>
                    <div class="article-meta">
                        <div class="author">
                            <span class="author-icon"><?= $article['author_icon'] ?></span>
                            <?= htmlspecialchars($article['author_name']) ?>
                        </div>
                        <div class="date">
                            <span class="date-icon">📅</span>
                            <?= date('d/m/Y', strtotime($article['published_at'])) ?>
                        </div>
                    </div>
                </div>
                <div class="article-content">
                    <p><?= nl2br(htmlspecialchars($article['content'])) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
        <div class="empty-state">
            <h3>
                <span class="icon">📝</span>
                لا توجد مقالات حالياً
            </h3>
            <p>يمكن لل أطباء و الأخصائيين إضافة مقالات طبية عبر نظامهم الخاص.</p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>