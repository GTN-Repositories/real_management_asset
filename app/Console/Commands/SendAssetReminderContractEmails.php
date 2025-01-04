<?php

namespace App\Console\Commands;

use App\Mail\AssetReminderContractEmail;
use App\Models\Asset;
use App\Models\GeneralSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAssetReminderContractEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-asset-reminder-contract-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim pengingat email untuk asset dengan masa berlaku kontrak (contract_period) mendekati habis.';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $general = GeneralSetting::where('group', 'reminder')->where('key', 'contract_reminder_period')->orderBy('id', 'desc')->first();
        $generalEmailSmtp = GeneralSetting::orderBy('value', 'asc')->where('group', 'email_reminder')->where('key', 'email_sender_smtp')->pluck('value');

        if ($general) {
            $reminderPeriod = (int)$general->value;
        } else {
            $reminderPeriod = 30;
            Log::info('Reminder period for contract not found. Using default value: ' . $reminderPeriod);
        }

        if ($general->status == 'active') {
            $assets = Asset::whereNotNull('contract_period') // Pastikan field contract_period ada
                ->whereDate('contract_period', '<=', now()->addDays($reminderPeriod))
                ->whereDate('contract_period', '>=', now()) // Masih relevan
                ->get();
    
            foreach ($assets as $asset) {
                // Log::info('Sending email reminder for asset: ' . $asset);
                Mail::to($generalEmailSmtp)->send(new AssetReminderContractEmail($asset));
            }
    
            $this->info('Pengingat email untuk contract_period telah dikirim.');
        }
    }
}
