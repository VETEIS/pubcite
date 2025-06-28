@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto p-8 bg-white rounded shadow mt-12 text-center">
    <h2 class="text-2xl font-bold mb-4 text-green-700">Submission Complete!</h2>
    <p class="mb-6">{{ $message ?? 'Your submission has been received.' }}</p>
    <a href="{{ route('dashboard') }}" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">Return to Dashboard</a>
    <a href="{{ route('publications.incentive.review') }}" class="ml-4 bg-gray-300 text-gray-800 px-6 py-2 rounded hover:bg-gray-400">Start New Request</a>
</div>
@endsection 