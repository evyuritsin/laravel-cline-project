<?php

namespace App\Services;

use App\Models\Inspection;
use App\Models\Report;
use App\Services\YandexStorageService;
use Illuminate\Support\Facades\Storage;

class PdfGeneratorService
{
    public function __construct(
        private YandexStorageService $storageService
    ) {}

    public function generateReport(Inspection $inspection): Report
    {
        $html = $this->generateHtml($inspection);
        
        $pdfPath = "reports/{$inspection->id}/" . uniqid() . '.pdf';
        
        // For MVP we store HTML, in production use WeasyPrint or DomPDF
        Storage::disk('yandex')->put($pdfPath . '.html', $html);
        
        $report = $inspection->report()->create([
            'pdf_path' => $pdfPath,
            'pdf_url' => $this->storageService->getUrl($pdfPath),
            'generated_at' => now(),
            'status' => 'pending',
        ]);

        $inspection->update(['status' => 'completed']);

        return $report;
    }

    private function generateHtml(Inspection $inspection): string
    {
        $rooms = $inspection->rooms()->with('photos')->get();
        
        $roomsHtml = '';
        foreach ($rooms as $room) {
            $photosHtml = '';
            foreach ($room->photos as $photo) {
                $photosHtml .= "<img src='{$photo->storage_path}' style='max-width:200px;margin:5px;'>";
            }
            
            $roomsHtml .= "
                <div style='margin-bottom:20px;'>
                    <h3>{$room->name}</h3>
                    <p>{$room->notes}</p>
                    <div style='display:flex;flex-wrap:wrap;'>{$photosHtml}</div>
                </div>
            ";
        }

        return "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='UTF-8'>
                <title>Акт приёма-передачи #{$inspection->id}</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 40px; }
                    h1 { color: #333; }
                    .header { border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 20px; }
                    .info { background: #f5f5f5; padding: 15px; margin-bottom: 20px; }
                    .room { border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; }
                </style>
            </head>
            <body>
                <div class='header'>
                    <h1>Акт приёма-передачи</h1>
                    <p><strong>Тип:</strong> {$inspection->getTypeLabel()}</p>
                    <p><strong>Дата:</strong> {$inspection->inspection_date->format('d.m.Y')}</p>
                </div>
                
                <div class='info'>
                    <p><strong>Адрес:</strong> {$inspection->address}</p>
                    <p><strong>Координаты:</strong> {$inspection->latitude}, {$inspection->longitude}</p>
                    <p><strong>Статус:</strong> {$inspection->getStatusLabel()}</p>
                </div>
                
                <h2>Комнаты</h2>
                {$roomsHtml}
                
                <div style='margin-top:40px;padding:20px;border-top:1px solid #333;'>
                    <p>Дата создания: " . now()->format('d.m.Y H:i') . "</p>
                    <p>ID инспекции: {$inspection->id}</p>
                </div>
            </body>
            </html>
        ";
    }
}