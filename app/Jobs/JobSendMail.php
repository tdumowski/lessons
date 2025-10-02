<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require 'vendor/autoload.php'; // Composer

class JobSendMail implements ShouldQueue
{
    use Queueable;

    public string $toAddress;
    public string $subject;
    public string $body;
    public bool $confidential;
    public bool $highPriority;

    /**
     * Create a new job instance.
     */
    public function __construct(string $toAddress, string $subject, string $body, bool|null $confidential = false, bool|null $highPriority = false)
    {
        $this->toAddress = $toAddress;
        $this->subject = $subject;
        $this->body = $body;
        $this->confidential = $confidential;
        $this->highPriority = $highPriority;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $mail = new PHPMailer(true);

        try {
            // Konfiguracja serwera SMTP
            $mail->isSMTP();
            $mail->Host = env('GMAIL_HOST');   // adres serwera SMTP
            $mail->SMTPAuth = true;
            $mail->Username = env('GMAIL_USERNAME');
            $mail->Password = env('GMAIL_PASSWORD'); // NIE hasło główne Gmail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = env('GMAIL_PORT');

            // Nadawca i odbiorca
            $mail->setFrom(env('GMAIL_FROM_EMAIL'), env('APP_NAME'));
            $mail->addAddress($this->toAddress);
            $mail->addReplyTo(env('GMAIL_FROM_EMAIL'));

            // Treść wiadomości
            $mail->isHTML(true);
            $mail->Subject = $this->subject;
            $mail->Body = $this->body;
            $mail->AltBody = self::stripHtml($this->body);

            if ($this->confidential) {
                $mail->AddCustomHeader("Sensitivity: Company-Confidential");
                $mail->AddCustomHeader("Classification: Confidential");
            }

            $mail->Priority = $this->highPriority;

            $mail->send();
            //LOG
        } catch (Exception $e) {
            //LOG
        }
    }

    private static function stripHtml(string $html): string
    {
        // Zamień typowe znaczniki łamania wierszy na \n
        $htmlWithBreaks = preg_replace([
            '/<br\s*\/?>/i',
            '/<\/p>/i',
            '/<\/div>/i',
            '/<\/li>/i',
            '/<\/h[1-6]>/i',
        ], "\n", $html);

        // Usuń wszystkie pozostałe znaczniki HTML
        $text = strip_tags($htmlWithBreaks);

        // Usuń nadmiarowe białe znaki i znormalizuj nowe linie
        $text = preg_replace("/\n{2,}/", "\n\n", $text); // podwójne nowe linie dla lepszej czytelności
        $text = trim($text);

        return $text;
    }
}
