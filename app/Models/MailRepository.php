<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Jobs\JobSendMail;

class MailRepository extends Model
{
    public static function mailPlanCreated(User $user) {
        
        $toAddress = $user->email;
        $subject = "Nowy plan został wygenerowany";
        $body = "Szanowny/a {[USER_NAME]}<br>Informujemy, że nowy plan lekcji został wygenerowany.";

        $body = str_replace("{[USER_NAME]}", $user->name, $body);

        JobSendMail::dispatch(
            toAddress: $toAddress, 
            subject: $subject, 
            body: $body,
            highPriority: true
        );
    }
}
