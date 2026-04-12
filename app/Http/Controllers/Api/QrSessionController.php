<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SesiAbsen;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class QrSessionController extends Controller
{
    public function generate(Request $request)
    {

        $request->validate([
            'jadwal_id' => 'required|exists:jadwal,id',
            'tipe' => 'required|in:masuk,pulang,jam_masuk,jam_pulang'
        ]);

        $jadwalId = $request->query('jadwal_id');
        $tipe = $request->query('tipe');

        /* Mapping tipe */
        if ($tipe === 'masuk') {
            $tipe = 'jam_masuk';
        }

        if ($tipe === 'pulang') {
            $tipe = 'jam_pulang';
        }

        $token = (string) Str::uuid();

        $expiredAt = Carbon::now()->addMinutes(5);

        $session = SesiAbsen::create([
            'jadwal_id' => $jadwalId,
            'tipe' => $tipe,
            'tanggal' => Carbon::today(),
            'token_qr' => $token,
            'expired_at' => $expiredAt,
            'dibuka_oleh' => auth()->id() ?? 1,
            'dibuka_pada' => Carbon::now()
        ]);

        /* Isi QR cukup token saja */
        $qrCode = QrCode::create($token)
            ->setSize(300)
            ->setMargin(10);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return response($result->getString(), 200, [
            'Content-Type' => 'image/png'
        ]);
    }
}