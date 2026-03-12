<?php

namespace App\Services;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    /**
     * Generate the receipt PDF for an order and store it in S3.
     * Returns the S3 path.
     */
    public function generateAndStore(Order $order): string
    {
        $order->loadMissing(['user', 'sale', 'items.product.category']);

        $pdf = Pdf::loadView('website.account.orders.receipt-pdf', compact('order'))
                  ->setPaper('a4', 'portrait');

        $path = 'receipts/' . $order->order_number . '.pdf';

        Storage::disk('s3')->put($path, $pdf->output());

        $order->updateQuietly(['receipt_pdf_path' => $path]);

        return $path;
    }

    /**
     * Get a temporary pre-signed S3 URL for the receipt PDF (valid 24 hours).
     * Generates and stores the PDF first if it doesn't exist yet.
     */
    public function getTemporaryUrl(Order $order): string
    {
        $path = $order->receipt_pdf_path;

        if (!$path || !Storage::disk('s3')->exists($path)) {
            $path = $this->generateAndStore($order);
        }

        return Storage::disk('s3')->temporaryUrl($path, now()->addHours(24));
    }
}
