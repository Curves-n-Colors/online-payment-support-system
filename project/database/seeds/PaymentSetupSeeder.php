<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\PaymentSetup;
use App\Models\Client;
use App\User;

class PaymentSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = collect(User::all()->modelKeys());
        $clients = collect(Client::all()->modelKeys());
        $payment_options = ['NIBL','KHALTI'];
        $contents = [
            [
                "title" => "Server Support",
                "amount" => "15,000.00",
                "description" => "Server configuration\\r\\nUsability & Security Testing\\r\\nQuality Checks\\r\\nServer maintenance\\r\\nDaily Server Backup",
                "link_title" => "Contact us to upgrade the service",
                "link_url" => "https://web.whatsapp.com/"
            ],
            [
                "title" => "Content Management Service",
                "amount" => "20,000.00",
                "description" => "Content Writing\\r\\nGraphic Design\\r\\nRegular content update in the website",
                "link_title" => "Contact us to upgrade the service",
                "link_url" => "https://web.whatsapp.com/"
            ]
        ];

        for($i=0; $i<=366; $i++) {
            $model = new PaymentSetup();
            $model->title      = 'Monthly Support Service - ' . $i;
            $model->client_id  = 1;
            $model->email      = 'gauroughh@gmail.com';
            $model->uuid       = Str::uuid()->toString();
            $model->total      = (float) 35000;
            $model->currency   = 'NPR';
            $model->remarks    = 'Seeder Testing';
            $model->contents   = json_encode($contents);
            $model->is_active  = 10;
            $model->is_advance = 10;
            $model->user_id    = 1;
            $model->payment_options = json_encode($payment_options);
            $model->reference_date  = now()->subDays($i);
            $model->recurring_type  = 3; // MONTHLY

            $model->save();
        }
    }
}
