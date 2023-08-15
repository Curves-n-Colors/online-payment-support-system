<?php

namespace App\Services\Backend;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class LogsService
{
    public static function _set($text, $type)
    {
    	$path = 'public/logs/';
    	$file = 'audit.log';
        if(Auth::check())
        {
            $user = auth()->user();
            $content = $text . '||' . $type . '||' . date('Y-m-d H:i:s') . '||' . $user->name . '||' . $user->id;
        }
        else {
            $content = $text . '||' . $type . '||' . date('Y-m-d H:i:s') . '||SYSTEM||0';
        }


    	if (Storage::exists($path . $file)) {
    		Storage::append($path . $file, $content);
    		$size = ( Storage::size($path . $file) / 1024 ) / 1024; // convert into MB

    		if ($size > 5) {
    			$files = Storage::files($path);
    			Storage::copy($path . $file, $path . $file . count($files));
    			Storage::delete($path . $file);
    		}
    	}
        else {
        	Storage::put($path . $file, $content);
        }
    }

}
