<?php

namespace App\Services;

use App\Models\PointLedger;
use Illuminate\Support\Facades\DB;

class PointService
{
    /**
     * Ambil saldo poin terakhir berdasarkan created_at DESC
     *
     * @param int $userId
     * @return int
     */
    public function getLastBalance($userId): int
    {
        // Ambil data terakhir berdasarkan created_at DESC
        // Kita juga menambahkan orderBy 'id' DESC sebagai form of tie-breaker
        $lastLedger = PointLedger::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        // Jika tidak ada -> return 0
        if (!$lastLedger) {
            return 0;
        }

        // Return current_balance
        return $lastLedger->current_balance;
    }

    /**
     * Helper Function untuk Tambah Ledger/Mutasi Poin
     *
     * @param int $userId
     * @param string $type enum('EARN', 'SPEND', 'PENALTY')
     * @param int $amount
     * @param string $description
     * @return PointLedger
     */
    public function createLedger($userId, $type, $amount, $description)
    {
        // Gunakan database transaction agar aman saat concurrent request
        return DB::transaction(function () use ($userId, $type, $amount, $description) {
            // 1. Ambil saldo terakhir
            $lastBalance = $this->getLastBalance($userId);

            // 2. Hitung saldo baru (amount bisa bernilai negatif sudah dari parameternya)
            $newBalance = $lastBalance + $amount;

            // 3. Insert ke point_ledgers
            return PointLedger::create([
                'user_id'          => $userId,
                'transaction_type' => $type,
                'amount'           => $amount,
                'current_balance'  => $newBalance,
                'description'      => $description,
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | CONTOH PENGGUNAAN POINT SERVICE
    |--------------------------------------------------------------------------
    | 
    | Berikut adalah contoh integrasi sesuai permintaan yang bisa di-copy &
    | digunakan saat Anda memanggil PointService di dalam Controller / sistem absen.
    |
    */
    public function recordAttendanceExample($userId, $status, $minutesLate = 0)
    {
        if ($status === 'TEPAT_WAKTU') {
            // Tepat waktu -> createLedger(user_id, 'EARN', +5, 'Tepat waktu')
            $this->createLedger($userId, 'EARN', 5, 'Tepat waktu kehadiran');
        } 
        elseif ($status === 'TERLAMBAT') {
            // Telat -> createLedger(user_id, 'PENALTY', -3, 'Terlambat 20 menit')
            $penaltyAmount = -3;
            $this->createLedger($userId, 'PENALTY', $penaltyAmount, "Terlambat {$minutesLate} menit");
        }
    }
}
