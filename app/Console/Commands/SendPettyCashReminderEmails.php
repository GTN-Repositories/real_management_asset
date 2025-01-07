<?php

namespace App\Console\Commands;

use App\Mail\PettyCashReminderEmail;
use App\Models\GeneralSetting;
use App\Models\PettyCash;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPettyCashReminderEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-petty-cash-reminder-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim pengingat email untuk permintaan petty cash yang mendekati batas waktu.';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $general = GeneralSetting::where('group', 'reminder')->where('key', 'petty_cash_reminder_period')->orderBy('id', 'desc')->first();
        $generalEmailSmtp = GeneralSetting::orderBy('value', 'asc')->where('group', 'email_reminder')->where('key', 'email_sender_smtp')->pluck('value');

        if ($general) {
            $reminderPeriod = (int)$general->value;
        } else {
            $reminderPeriod = 30;
            Log::info('Reminder period for petty cash not found. Using default value: ' . $reminderPeriod);
        }

        if ($general->status == 'active') {
            $pettyCashRequests = PettyCash::where('status', 1)
                                        ->whereDate('date', '<=', now()->addDays($reminderPeriod))
                                        ->get();

            foreach ($pettyCashRequests as $request) {
                // Log::info('Sending email reminder for petty cash request: ' . $request);
                Mail::to($generalEmailSmtp)->send(new PettyCashReminderEmail($request));
            }

            $this->info('Pengingat email untuk petty cash telah dikirim.');
        }
    }
}
