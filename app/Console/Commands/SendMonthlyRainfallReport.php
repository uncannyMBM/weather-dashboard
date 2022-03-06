<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DomPDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendMonthlyRainfallReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monthly:rainfall';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A Command to send monthly rainfall data to user.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = DB::table('notification_type_recipient_preferences')->where(['notification_types_id' => 4, 'active' => 1,])
            ->join('notification_recipients', 'notification_recipients.id', '=', 'notification_type_recipient_preferences.notification_recipients_id')
            ->join('companies', 'companies.id', '=', 'notification_recipients.company_id')
            ->select(DB::raw('(notification_recipients.email_id) as email, (notification_recipients.cc_email_ids) as ccemail ,companies.name,
       companies.id, notification_recipients.full_name '))
            ->get();
        try {
            foreach ($users as $user) {
                $companyid = $user->id;
                $company_email = $user->email;
                $company = $user->name;
                $data = DB::table('base_station_company')->join('companies', 'companies.id', '=', 'base_station_company.company_id')
                    ->join('base_stations', 'base_stations.id', '=', 'base_station_company.base_station_id')
                    ->select(DB::raw('base_stations.tag,(base_stations.name) as nam,
          base_stations.status,
          base_stations.status_log as value'))
                    ->where('companies.id', $companyid)
                    ->whereNull('base_stations.deleted_at')
                    ->get();

                $dompdf = DomPDF::loadView('report.monthlyEmailReport', compact('company', 'data'));
                $dompdf->setPaper('A3', 'landscape');
                $pdf = $dompdf->output();
                $data = [
                    'details' => 'Please find attached your',
                    'company_name' => $user->name,
                    'user_name' => $user->full_name,
                ];
                Mail::send('report.emailbody', $data, function ($message) use ($user, $pdf) {
                    $message->from('mdkamruzzaman@ontoto.com', 'Ontoto Pty Ltd.');
                    $message->subject('Monthly RainFall Report');
                    $message->to($user->email)->cc($user->ccemail ?: []);
                    $message->attachData($pdf, 'report.pdf', [
                        'mime' => 'application/pdf',
                    ]);
                });
            }
        } catch (\Throwable $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";

        }
    }
}
