<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Mail;

try {
    Mail::raw('Test email from Laravel - Email system is working!', function($message) {
        $message->to('vetescoton@usep.edu.ph')
                ->subject('Email Test - ' . now());
    });
    
    echo "✅ Test email sent successfully!\n";
    echo "Check your email inbox for the test message.\n";
    
} catch (Exception $e) {
    echo "❌ Email failed to send:\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "\nThis usually means:\n";
    echo "1. SMTP settings are incorrect in .env file\n";
    echo "2. Email credentials are wrong\n";
    echo "3. Mail driver is still set to 'log'\n";
}
