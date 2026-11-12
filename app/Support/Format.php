<?php

namespace App\Support;

use Carbon\Carbon;

final class Format
{
    public static function rupiah(int|float|string|null $value): string
    {
        return 'Rp'.number_format((float) ($value ?? 0), 0, ',', '.');
    }

    public static function date(string|Carbon|null $value, string $format = 'd M Y'): string
    {
        if (! $value) {
            return '-';
        }

        return Carbon::parse($value)->translatedFormat($format);
    }

    public static function dateTime(string|Carbon|null $value): string
    {
        return self::date($value, 'd M Y, H:i');
    }
}
