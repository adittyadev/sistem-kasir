<?php

if (! function_exists('rupiah')) {
    function rupiah($angka): string
    {
        return 'Rp ' . number_format((float) $angka, 0, ',', '.');
    }
}

if (! function_exists('tgl')) {
    function tgl(?string $waktu, string $format = 'd/m/Y H:i'): string
    {
        return $waktu ? date($format, strtotime($waktu)) : '-';
    }
}

if (! function_exists('badge_status')) {
    function badge_status(string $status): string
    {
        $kelas = ['diproses' => 'warning text-dark', 'selesai' => 'success', 'dibatalkan' => 'secondary'][$status] ?? 'light text-dark';

        return '<span class="badge bg-' . $kelas . '">' . esc(ucfirst($status)) . '</span>';
    }
}

if (! function_exists('label_tipe')) {
    function label_tipe(string $tipe): string
    {
        return ['dine_in' => 'Dine-In', 'takeaway' => 'Takeaway'][$tipe] ?? $tipe;
    }
}

if (! function_exists('label_metode')) {
    function label_metode(string $metode): string
    {
        return ['tunai' => 'Tunai', 'qris' => 'QRIS', 'transfer' => 'Transfer Bank', 'debit' => 'Kartu Debit'][$metode] ?? $metode;
    }
}
