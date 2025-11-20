<?php

use yii\helpers\Html;

/** @var string $token */
/** @var string $userName */
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 40px 30px;
        }
        .token-box {
            background: #f8f9fa;
            border: 2px dashed #4CAF50;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .token {
            font-size: 36px;
            font-weight: bold;
            color: #4CAF50;
            letter-spacing: 5px;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Recuperación de Contraseña</h1>
        </div>
        <div class="content">
            <p>Hola <strong><?= Html::encode($userName) ?></strong>,</p>
            
            <p>Recibimos una solicitud para restablecer tu contraseña. Usa el siguiente código para continuar:</p>
            
            <div class="token-box">
                <p style="margin: 0; color: #6c757d; font-size: 14px;">Tu código de verificación:</p>
                <div class="token"><?= Html::encode($token) ?></div>
            </div>
            
            <p>Este código es válido y debe ser utilizado lo antes posible.</p>
            
            <p><strong>Si no solicitaste este cambio, ignora este correo.</strong></p>
            
            <p>Saludos,<br>El equipo de Wintick</p>
        </div>
        <div class="footer">
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
        </div>
    </div>
</body>
</html>