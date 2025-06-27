<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Request as UserRequest;
use Barryvdh\DomPDF\Facade\Pdf;

class PublicationsController extends Controller
{
    public function create()
    {
        return view('publications.request');
    }

    public function store(Request $request)
    {
        $request->validate([
            'incentive_field' => 'required',
            'recommendation_field' => 'required',
            'terminal_field' => 'required',
        ]);

        // Create the request with form data
        $userRequest = UserRequest::create([
            'user_id' => Auth::id(),
            'request_code' => 'REQ-' . now()->format('Ymd-His'),
            'type' => 'Publication',
            'status' => 'pending',
            'requested_at' => now(),
            'form_data' => [
                'incentive_field' => $request->incentive_field,
                'recommendation_field' => $request->recommendation_field,
                'terminal_field' => $request->terminal_field,
            ],
        ]);

        // Generate PDF
        try {
            $this->generatePDFForRequest($userRequest);
        } catch (\Exception $e) {
            // Log error but don't fail the request
            Log::error('PDF generation failed: ' . $e->getMessage());
        }

        return redirect()->route('dashboard')->with('success', 'Publication request submitted successfully!');
    }

    public function adminUpdate(Request $httpRequest, \App\Models\Request $request)
    {
        $httpRequest->validate([
            'status' => 'required|in:pending,endorsed,rejected',
        ]);
        $request->status = $httpRequest->input('status');
        $request->save();
        return back()->with('success', 'Request status updated successfully.');
    }

    public function viewPDF(\App\Models\Request $request)
    {
        $pdfContent = $request->getPDFContent();
        
        if (!$pdfContent) {
            // Generate PDF if it doesn't exist
            try {
                $pdfContent = $this->generatePDFForRequest($request);
            } catch (\Exception $e) {
                return response('PDF generation failed', 500);
            }
        }

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $request->request_code . '.pdf"');
    }

    private function generatePDFForRequest($request)
    {
        $pdf = Pdf::loadView('pdfs.request-form', [
            'request' => $request,
            'user' => $request->user,
            'formData' => $request->form_data,
        ]);

        $pdfContent = $pdf->output();
        $pdfPath = 'pdfs/requests/' . $request->request_code . '.pdf';

        // Store PDF content in database
        $request->update([
            'pdf_content' => base64_encode($pdfContent),
            'pdf_path' => $pdfPath,
        ]);

        return $pdfContent;
    }
} 