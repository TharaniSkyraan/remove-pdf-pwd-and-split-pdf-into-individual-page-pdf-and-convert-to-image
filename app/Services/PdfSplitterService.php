<?php

namespace App\Services;

use TCPDI;
use Exception;
use Imagick;

class PdfSplitterService
{
    public function splitPdf($filePath)
    {
        $pdf = new TCPDI();

        try {
            $pageCounts = $pdf->setSourceFile($filePath);
            $pdf->importPage(1);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }

        $outputFiles = $images = [];

        for ($pageNo = 1; $pageNo <= $pageCounts; $pageNo++) {
            $pdf = new TCPDI();
            $pdf->AddPage();
            $pdf->setSourceFile($filePath); // Ensure setSourceFile() is called here
            $pageId = $pdf->importPage($pageNo);
            $pdf->useTemplate($pageId, 5, 5, 200);
            $outputFilePath = storage_path("app/public/pdf/split_page_{$pageNo}.pdf");
            $pdf->Output($outputFilePath, 'F');


            $imagick = new Imagick($outputFilePath. '[0]');
            $imagick->setImageFormat('jpg');
            $imagick->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
            $imagick->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
            $imagePath = storage_path('app/public/image/page_' . $pageNo . '.jpg');
            $imagick->writeImage($imagePath);



            // Add the image path to the array
            $images[] = asset('storage').'/image/page_' . $pageNo . '.jpg';

            $outputFiles[] = asset('storage')."/pdf/split_page_{$pageNo}.pdf";
            // Clear Imagick object
            $imagick->clear();
            $imagick->destroy();
       
        }
       
        return response()->json([
            'images' => $images,
            'outputFiles' => $outputFiles
        ]);
        
    }
}
