@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white rounded shadow">
    <!-- Floating Notifications -->
    @if(session('success'))
    <div id="success-notification" class="fixed top-20 right-4 z-[60] bg-green-600 text-white px-4 py-2 rounded shadow-lg backdrop-blur border border-green-500/20 transform transition-all duration-300 opacity-100 translate-x-0">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif
    
    @if(session('error'))
    <div id="error-notification" class="fixed top-20 right-4 z-[60] bg-red-600 text-white px-4 py-2 rounded shadow-lg backdrop-blur border border-red-500/20 transform transition-all duration-300 opacity-100 translate-x-0">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    </div>
    @endif
    <h2 class="text-xl font-bold text-center mb-4">Review Your Submission</h2>
    <div class="mb-6">
        <h3 class="font-semibold mb-2">Generated Documents</h3>
        <ul class="list-disc pl-6 text-sm">
            <li><a href="{{ asset(str_replace(storage_path('app/'), 'storage/', $incentiveDocx)) }}" class="text-blue-600 underline" download>Download Incentive Application DOCX</a></li>
            <li><a href="{{ asset(str_replace(storage_path('app/'), 'storage/', $recDocx)) }}" class="text-blue-600 underline" download>Download Recommendation Letter DOCX</a></li>
            <li><a href="{{ asset(str_replace(storage_path('app/'), 'storage/', $termDocx)) }}" class="text-blue-600 underline" download>Download Terminal Report DOCX</a></li>
        </ul>
    </div>
    <div class="mb-6">
        <h3 class="font-semibold mb-2">Form Data</h3>
        <ul class="list-disc pl-6 text-sm">
            @foreach($data as $key => $value)
                @if(!str_ends_with($key, '_pdf') && !is_array($value))
                    <li><span class="font-semibold">{{ ucwords(str_replace('_', ' ', $key)) }}:</span> {{ $value }}</li>
                @endif
            @endforeach
            @if(isset($data['indexed_in']) && is_array($data['indexed_in']))
                <li><span class="font-semibold">Indexed In:</span> {{ implode(', ', $data['indexed_in']) }}</li>
            @endif
        </ul>
    </div>
    <div class="mb-6">
        <h3 class="font-semibold mb-2">Attachments</h3>
        <ul class="list-disc pl-6 text-sm">
            @foreach($attachments as $key => $path)
                <li>
                    <span class="font-semibold">{{ ucwords(str_replace('_', ' ', str_replace('_pdf', '', $key))) }}:</span>
                    <a href="{{ asset('storage/' . str_replace('temp/requests/', '', $path)) }}" class="text-blue-600 underline" target="_blank">View PDF</a>
                </li>
            @endforeach
        </ul>
    </div>
    <form action="{{ route('publications.incentive.submit') }}" method="POST">
        @csrf
        <div class="flex justify-between mt-8">
            <a href="{{ url()->previous() }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Back to Edit</a>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded">Submit</button>
        </div>
    </form>
</div>

<script>
    // Auto-hide notifications after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const successNotification = document.getElementById('success-notification');
        const errorNotification = document.getElementById('error-notification');
        
        if (successNotification) {
            setTimeout(() => {
                successNotification.classList.add('opacity-0', 'translate-x-full');
                setTimeout(() => {
                    if (document.body.contains(successNotification)) {
                        document.body.removeChild(successNotification);
                    }
                }, 300);
            }, 5000);
        }
        
        if (errorNotification) {
            setTimeout(() => {
                errorNotification.classList.add('opacity-0', 'translate-x-full');
                setTimeout(() => {
                    if (document.body.contains(errorNotification)) {
                        document.body.removeChild(errorNotification);
                    }
                }, 300);
            }, 5000);
        }
    });
</script>
@endsection 