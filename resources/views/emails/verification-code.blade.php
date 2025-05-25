<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Код подтверждения</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #f9f9f9;
            border-radius: 5px;
            padding: 20px;
            border: 1px solid #e0e0e0;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            color: #0d6efd;
            margin: 20px 0;
            letter-spacing: 5px;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Код подтверждения номера телефона</h2>
        
        <p>Здравствуйте!</p>
        
        <p>Вы запросили изменение номера телефона в вашем профиле.</p>
        
        <p>Ваш код подтверждения:</p>
        
        <div class="code">{{ $verificationCode }}</div>
        
        <p>Введите этот код в окно подтверждения для завершения процедуры изменения номера телефона.</p>
        
        <p>Если вы не запрашивали изменение номера телефона, проигнорируйте это сообщение.</p>
        
        <p>С уважением,<br>Команда {{ config('app.name') }}</p>
    </div>
    
    <div class="footer">
        Это автоматическое сообщение, пожалуйста, не отвечайте на него.
    </div>
</body>
</html>
