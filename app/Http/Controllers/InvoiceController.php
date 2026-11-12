<?php

namespace App\Http\Controllers;

use App\Models\Faktur;
use App\Models\User;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function show(int $id)
    {
        $faktur = $this->findFaktur($id);

        return view('invoices.show', [
            'faktur' => $faktur,
            'backUrl' => $this->backUrl($faktur),
            'downloadUrl' => $this->downloadUrl($faktur),
        ]);
    }

    public function download(int $id)
    {
        $faktur = $this->findFaktur($id);
        $nomorInvoice = $faktur->nomor_faktur ?: 'INV-'.$faktur->id;

        $pdf = new Dompdf([
            'defaultFont' => 'DejaVu Sans',
            'isRemoteEnabled' => false,
        ]);

        $pdf->loadHtml(view('invoices.pdf', compact('faktur', 'nomorInvoice'))->render());
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        $filename = 'Invoice-'.preg_replace('/[^A-Za-z0-9_-]/', '-', $nomorInvoice).'.pdf';

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function findFaktur(int $id): Faktur
    {
        /** @var User $user */
        $user = Auth::user();

        return Faktur::with(['toko', 'user'])
            ->visibleTo($user)
            ->findOrFail($id);
    }

    private function backUrl(Faktur $faktur): string
    {
        return Auth::user()->role === 'admin'
            ? route('admin.pembukuan.show', $faktur->id)
            : route('sales.riwayat.show', $faktur->id);
    }

    private function downloadUrl(Faktur $faktur): string
    {
        return Auth::user()->role === 'admin'
            ? route('admin.pembukuan.invoice.download', $faktur->id)
            : route('sales.riwayat.invoice.download', $faktur->id);
    }
}
