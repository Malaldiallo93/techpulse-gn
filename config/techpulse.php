<?php

return [
    // Incrémenter pour invalider le cache du navigateur et du service worker.
    'asset_version' => env('TECHPULSE_ASSET_VERSION', '2'),
    'whatsapp_channel' => env('TECHPULSE_WHATSAPP_URL', 'https://whatsapp.com/channel/'),
    'telegram_channel' => env('TECHPULSE_TELEGRAM_URL', 'https://t.me/'),
    'contact_email' => env('TECHPULSE_CONTACT_EMAIL', 'redaction@techpulse.gn'),
    'volunteers' => (int) env('TECHPULSE_VOLUNTEERS', 24),
];
