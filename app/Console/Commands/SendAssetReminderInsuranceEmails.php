<?php

namespace App\Console\Commands;

use App\Mail\AssetReminderInsuranceEmail;
use App\Models\Asset;
use App\Models\GeneralSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAssetReminderInsuranceEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-asset-reminder-insurance-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim pengingat email untuk aset yang akan berakhir masa berlaku asuransinya.';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $general = GeneralSetting::where('group', 'reminder')->where('key', 'insurance_reminder_period')->orderBy('id', 'desc')->first();
        $generalEmailSmtp = GeneralSetting::orderBy('value', 'asc')->where('group', 'email_reminder')->where('key', 'email_sender_smtp')->pluck('value');

        if ($general) {
            $reminderPeriod = (int)$general->value;
        } else {
            $reminderPeriod = 30;
            Log::info('Reminder period for insurance not found. Using default value: ' . $reminderPeriod);
        }

        if ($general->status == 'active') {
            $assets = Asset::whereDate('asuransi_date', '<=', now()->addDays($reminderPeriod))
                        ->whereDate('asuransi_date', '>=', now())->get();

            foreach ($assets as $asset) {
                // Log::info('Sending email reminder for asset: ' . $asset);
                Mail::to($generalEmailSmtp)->send(new AssetReminderInsuranceEmail($asset));
            }

            $this->info('Pengingat email reminder telah dikirim.');
        }
    }
}
