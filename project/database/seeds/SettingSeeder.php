<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            'name' => 'No of days to send before the ending of subscription for Weekly Mail',
            'slug' => 'no-of-days-to-send-before-the-ending-of-subscription-for-weekly-mail',
            'value' => 5,
            'status' => 10,
            'user_id' => 1
        ]);

        DB::table('settings')->insert([
            'name' => 'No of days to send email between days for Weekly Mail',
            'slug' => 'no-of-days-to-send-email-between-days-for-weekly-mail',
            'value' => 2,
            'status' => 10,
            'user_id' => 1
        ]);

        DB::table('settings')->insert([
            'name' => 'No of days to send email between days for Extended Period for Weekly Mail',
            'slug' => 'no-of-days-to-send-email-between-days-for-extended-period-for-weekly-mail',
            'value' => 2,
            'status' => 10,
            'user_id' => 1
        ]);

        DB::table('settings')->insert([
            'name' => 'Time to send E-Mails for Weekly Mail',
            'slug' => 'time-to-send-e-mails-for-weekly-mail',
            'value' => '10:00',
            'status' => 10,
            'user_id' => 1
        ]);
        //WEEKLY
        DB::table('settings')->insert([
            'name' => 'No of days to send before the ending of subscription for Monthly Mail',
            'slug' => 'no-of-days-to-send-before-the-ending-of-subscription-for-monthly-mail',
            'value' => 7,
            'status' => 10,
            'user_id' => 1
        ]);

        DB::table('settings')->insert([
            'name' => 'No of days to send email between days for Monthly Mail',
            'slug' => 'no-of-days-to-send-email-between-days-for-monthly-mail',
            'value' => 2,
            'status' => 10,
            'user_id' => 1
        ]);

        DB::table('settings')->insert([
            'name' => 'No of days to send email between days for Extended Period for Monthly Mail',
            'slug' => 'no-of-days-to-send-email-between-days-for-extended-period-for-monthly-mail',
            'value' => 2,
            'status' => 10,
            'user_id' => 1
        ]);

        DB::table('settings')->insert([
            'name' => 'Time to send E-Mails for Monthly Mail',
            'slug' => 'time-to-send-e-mails-for-monthly-mail',
            'value' => '10:00',
            'status' => 10,
            'user_id' => 1
        ]);
        //Monthly 

        DB::table('settings')->insert([
            'name' => 'No of days to send before the ending of subscription for Quarterly Mail',
            'slug' => 'no-of-days-to-send-before-the-ending-of-subscription-for-quarterly-mail',
            'value' => 5,
            'status' => 10,
            'user_id' => 1
        ]);

        DB::table('settings')->insert([
            'name' => 'No of days to send email between days for Quarterly Mail',
            'slug' => 'no-of-days-to-send-email-between-days-for-quarterly-mail',
            'value' => 2,
            'status' => 10,
            'user_id' => 1
        ]);

        DB::table('settings')->insert([
            'name' => 'No of days to send email between days for Extended Period for Quarterly Mail',
            'slug' => 'no-of-days-to-send-email-between-days-for-extended-period-for-quarterly-mail',
            'value' => 2,
            'status' => 10,
            'user_id' => 1
        ]);

        DB::table('settings')->insert([
            'name' => 'Time to send E-Mails for Quarterly Mail',
            'slug' => 'time-to-send-e-mails-for-quarterly-mail',
            'value' => '10:00',
            'status' => 10,
            'user_id' => 1
        ]);
        //Quarterly
        DB::table('settings')->insert([
            'name' => 'No of days to send before the ending of subscription for Yearly Mail',
            'slug' => 'no-of-days-to-send-before-the-ending-of-subscription-for-yearly-mail',
            'value' => 30,
            'status' => 10,
            'user_id' => 1
        ]);

        DB::table('settings')->insert([
            'name' => 'No of days to send email between days for Yearly Mail',
            'slug' => 'no-of-days-to-send-email-between-days-for-yearly-mail',
            'value' => 2,
            'status' => 10,
            'user_id' => 1
        ]);

        DB::table('settings')->insert([
            'name' => 'No of days to send email between days for Extended Period for Yearly Mail',
            'slug' => 'no-of-days-to-send-email-between-days-for-extended-period-for-yearly-mail',
            'value' => 2,
            'status' => 10,
            'user_id' => 1
        ]);

        DB::table('settings')->insert([
            'name' => 'Time to send E-Mails for Yearly Mail',
            'slug' => 'time-to-send-e-mails-for-yearly-mail',
            'value' => '10:00',
            'status' => 10,
            'user_id' => 1
        ]);
        //Yearly 

        DB::table('settings')->insert([
            'name' => 'VAT Rate',
            'slug' => 'vat-rate',
            'value' => '13',
            'status' => 10,
            'user_id' => 1
        ]); 
        //VAT
    }
}
