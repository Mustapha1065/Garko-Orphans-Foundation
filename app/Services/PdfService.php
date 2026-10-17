<?php
namespace GOF\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService {
    public function generateFromHtml(string $html, string $outputPath): bool {
        try {
            $options = new Options();
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('defaultPaperSize', 'A4');
            
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->render();
            
            file_put_contents($outputPath, $dompdf->output());
            return true;
        } catch (\Exception $e) {
            error_log("PDF Generation failed: " . $e->getMessage());
            return false;
        }
    }

    public function generateIdCard(string $html, string $outputPath): bool {
        try {
            $options = new Options();
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            // CR80 standard ID card size
            $dompdf->setPaper(array(0, 0, 153.07, 242.64), 'portrait');
            $dompdf->render();
            
            file_put_contents($outputPath, $dompdf->output());
            return true;
        } catch (\Exception $e) {
            error_log("ID Card PDF Generation failed: " . $e->getMessage());
            return false;
        }
    }
}
