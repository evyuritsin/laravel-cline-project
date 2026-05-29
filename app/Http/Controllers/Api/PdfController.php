<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inspection;
use App\Models\Report;
use App\Services\PdfGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function __construct(
        private PdfGeneratorService $pdfService
    ) {}

    public function generate(Request $request, int $inspectionId): JsonResponse
    {
        $inspection = $request->user()
            ->inspections()
            ->with('rooms.photos')
            ->findOrFail($inspectionId);

        $report = $this->pdfService->generateReport($inspection);

        return response()->json([
            'report' => $report,
            'pdf_url' => $report->getFullUrl(),
        ]);
    }

    public function send(Request $request, int $inspectionId): JsonResponse
    {
        $request->validate([
            'email' => 'nullable|email',
            'telegram_id' => 'nullable|numeric',
        ]);

        $inspection = $request->user()
            ->inspections()
            ->with('report')
            ->findOrFail($inspectionId);

        if (!$inspection->report) {
            return response()->json([
                'message' => 'Generate PDF first',
            ], 400);
        }

        $sentTo = [];
        if ($request->email) {
            $sentTo[] = ['type' => 'email', 'value' => $request->email];
        }
        if ($request->telegram_id) {
            $sentTo[] = ['type' => 'telegram', 'value' => $request->telegram_id];
        }

        $inspection->report->update([
            'sent_to' => $sentTo,
            'status' => 'sent',
        ]);

        return response()->json([
            'message' => 'Report sent successfully',
            'report' => $inspection->report,
        ]);
    }
}