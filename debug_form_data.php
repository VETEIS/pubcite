<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$requestId = 10; // Change this to the ID you want to inspect
$request = \App\Models\Request::find($requestId);
if (!$request) {
    echo "Request not found.\n";
    exit(1);
}
echo "Request ID: {$request->id}\n";
echo "Request Code: {$request->request_code}\n";
echo "Type: {$request->type}\n";
echo "Form Data (raw):\n";
$formData = json_decode($request->form_data, true);
if ($formData) {
    foreach ($formData as $key => $value) {
        echo "  {$key}: {$value}\n";
    }
} else {
    echo "  No form data found\n";
} 