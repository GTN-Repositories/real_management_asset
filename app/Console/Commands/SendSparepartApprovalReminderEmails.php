<?php

namespace App\Console\Commands;

use App\Mail\SparepartApprovalReminderEmail;
use App\Models\GeneralSetting;
use App\Models\ItemStock;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendSparepartApprovalReminderEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-sparepart-approval-reminder-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim pengingat email untuk persetujuan permintaan sparepart yang masih pending.';
    
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $general = GeneralSetting::where('group', 'reminder')->where('key', 'approval_reminder_sparepart_period')->orderBy('id', 'desc')->first();
        $generalEmailSmtp = GeneralSetting::orderBy('value', 'asc')->where('group', 'email_reminder')->where('key', 'email_sender_smtp')->pluck('value');

        if ($general) {
            $value = $general->value;
        } else {
            $value = 'pending';
            Log::info('Reminder period for petty cash not found. Using default value: ' . $value);
        }

        if ($general->status == 'active') {
            $pendingRequests = ItemStock::where('status', 'pending')->get();

            foreach ($pendingRequests as $request) {
                // Log::info('Sending email reminder for sparepart request: ' . $request);
                Mail::to($generalEmailSmtp)->send(new SparepartApprovalReminderEmail($request));
            }

            $this->info('Pengingat email untuk persetujuan permintaan sparepart telah dikirim.');
        }
    }
}
