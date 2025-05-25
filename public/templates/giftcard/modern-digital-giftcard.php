<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Подарочная карта</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #8B5CF6; /* Фиолетовый основной цвет */
            --primary-dark: #6D28D9;
            --primary-light: #C4B5FD;
            --secondary: #EC4899; /* Розовый акцент */
            --dark: #111827;
            --light: #ffffff;
            --accent: #F59E0B; /* Золотой акцент */
            --gray: #9CA3AF;
            --surface: #F9FAFB;
            --surface-2: #F3F4F6;
            
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 6px 12px rgba(0, 0, 0, 0.1), 0 0 4px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 15px 25px rgba(0, 0, 0, 0.12), 0 0 6px rgba(0, 0, 0, 0.08);
            
            --border-radius-sm: 0.5rem;
            --border-radius: 1rem;
            --border-radius-lg: 1.5rem;
            --border-radius-xl: 2rem;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Open Sans', sans-serif;
            background: linear-gradient(135deg, #F1F5F9 0%, #E2E8F0 100%);
            color: var(--dark);
            min-height: 100vh;
            line-height: 1.5;
            overflow-x: hidden;
            padding: 0;
            margin: 0;
        }
        
        .certificate-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            perspective: 1000px;
            min-height: auto;
            height: auto;
            overflow: visible;
        }
        
        @media (min-width: 768px) {
            .certificate-container {
                grid-template-columns: 1fr 1fr;
                min-height: 100vh;
                align-items: center;
            }
        }

        /* Мобильная версия */
        @media (max-width: 767px) {
            body {
                min-height: auto;
                height: auto;
                overflow-y: auto;
                padding-bottom: 2rem;
            }
            
            .certificate-container {
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
                padding: 1.5rem;
                height: auto;
            }
        }
        
        /* Основная карточка подарочной карты */
        .certificate-main {
            position: relative;
            transform-style: preserve-3d;
            animation: floatUp 1.2s ease-out forwards;
        }
        
        .certificate-card {
            background: linear-gradient(135deg, #5B21B6, #7C3AED, #8B5CF6);
            border-radius: var(--border-radius-lg);
            padding: 3rem 2.5rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(139, 92, 246, 0.3), 0 0 20px rgba(139, 92, 246, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transform-style: preserve-3d;
            transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
        }
        
        /* Эффект сияния на подарочной карте */
        .certificate-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(45deg);
            animation: shine 10s infinite linear;
            pointer-events: none;
        }
        
        @keyframes shine {
            0% {
                transform: translateX(-100%) rotate(45deg);
            }
            100% {
                transform: translateX(100%) rotate(45deg);
            }
        }
        
        .certificate-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(139, 92, 246, 0.4), 0 5px 25px rgba(139, 92, 246, 0.3);
        }
        
        /* Геометрические элементы декора */
        .geometric-shape {
            position: absolute;
            opacity: 0.15;
            z-index: 0;
            pointer-events: none;
        }
        
        .circle-top-right {
            top: -80px;
            right: -80px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: radial-gradient(circle, var(--secondary) 0%, rgba(236, 72, 153, 0.2) 70%);
            mix-blend-mode: overlay;
            animation: float 8s ease-in-out infinite;
        }
        
        .circle-bottom-left {
            bottom: -100px;
            left: -100px;
            width: 250px;
            height: 250px;
            border-radius: 50%;
            background: radial-gradient(circle, var(--accent) 0%, rgba(245, 158, 11, 0.2) 70%);
            mix-blend-mode: overlay;
            animation: float 10s ease-in-out infinite reverse;
        }
        
        /* Узор на фоне подарочной карты */
        .pattern {
            position: absolute;
            z-index: 0;
            opacity: 0.05;
            background-image: url("data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 0h10v10H0V0zm10 10h10v10H10V10z' fill='%23ffffff' fill-opacity='1' fill-rule='evenodd'/%3E%3C/svg%3E");
            background-size: 20px 20px;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        
        /* Заголовок и лого */
        .header-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 2;
            margin-bottom: 3rem;
        }
        
        .certificate-type {
            position: absolute;
            top: -10px;
            left: 0;
            background: var(--secondary);
            color: white;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0.35rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 3px 10px rgba(236, 72, 153, 0.4);
        }
        
        .company-branding {
            position: relative;
            padding-top: 1rem;
        }
        
        .company-name {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--light);
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            position: relative;
            display: inline-block;
            margin-top: 0.5rem;
        }
        
        .company-name::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 40%;
            height: 3px;
            background: var(--light);
            border-radius: 2px;
            opacity: 0.8;
        }
        
        .company-logo {
            max-width: 120px;
            max-height: 80px;
            object-fit: contain;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.2));
            transition: all 0.4s ease;
        }
        
        .company-logo:hover {
            transform: scale(1.05);
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.3));
        }
        
        /* Основной контент подарочной карты */
        .certificate-content {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 1.5rem 0;
            color: var(--light);
        }
        
        .content-divider {
            margin: 1.5rem auto;
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, rgba(255,255,255,0.2), rgba(255,255,255,0.8), rgba(255,255,255,0.2));
            border-radius: 2px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .certificate-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 2rem;
            color: var(--light);
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        
        /* Сумма подарочной карты */
        .amount-wrapper {
            position: relative;
            display: inline-block;
            margin: 2.5rem 0;
            padding: 1rem 2rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: var(--border-radius);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .amount-wrapper:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .amount-label {
            display: block;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 0.5rem;
        }
        
        .certificate-amount {
            font-family: 'Montserrat', sans-serif;
            font-size: 3.5rem;
            font-weight: 800;
            color: var(--light);
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            line-height: 1.2;
        }
        
        /* Получатель */
        .recipient-section {
            margin: 2rem 0;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: var(--border-radius);
            border: 1px solid rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(5px);
        }
        
        .recipient-label {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .recipient-name {
            font-family: 'Montserrat', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--light);
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        
        /* Сообщение */
        .message-container {
            position: relative;
            z-index: 2;
            margin: 2rem 0;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.08);
            border-radius: var(--border-radius);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: var(--shadow-sm);
            backdrop-filter: blur(5px);
        }
        
        .message-text {
            font-style: italic;
            color: var(--light);
            font-weight: 400;
            font-size: 1rem;
            text-align: center;
        }
        
        /* Срок действия */
        .validity-section {
            display: flex;
            justify-content: space-around;
            gap: 1.5rem;
            margin: 2rem 0 1rem;
            position: relative;
            z-index: 2;
        }
        
        .validity-item {
            flex: 1;
            padding: 1.2rem;
            background: rgba(255, 255, 255, 0.08);
            border-radius: var(--border-radius);
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }
        
        .validity-item:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
            background: rgba(255, 255, 255, 0.12);
        }
        
        .validity-label {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 0.5rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .validity-date {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--light);
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }
        
        /* Второй раздел с QR-кодом и информацией */
        .certificate-details {
            position: relative;
            animation: fadeInRight 1.2s ease-out forwards 0.3s;
            opacity: 0;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .details-card {
            background: var(--light);
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--shadow-lg);
            border: 1px solid rgba(139, 92, 246, 0.1);
            position: relative;
            overflow: hidden;
            flex: 1;
        }
        
        .card-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.03;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'%3E%3Cpath d='M0 12l11 2-9-7 9 7-11 2zm0 0l11-2-9 7 9-7-11-2z' fill='%238B5CF6' fill-opacity='0.4'/%3E%3C/svg%3E");
            background-size: 12px 12px;
            z-index: 0;
            pointer-events: none;
        }
        
        /* Информация о подарочной карте */
        .cert-info-card {
            display: flex;
            flex-direction: column;
        }
        
        .cert-info-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }
        
        .cert-info-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark);
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .cert-info-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 30px;
            height: 2px;
            background: var(--primary);
            box-shadow: 0 1px 5px rgba(139, 92, 246, 0.3);
        }
        
        .certificate-number {
            display: inline-block;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--primary-dark);
            background: rgba(139, 92, 246, 0.1);
            padding: 0.5rem 0.8rem;
            border-radius: var(--border-radius-sm);
            border: 1px solid rgba(139, 92, 246, 0.1);
        }
        
        .certificate-number::before {
            content: "№";
            margin-right: 0.25rem;
            opacity: 0.7;
        }
        
        /* QR код */
        .qr-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 1.5rem 0;
            position: relative;
            z-index: 1;
            text-align: center;
        }
        
        .qr-code {
            width: 160px;
            height: 160px;
            background: white;
            border-radius: var(--border-radius-sm);
            padding: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(139, 92, 246, 0.2);
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }
        
        .qr-code:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15), 0 0 15px rgba(139, 92, 246, 0.2);
        }
        
        .qr-code img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .qr-caption {
            font-size: 0.85rem;
            color: var(--gray);
            margin-top: 0.5rem;
        }
        
        /* Список преимуществ и способы использования */
        .usage-guide {
            margin-top: 2rem;
        }
        
        .usage-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 1rem;
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .usage-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 30px;
            height: 2px;
            background: var(--primary);
        }
        
        .usage-list {
            list-style-type: none;
            padding: 0;
        }
        
        .usage-item {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 1rem;
            padding: 0.75rem;
            background: var(--surface);
            border-radius: var(--border-radius-sm);
            transition: all 0.2s ease;
        }
        
        .usage-item:hover {
            background: var(--surface-2);
            transform: translateX(5px);
        }
        
        .usage-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 30px;
            height: 30px;
            background: rgba(139, 92, 246, 0.1);
            color: var(--primary);
            border-radius: 50%;
        }
        
        .usage-text {
            font-size: 0.9rem;
            color: var(--dark);
        }
        
        /* Счетчик */
        .countdown-section {
            margin: 1.5rem 0;
            position: relative;
            z-index: 1;
        }
        
        .countdown-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }
        
        .countdown-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 0.9rem;
            color: var(--gray);
            font-weight: 600;
        }
        
        .progress-container {
            height: 8px;
            background: var(--surface-2);
            border-radius: 4px;
            overflow: hidden;
            position: relative;
            margin-top: 1rem;
            border: 1px solid rgba(139, 92, 246, 0.1);
        }
        
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-light), var(--primary));
            border-radius: 4px;
            position: relative;
            transition: width 1s ease;
        }
        
        .countdown-display {
            text-align: center;
            background: var(--surface);
            color: var(--dark);
            font-family: 'Montserrat', sans-serif;
            font-size: 1.5rem;
            font-weight: 600;
            padding: 1rem;
            border-radius: var(--border-radius-sm);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(139, 92, 246, 0.1);
            box-shadow: var(--shadow-sm);
        }
        
        /* Бренд подарочной карты */
        .brand-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: 2rem;
            padding: 1.5rem;
            background: var(--surface);
            border-radius: var(--border-radius);
            border: 1px solid rgba(139, 92, 246, 0.1);
        }
        
        .brand-logo {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }
        
        .brand-details {
            flex: 1;
        }
        
        .brand-name {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--dark);
            margin-bottom: 0.25rem;
        }
        
        .brand-description {
            font-size: 0.85rem;
            color: var(--gray);
        }

        /* Анимации */
        @keyframes floatUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInRight {
            0% {
                opacity: 0;
                transform: translateX(30px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        /* Медиа-запросы для адаптивности */
        @media (max-width: 1024px) {
            .certificate-container {
                padding: 1.5rem;
            }
            
            .certificate-card {
                padding: 2rem;
            }
            
            .recipient-name {
                font-size: 1.8rem;
            }
            
            .certificate-amount {
                font-size: 3rem;
            }
        }
        
        @media (max-width: 767px) {
            .certificate-card, 
            .details-card {
                padding: 1.5rem;
            }
            
            .header-section {
                flex-direction: column-reverse;
                gap: 1.5rem;
                text-align: center;
            }
            
            .certificate-type {
                position: relative;
                display: inline-block;
                margin-bottom: 1rem;
                transform: translateY(0);
                top: 0;
            }
            
            .company-name::after {
                left: 50%;
                transform: translateX(-50%);
            }
            
            .company-name {
                display: block;
                text-align: center;
            }
            
            .recipient-name {
                font-size: 1.6rem;
            }
            
            .certificate-amount {
                font-size: 2.8rem;
            }
            
            .validity-section {
                flex-direction: column;
                gap: 1rem;
            }
            
            .cert-info-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .cert-info-title::after {
                left: 50%;
                transform: translateX(-50%);
            }
            
            .usage-title {
                text-align: center;
            }
            
            .usage-title::after {
                left: 50%;
                transform: translateX(-50%);
            }
            
            .certificate-main,
            .certificate-details,
            .certificate-card,
            .details-card {
                animation: none;
                transform: none;
                opacity: 1;
            }
        }
        
        @media (max-width: 480px) {
            .certificate-container {
                padding: 1rem;
            }
            
            .certificate-card, 
            .details-card {
                padding: 1.2rem;
            }
            
            .recipient-name {
                font-size: 1.4rem;
            }
            
            .certificate-amount {
                font-size: 2.2rem;
                padding: 0.5rem 1rem;
            }
            
            .countdown-display {
                font-size: 1.3rem;
                padding: 0.75rem;
            }
        }
    </style>
</head>

<body>
    <div class="certificate-container">
        <!-- Первый блок - основная подарочная карта -->
        <div class="certificate-main">
            <div class="certificate-card">
                <!-- Декоративные элементы -->
                <div class="geometric-shape circle-top-right"></div>
                <div class="geometric-shape circle-bottom-left"></div>
                <div class="pattern"></div>
                
                <!-- Заголовок и лого -->
                <div class="header-section">
                    <div class="company-branding">
                        <div class="certificate-type">ПОДАРОЧНАЯ КАРТА</div>
                        <h1 class="company-name"><?= $company_name ?></h1>
                    </div>
                    <img src="<?= $company_logo ?>" alt="Логотип компании" class="company-logo">
                </div>
                
                <!-- Основной контент подарочной карты -->
                <div class="certificate-content">
                    <h2 class="certificate-title">Gift Card</h2>
                    <div class="content-divider"></div>
                    
                    <!-- Сумма подарочной карты -->
                    <div class="amount-wrapper">
                        <span class="amount-label">Номинал</span>
                        <div class="certificate-amount"><?= $amount ?></div>
                    </div>
                    
                    <!-- Получатель -->
                    <div class="recipient-section">
                        <div class="recipient-label">Персональная подарочная карта для</div>
                        <h2 class="recipient-name"><?= $recipient_name ?></h2>
                    </div>
                    
                    <!-- Сообщение - выводим только если есть значение -->
                    <?php if (!empty($message)): ?>
                    <div class="message-container">
                        <p class="message-text"><?php echo $message; ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Срок действия - выводим только если есть обе даты -->
                    <?php if (!empty($valid_from) && !empty($valid_until)): ?>
                    <div class="validity-section">
                        <div class="validity-item">
                            <div class="validity-label">Дата активации</div>
                            <div class="validity-date"><?= $valid_from ?></div>
                        </div>
                        <div class="validity-item">
                            <div class="validity-label">Действительна до</div>
                            <div class="validity-date"><?= $valid_until ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Второй блок - детали подарочной карты -->
        <div class="certificate-details">
            <!-- Информация о подарочной карте и QR-код -->
            <div class="details-card cert-info-card">
                <div class="card-pattern"></div>
                
                <div class="cert-info-header">
                    <h3 class="cert-info-title">Информация о подарочной карте</h3>
                    <div class="certificate-number"><?= $certificate_number ?></div>
                </div>
                
                <div class="qr-section">
                    <div class="qr-code" id="qr-code">
                        <!-- QR-код будет добавлен с помощью JS -->
                    </div>
                    <div class="qr-caption">Отсканируйте QR-код, чтобы проверить баланс</div>
                </div>
                
                <!-- Счетчик времени -->
                <div class="countdown-section">
                    <div class="countdown-header">
                        <div class="countdown-title">Осталось времени для использования</div>
                    </div>
                    <div class="countdown-display" id="countdown-timer">
                        Загрузка...
                    </div>
                    <div class="progress-container">
                        <div class="progress-bar" id="countdown-progress"></div>
                    </div>
                </div>
                
                <!-- Как использовать подарочную карту -->
                <div class="usage-guide">
                    <h3 class="usage-title">Как использовать</h3>
                    <ul class="usage-list">
                        <li class="usage-item">
                            <span class="usage-icon">1</span>
                            <span class="usage-text">Предъявите карту при оплате в наших магазинах или введите номер карты при онлайн-заказе</span>
                        </li>
                        <li class="usage-item">
                            <span class="usage-icon">2</span>
                            <span class="usage-text">Сумма покупки будет списана с баланса подарочной карты</span>
                        </li>
                        <li class="usage-item">
                            <span class="usage-icon">3</span>
                            <span class="usage-text">Остаток средств можно использовать для следующих покупок до истечения срока действия</span>
                        </li>
                    </ul>
                </div>
                
                <!-- Информация о магазине/бренде -->
                <div class="brand-info">
                    <img src="<?= $company_logo ?>" alt="<?= $company_name ?>" class="brand-logo">
                    <div class="brand-details">
                        <div class="brand-name"><?= $company_name ?></div>
                        <div class="brand-description">Подарочная карта действительна во всех магазинах сети</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Генерация QR-кода
            generateQR('<?= $certificate_number ?>');
            
            // Запуск таймера обратного отсчета
            startCountdown();
            
            // Добавление эффектов при наведении
            addHoverEffects();
        });
        
        // Функция для генерации QR-кода
        function generateQR(data) {
            const qrElement = document.getElementById('qr-code');
            
            // В реальном проекте используется библиотека для генерации QR
            // Для демонстрации делаем просто имитацию
            qrElement.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 90 90">
                    <rect x="10" y="10" width="70" height="70" fill="#ffffff" />
                    <rect x="20" y="20" width="50" height="50" fill="#8B5CF6" />
                    <rect x="30" y="30" width="30" height="30" fill="#ffffff" />
                    <rect x="40" y="40" width="10" height="10" fill="#8B5CF6" />
                    <text x="45" y="85" text-anchor="middle" font-size="8" fill="#333">${data}</text>
                </svg>
            `;
        }
        
        // Функция для запуска таймера обратного отсчета
        function startCountdown() {
            // Получаем даты из подарочной карты
            const validFromElement = document.querySelector('.validity-item:first-child .validity-date');
            const validUntilElement = document.querySelector('.validity-item:last-child .validity-date');
            
            if (!validFromElement || !validUntilElement) return;
            
            const validFromText = validFromElement.innerText;
            const validUntilText = validUntilElement.innerText;
            
            // Парсим даты (формат дд.мм.гггг)
            const parseDate = function(dateText) {
                const parts = dateText.split('.');
                if (parts.length !== 3) return new Date();
                
                const day = parseInt(parts[0], 10);
                const month = parseInt(parts[1], 10) - 1; // Месяцы в JS начинаются с 0
                const year = parseInt(parts[2], 10);
                
                return new Date(year, month, day, 23, 59, 59);
            };
            
            const validFromDate = parseDate(validFromText);
            const validUntilDate = parseDate(validUntilText);
            
            // Функция обновления таймера
            function updateCountdown() {
                const currentDate = new Date();
                const timeDiff = validUntilDate - currentDate;
                
                // Общая длительность подарочной карты
                const totalDuration = validUntilDate - validFromDate;
                
                // Оставшийся процент времени
                const progressPercent = Math.max(0, Math.min(100, (timeDiff / totalDuration) * 100));
                document.getElementById('countdown-progress').style.width = `${progressPercent}%`;
                
                // Если время истекло
                if (timeDiff <= 0) {
                    document.getElementById('countdown-timer').innerText = "Срок действия истёк";
                    document.getElementById('countdown-timer').style.background = 
                        "linear-gradient(135deg, #ef4444 0%, #b91c1c 100%)";
                    document.getElementById('countdown-timer').style.color = "#ffffff";
                    return;
                }
                
                // Расчет оставшегося времени
                const days = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeDiff % (1000 * 60)) / 1000);
                
                // Правильное склонение слова "день"
                let daysLabel = "дней";
                if (days % 10 === 1 && days % 100 !== 11) {
                    daysLabel = "день";
                } else if ([2, 3, 4].includes(days % 10) && ![12, 13, 14].includes(days % 100)) {
                    daysLabel = "дня";
                }
                
                // Форматирование вывода
                let countdownText = "";
                
                if (days > 0) {
                    countdownText = `${days} ${daysLabel} ${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                } else {
                    countdownText = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                }
                
                document.getElementById('countdown-timer').innerText = countdownText;
                
                // Изменение цвета при малом оставшемся сроке
                if (days < 3) {
                    document.getElementById('countdown-timer').style.background = 
                        "linear-gradient(135deg, #f97316 0%, #ea580c 100%)";
                    document.getElementById('countdown-timer').style.color = "#ffffff";
                }
            }
            
            // Запускаем обновление таймера
            updateCountdown();
            setInterval(updateCountdown, 1000);
        }
        
        // Добавление эффектов при наведении и взаимодействии
        function addHoverEffects() {
            if (window.innerWidth <= 768) return; // Отключаем на мобильных
            
            // Добавляем мягкие переходы для элементов списка использования
            const usageItems = document.querySelectorAll('.usage-item');
            usageItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    usageItems.forEach(otherItem => {
                        if (otherItem !== item) {
                            otherItem.style.opacity = '0.7';
                        }
                    });
                });
                
                item.addEventListener('mouseleave', function() {
                    usageItems.forEach(otherItem => {
                        otherItem.style.opacity = '1';
                    });
                });
            });
        }
        
        // Обработчик для динамической загрузки логотипа через postMessage
        window.addEventListener('message', function(event) {
            if (event.data && event.data.type === 'update_logo') {
                const logoUrl = event.data.logo_url;
                const logoImages = document.querySelectorAll('.company-logo, .brand-logo');
                
                logoImages.forEach(img => {
                    if (logoUrl === 'none') {
                        img.style.display = 'none';
                    } else {
                        img.src = logoUrl;
                        img.style.display = 'block';
                    }
                });
                
                // Отправляем подтверждение обратно
                if (event.source) {
                    event.source.postMessage({
                        type: 'logo_updated',
                        success: true
                    }, '*');
                }
            }
        });
        
        // Исправления для мобильных устройств
        if (window.innerWidth <= 768) {
            document.querySelectorAll('.certificate-card, .details-card').forEach(panel => {
                panel.style.transform = 'none';
                panel.style.animation = 'none';
                panel.style.transition = 'all 0.3s ease';
                panel.style.opacity = '1';
            });
            
            document.querySelector('.certificate-details').style.opacity = '1';
        }
    </script>
</body>
</html>
