@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white rounded shadow mt-8">
    <div class="mb-6">
        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
            <div class="bg-indigo-600 h-2.5 rounded-full" style="width: 100%"></div>
        </div>
        <div class="flex justify-between text-xs text-gray-600 mb-2">
            <span>Step 1: Details</span>
            <span>Step 2: Upload</span>
            <span class="font-bold text-indigo-700">Step 3: Review & Submit</span>
        </div>
    </div>
    <h2 class="text-xl font-bold mb-4">Step 3: Review & Submit</h2>
    <div class="mb-4">
        <h3 class="font-semibold mb-2">Summary of Entered Data</h3>
        <div class="bg-gray-50 p-3 rounded text-sm max-h-48 overflow-y-auto">
            <ul>
                @foreach($data as $key => $value)
                    <li><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ is_array($value) ? implode(', ', $value) : $value }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="mb-4">
        <h3 class="font-semibold mb-2">Uploaded Files</h3>
        <ul class="list-disc pl-6">
            @foreach($attachments as $key => $file)
                <li>{{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $file['original_name'] }}</li>
            @endforeach
        </ul>
    </div>
    <div class="mb-4">
        <h3 class="font-semibold mb-2">Generated DOCX Files</h3>
        <ul class="list-disc pl-6">
            @if($docxPaths['incentive'])
                <li><a href="{{ route('publications.incentive.download', ['token' => $token, 'type' => 'incentive']) }}" class="text-indigo-600 underline" download>Incentive Application DOCX</a></li>
            @else
                <li class="text-gray-500">Incentive Application DOCX - Not available</li>
            @endif
            @if($docxPaths['recommendation'])
                <li><a href="{{ route('publications.incentive.download', ['token' => $token, 'type' => 'recommendation']) }}" class="text-indigo-600 underline" download>Recommendation Letter DOCX</a></li>
            @else
                <li class="text-gray-500">Recommendation Letter DOCX - Not available</li>
            @endif
            @if($docxPaths['terminal'])
                <li><a href="{{ route('publications.incentive.download', ['token' => $token, 'type' => 'terminal']) }}" class="text-indigo-600 underline" download>Terminal Report DOCX</a></li>
            @else
                <li class="text-gray-500">Terminal Report DOCX - Not available</li>
            @endif
        </ul>
    </div>
    <form method="POST" action="{{ route('publications.incentive.submit', ['token' => $token]) }}">
        @csrf
        <div class="flex justify-between mt-6">
            <a href="{{ route('publications.incentive.upload', ['token' => $token]) }}" class="bg-gray-300 text-gray-800 px-6 py-2 rounded hover:bg-gray-400">Back</a>
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">Submit</button>
        </div>
    </form>
</div>
@endsection 