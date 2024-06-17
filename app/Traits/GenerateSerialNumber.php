<?php

namespace App\Traits;

use Carbon\Carbon;

trait GenerateSerialNumber
{
    protected static function bootGenerateSerialNumber()
    {
        static::creating(function (self $model) {
            $prefix = $model->serialNumber['prefix'];
            $serialColumn = $model->serialNumber['serial_column'];

            $model->{$serialColumn} = $model->generateSerial($prefix, $serialColumn);
        });
    }

    protected function generateSerial(string $prefix, string $serialColumn): string
    {
        // Create a slug from the source using Laravel's Str::slug method
        $today = Carbon::now()->format('dmy');

        // Ambil nomor tiket terakhir untuk hari ini
        $lastSerialNumber = $this->query()->where($serialColumn, 'like', $prefix . '-' . $today . '-%')->max('ticket_number');

        // Jika tidak ada nomor tiket sebelumnya untuk hari ini, set nomor awal ke 00001
        $lastNumber = !empty($lastSerialNumber) ? intval(substr($lastSerialNumber, -5)) : 0;

        // Increment nomor
        $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);

        // Gabungkan tanggal dan nomor untuk membentuk nomor tiket
        $ticketNumber = $prefix . '-' . $today . '-' . $newNumber;

        return $ticketNumber;
    }
}
