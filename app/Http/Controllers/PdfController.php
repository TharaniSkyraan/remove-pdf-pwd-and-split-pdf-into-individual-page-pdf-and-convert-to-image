<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PdfSplitterService;

class PdfController extends Controller
{
    protected $pdfSplitterService;

    public function __construct(PdfSplitterService $pdfSplitterService)
    {
        $this->pdfSplitterService = $pdfSplitterService;
    }

    public function split(Request $request)
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf',
        ]);

        $file = $request->file('pdf');
        $password = $request->password;

        $filePath = $file->storeAs('public/temp_pdf', $file->getClientOriginalName());
        
        // Pdf remove password 
        
        $temppdfPath = storage_path('app/public/temp_pdf/' . $file->getClientOriginalName());
        $outputPath = storage_path('app/public/' . $file->getClientOriginalName());

        $command = "/usr/bin/qpdf --password='{$password}' --decrypt '{$temppdfPath}' '{$outputPath}'";
        exec($command, $output, $return_var);

        // Check for errors
        if ($return_var !== 0) {
             // Output debugging information
            return response()->json([
                'message' => 'Invalid pdf password',
                'files' => [],
            ]);
        } else {
            unlink($temppdfPath);
            $outputFiles = $this->pdfSplitterService->splitPdf($outputPath);
    
            return response()->json([
                'message' => 'PDF split successfully',
                'files' => $outputFiles,
            ]);
        }
    }
}
