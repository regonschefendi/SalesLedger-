<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Bersihkan foto yatim di Supabase (mis. data faktur dihapus langsung dari
// Table Editor/SQL sehingga event model Laravel tidak berjalan).
Schedule::command('faktur:purge-orphans')->daily()->withoutOverlapping();
