<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Page</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .info { background: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
    <h1>Debug Information</h1>
    
    <div class="status info">
        <strong>Environment:</strong> {{ app()->environment() }}
    </div>
    
    <div class="status info">
        <strong>App URL:</strong> {{ config('app.url') }}
    </div>
    
    <div class="status info">
        <strong>Debug Mode:</strong> {{ config('app.debug') ? 'Enabled' : 'Disabled' }}
    </div>
    
    <div class="status info">
        <strong>Assets Path:</strong> {{ asset('build/assets/app.js') }}
    </div>
    
    <div class="status info">
        <strong>Build Directory Exists:</strong> {{ file_exists(public_path('build')) ? 'Yes' : 'No' }}
    </div>
    
    @if(file_exists(public_path('build')))
        <div class="status success">
            <strong>Build Files:</strong>
            <ul>
                @foreach(scandir(public_path('build')) as $file)
                    @if($file !== '.' && $file !== '..')
                        <li>{{ $file }}</li>
                    @endif
                @endforeach
            </ul>
        </div>
    @else
        <div class="status error">
            <strong>Build directory not found!</strong>
        </div>
    @endif
    
    <div class="status info">
        <strong>Alpine.js Status:</strong> <span id="alpine-status">Checking...</span>
    </div>
    
    <div class="status info">
        <strong>Livewire Status:</strong> <span id="livewire-status">Checking...</span>
    </div>
    
    <script>
        // Check Alpine.js
        setTimeout(function() {
            if (window.Alpine) {
                document.getElementById('alpine-status').textContent = 'Loaded';
                document.getElementById('alpine-status').parentElement.className = 'status success';
            } else {
                document.getElementById('alpine-status').textContent = 'Not Loaded';
                document.getElementById('alpine-status').parentElement.className = 'status error';
            }
        }, 1000);
        
        // Check Livewire
        setTimeout(function() {
            if (window.Livewire) {
                document.getElementById('livewire-status').textContent = 'Loaded';
                document.getElementById('livewire-status').parentElement.className = 'status success';
            } else {
                document.getElementById('livewire-status').textContent = 'Not Loaded';
                document.getElementById('livewire-status').parentElement.className = 'status error';
            }
        }, 1000);
    </script>
</body>
</html> 