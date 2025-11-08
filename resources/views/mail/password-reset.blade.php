<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Código de Seguridad</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 20px auto; background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">

        <div style="text-align: center; padding-bottom: 20px; border-bottom: 2px solid #C8102E;">
             <h2 style="color: #C8102E; margin: 0;">Restablecimiento de Contraseña</h2>
        </div>

        <p style="margin-top: 20px;">Hola {{ $userName }},</p>

        <p>Has solicitado restablecer la contraseña de tu cuenta. Por favor, usa el siguiente código de seguridad en la aplicación para continuar:</p>

        <div style="text-align: center; margin: 30px 0;">
            <p style="font-size: 28px; font-weight: bold; background-color: #f7f7f7; padding: 15px; display: inline-block; border: 1px solid #C8102E; border-radius: 4px; letter-spacing: 5px;">
                {{ $code }}
            </p>
        </div>

        <p style="font-size: 0.9em; color: #888;">
            Este código de seguridad es válido por 60 minutos y debe ser ingresado directamente en el formulario de restablecimiento de contraseña.
        </p>

        <p>Si no solicitaste este cambio, puedes ignorar este correo. Tu contraseña no cambiará hasta que ingreses el código.</p>

        <p>Saludos cordiales,<br>El equipo de Habilitación Profesional UCSC</p>
    </div>
</body>
</html>
