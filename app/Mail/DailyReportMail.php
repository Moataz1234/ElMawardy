<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DailyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $reportData;
    protected $pdf;
    protected $reportDate;

    public function __construct($reportData, $pdf, $reportDate)
    {
        $this->reportData = $reportData;
        $this->pdf = $pdf;
        $this->reportDate = $reportDate;
    }

    public function build()
    {
        try {
            Log::info('Attempting to build email');
            return $this->subject('Daily Sales Report - ' . $this->reportDate)
                       ->view('Admin.Reports.gold_report')
                       ->attachData($this->pdf->output(), 'sales_report_' . $this->reportDate . '.pdf')
                       ->with([
                           'reportData' => $this->reportData,
                           'reportDate' => $this->reportDate,
                           'isPdf' => true
                       ]);
        } catch (\Exception $e) {
            Log::error('Error building email: ' . $e->getMessage());
            throw $e;
        }
    }
}