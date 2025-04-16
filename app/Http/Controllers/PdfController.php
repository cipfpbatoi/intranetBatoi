<?php

namespace Intranet\Http\Controllers;
use Imagick;


class PdfController extends Controller
{
    public function index()
    {
        $imagick = new Imagick(public_path('A5.pdf'));



        $saveImagePath = public_path('/conversio/A5.jpg');
        $imagick->writeImages($saveImagePath, true);

        return response()->file($saveImagePath);
    }
}
