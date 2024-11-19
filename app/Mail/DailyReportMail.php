<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DailyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reportsData;
    public $pdf;

    public function __construct($reportsData, $pdf)
    {
        $this->reportsData = $reportsData;
        $this->pdf = $pdf;
    }

    public function build()
    {
        try {
            Log::info('Attempting to build email');
            return $this->subject('Daily Gold Sales Report - ' . now()->format('d-m-Y'))
                       ->view('emails.daily_report')
                       ->attachData($this->pdf->output(), 'daily_report.pdf');
        } catch (\Exception $e) {
            Log::error('Error building email: ' . $e->getMessage());
            throw $e;
        }
    }
}