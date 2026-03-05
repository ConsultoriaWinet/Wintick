<?php

return [
    'adminEmail'  => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName'  => 'Example.com mailer',

    // ── Anti-brute force (PCI DSS 8.3.4 / ISO 27001 A.9.4.2) ─────────────
    // Intentos fallidos antes de bloquear la cuenta
    'security.maxLoginAttempts'  => 5,
    // Minutos de bloqueo de cuenta (PCI DSS exige mínimo 30)
    'security.lockoutMinutes'    => 30,
    // Intentos fallidos desde una misma IP antes de bloquearla
    'security.ipMaxAttempts'     => 20,
    // Ventana de tiempo (minutos) para contar intentos por IP
    'security.ipWindowMinutes'   => 15,
];
