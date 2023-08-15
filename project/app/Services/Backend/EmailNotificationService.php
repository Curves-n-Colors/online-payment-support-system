<?php

namespace App\Services\Backend;

use App\Models\EmailNotification;

class EmailNotificationService
{
    public static function _storing($data)
    {
        $model = new EmailNotification();
        $model->client_id = $data->client_id;
        $model->email     = $data->email;
        $model->uuid      = $data->uuid; // either payment-entry or payment-detail
        $model->data      = json_encode($data->body);
        return $model->save();
    }
}
