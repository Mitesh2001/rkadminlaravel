<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Helper;

class ImportsController extends Controller
{
    private function getRows($file)
    {
        $replace = substr($file, 0, strpos($file, ',') + 1);
        $newFile = str_replace($replace, '', $file);
        $newFile = str_replace(' ', '+', $newFile);
        $rows = explode("\n", base64_decode($newFile));
        $array = array_map('str_getcsv', $rows);
        return $array;
    }
}
