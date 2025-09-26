<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class LogRepository extends Model
{
    public static function saveLogFile(string $mode, string $text) {
        //save prompt in the log file
        $date = Carbon::now()->format('Y-m-d');
        $time = Carbon::now()->format('H:i:s');
        Storage::disk('local')->append($mode . "_" . $date . ".log", "[".$time."]"."\n".$text);
    }

}

