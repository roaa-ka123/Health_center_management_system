<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="description" content="المركز الصحي المتقدم — رعاية صحية شاملة بأعلى معايير الجودة والاحترافية.">
    <title>المركز الصحي المتقدم | رعاية طبية متميزة</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f9fafb;
            color: #1e293b;
            line-height: 1.6;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }

       
        header {
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: #0ea5e9;
        }

        .logo span {
            color: #059669;
        }

        .nav-links {
            display: flex;
            gap: 1rem;
        }

        .nav-links a {
            text-decoration: none;
            color: #334155;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #0ea5e9;
        }

        .auth-buttons {
            display: flex;
            gap: 0.75rem;
        }

        .btn {
            display: inline-block;
            padding: 0.65rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .btn-outline {
            border: 2px solid #0ea5e9;
            color: #0ea5e9;
            background: transparent;
        }

        .btn-outline:hover {
            background-color: #f0f9ff;
            transform: translateY(-2px);
        }

        .btn-primary {
            background-color: #0ea5e9;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0284c7;
            transform: translateY(-2px);
        }

        
        .btn-register {
            background: linear-gradient(135deg, #0ea5e9 0%, #243591 100%);
            color: white;
            box-shadow: 0 4px 8px rgba(97, 137, 160, 0.2);
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-register:hover {
            background: linear-gradient(135deg, #137e99 0%, #1c608d 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(37, 99, 192, 0.3);
        }

        
        .hero {
            background: linear-gradient(135deg, #e0f2fe 0%, #f0fdf4 100%);
            padding: 120px 0 80px;
            text-align: center;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1.25rem;
            color: #0f172a;
            font-weight: 700;
        }

        .hero p {
            font-size: 1.25rem;
            color: #475569;
            max-width: 700px;
            margin: 0 auto 2rem;
        }

        
        section {
            padding: 80px 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
            font-size: 2.25rem;
            color: #0f172a;
            font-weight: 700;
        }

        .services {
            background-color: #fff;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }

        .service-card {
            background: #f8fafc;
            padding: 2rem;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .service-card h3 {
            margin: 1rem 0 0.75rem;
            color: #0f172a;
            font-size: 1.25rem;
        }

        
        .team {
            background-color: #f8fafc;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 2.5rem;
            justify-items: center;
        }

        .team-member {
            text-align: center;
            max-width: 180px;
        }

        .avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            font-size: 3rem;
            font-weight: bold;
            color: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .avatar.male {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        }

        .avatar.female {
            background: linear-gradient(135deg, #ec4899, #be123c);
        }

        .team-member h3 {
            margin-bottom: 0.5rem;
            color: #0f172a;
            font-size: 1.1rem;
        }

        .team-member p {
            color: #64748b;
            font-size: 0.95rem;
        }

        
        .articles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .article-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: transform 0.3s;
        }

        .article-card:hover {
            transform: translateY(-5px);
        }

        .article-content {
            padding: 1.5rem;
        }

        .article-content h3 {
            margin-bottom: 0.75rem;
            color: #0f172a;
        }

        
        footer {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #cbd5e1;
            padding: 4rem 0 2rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2.5rem;
            margin-bottom: 2.5rem;
        }

        .footer-col h3 {
            color: white;
            margin-bottom: 1.5rem;
            font-size: 1.25rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .footer-col h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background: #0ea5e9;
            border-radius: 3px;
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col ul li {
            margin-bottom: 0.9rem;
        }

        .footer-col ul li a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-col ul li a:hover {
            color: white;
        }

        .contact-info li {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .copyright {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid #334155;
            color: #94a3b8;
            font-size: 0.95rem;
        }

        
        @media (max-width: 768px) {
            .navbar {
                flex-wrap: wrap;
                gap: 0.75rem;
            }

            .auth-buttons {
                width: 100%;
                justify-content: center;
            }

            .hero h1 {
                font-size: 2.2rem;
            }

            .hero p {
                font-size: 1.1rem;
            }

            .section-title {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>

    
    <header>
        <div class="container">
            <nav class="navbar">
                <div class="logo">المركز<span>الصحي</span></div>
                <div class="nav-links">
                    <a href="#services">الخدمات</a>
                    <a href="#team">الفريق</a>
                    <a href="#articles">المقالات</a>
                </div>
                <div class="auth-buttons">
                    <a href="patient/register.php" class="btn btn-register">👤 إنشاء حساب</a>
                    <a href="login.php" class="btn btn-primary">تسجيل الدخول</a>
                </div>
            </nav>
        </div>
    </header>

    
    <section class="hero">
        <div class="container">
            <h1>رعاية صحية استثنائية بلمسة إنسانية</h1>
            <p>نؤمن في المركز الصحي المتقدم بأن الصحة هي أثمن ما يملكه الإنسان. نقدم لك خدمات طبية متكاملة بكوادر متخصصة ومرافق عالمية.</p>
            <a href="#team" class="btn btn-primary">تعرف على فريقنا</a>
        </div>
    </section>

    
    <section id="services" class="services">
        <div class="container">
            <h2 class="section-title">خدماتنا المتميزة</h2>
            <div class="services-grid">
                <div class="service-card">
                    <h3>استشارات طبية فورية</h3>
                    <p>استشارات مع نخبة من الأطباء في أكثر من 20 تخصصًا طبيًا.</p>
                </div>
                <div class="service-card">
                    <h3>فحوصات مخبرية دقيقة</h3>
                    <p>تحاليل مخبرية بأحدث التقنيات ونتائج خلال 24 ساعة.</p>
                </div>
                <div class="service-card">
                    <h3>رعاية مرضى مزمنين</h3>
                    <p>متابعة دورية وخطط علاجية مخصصة لأمراض السكري والضغط.</p>
                </div>
                <div class="service-card">
                    <h3>استبيانات تقييم صحي</h3>
                    <p>تقييم شامل لصحتك العامة من خلال استبيانات ذكية.</p>
                </div>
            </div>
        </div>
    </section>

    
    <section id="team" class="team">
        <div class="container">
            <h2 class="section-title">فريقنا الطبي المتميز</h2>
            <div class="team-grid">
                <div class="team-member">
                    <div class="avatar male">د</div>
                    <h3>د. أحمد محمد</h3>
                    <p>أخصائي أمراض قلب</p>
                </div>
                <div class="team-member">
                    <div class="avatar female">د</div>
                    <h3>د. فاطمة علي</h3>
                    <p>أخصائية تغذية علاجية</p>
                </div>
                <div class="team-member">
                    <div class="avatar male">د</div>
                    <h3>د. خالد سعيد</h3>
                    <p>طبيب عام</p>
                </div>
                <div class="team-member">
                    <div class="avatar female">أ</div>
                    <h3>أ. منى حسن</h3>
                    <p>أخصائية نفسية</p>
                </div>
                <div class="team-member">
                    <div class="avatar male">د</div>
                    <h3>د. سليمان راشد</h3>
                    <p>أخصائي عظام</p>
                </div>
                <div class="team-member">
                    <div class="avatar female">د</div>
                    <h3>د. نورة عبد الله</h3>
                    <p>أخصائية نساء وولادة</p>
                </div>
            </div>
        </div>
    </section>

    
    <section id="articles" class="services">
        <div class="container">
            <h2 class="section-title">مقالات صحية مفيدة</h2>
            <div class="articles-grid">
                <div class="article-card">
                    <div class="article-content">
                        <h3>الوقاية من أمراض القلب</h3>
                        <p>نصائح ذهبية من أخصائيينا للحفاظ على صحة قلبك وتجنب المضاعفات الخطيرة.</p>
                    </div>
                </div>
                <div class="article-card">
                    <div class="article-content">
                        <h3>أهمية الفحص الدوري</h3>
                        <p>الكشف المبكر عن الأمراض ينقذ الحياة. تعرف على الفحوصات الأساسية التي يجب أن تخضع لها سنويًا.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-col">
                    <h3>المركز الصحي</h3>
                    <p>نسعى لتقديم رعاية صحية متميزة تجمع بين التكنولوجيا الحديثة واللمسة الإنسانية.</p>
                </div>
                <div class="footer-col">
                    <h3>روابط سريعة</h3>
                    <ul>
                        <li><a href="#services">الخدمات</a></li>
                        <li><a href="#team">الفريق الطبي</a></li>
                        <li><a href="#articles">المقالات</a></li>
                        <li><a href="login.php">لوحة التحكم</a></li>
                        <li><a href="register.php">إنشاء حساب مريض</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>اتصل بنا</h3>
                    <ul class="contact-info">
                        <li>📞 <span>0955478963</span></li>
                        <li>✉️ <span>info@healthcenter.com</span></li>
                        <li>📍 <span>دمشق-اتستراد المزة</span></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                &copy; 2025 المركز الصحي المتقدم. جميع الحقوق محفوظة.
            </div>
        </div>
    </footer>

</body>
</html>