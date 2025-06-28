@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white rounded shadow">
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
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
@endsection 