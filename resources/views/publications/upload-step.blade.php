@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white rounded shadow mt-8">
    <div class="mb-6">
        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
            <div class="bg-indigo-600 h-2.5 rounded-full" style="width: 66%"></div>
        </div>
        <div class="flex justify-between text-xs text-gray-600 mb-2">
            <span>Step 1: Details</span>
            <span class="font-bold text-indigo-700">Step 2: Upload</span>
            <span>Step 3: Review & Submit</span>
        </div>
    </div>
    <h2 class="text-xl font-bold mb-4">Step 2: Upload Required Attachments</h2>
    @if(isset($error))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-2">
            <strong>{{ $error }}</strong>
        </div>
    @endif
    @if(isset($success))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded mb-2">
            <strong>{{ $success }}</strong>
        </div>
    @endif
    <form method="POST" action="{{ route('publications.incentive.upload.handle', ['token' => $token]) }}" enctype="multipart/form-data" autocomplete="on">
        @csrf
        <div class="mb-4">
            <label class="block font-semibold mb-1">Article PDF <span class="text-red-600">*</span></label>
            <input type="file" name="article_pdf" accept="application/pdf" required class="border rounded-lg p-2 w-full">
            @if(isset($errors) && $errors->has('article_pdf'))
                <span class="text-red-600 text-xs">{{ $errors->first('article_pdf') }}</span>
            @endif
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Cover PDF <span class="text-red-600">*</span></label>
            <input type="file" name="cover_pdf" accept="application/pdf" required class="border rounded-lg p-2 w-full">
            @if(isset($errors) && $errors->has('cover_pdf'))
                <span class="text-red-600 text-xs">{{ $errors->first('cover_pdf') }}</span>
            @endif
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Acceptance PDF <span class="text-red-600">*</span></label>
            <input type="file" name="acceptance_pdf" accept="application/pdf" required class="border rounded-lg p-2 w-full">
            @if(isset($errors) && $errors->has('acceptance_pdf'))
                <span class="text-red-600 text-xs">{{ $errors->first('acceptance_pdf') }}</span>
            @endif
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Peer Review PDF <span class="text-red-600">*</span></label>
            <input type="file" name="peer_review_pdf" accept="application/pdf" required class="border rounded-lg p-2 w-full">
            @if(isset($errors) && $errors->has('peer_review_pdf'))
                <span class="text-red-600 text-xs">{{ $errors->first('peer_review_pdf') }}</span>
            @endif
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Terminal Report PDF <span class="text-red-600">*</span></label>
            <input type="file" name="terminal_report_pdf" accept="application/pdf" required class="border rounded-lg p-2 w-full">
            @if(isset($errors) && $errors->has('terminal_report_pdf'))
                <span class="text-red-600 text-xs">{{ $errors->first('terminal_report_pdf') }}</span>
            @endif
        </div>
        <div class="flex justify-end mt-6">
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">Upload & Continue</button>
        </div>
    </form>
</div>
@endsection 