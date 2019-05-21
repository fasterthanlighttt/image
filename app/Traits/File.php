<?php

namespace App\Traits;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

trait File
{
    public function randomFileName($file)
    {
        $fileName = Str::random(30) .'.'. $file->getClientOriginalExtension();

        return $fileName;
    }

    public function isEmptyDir($dir)
    {
        if (($files = scandir($dir)) && count($files) <= 2) {
            return true;
        }
        return false;
    }

    public function rgbToHsl( $r, $g, $b ) {
        $oldR = $r;
        $oldG = $g;
        $oldB = $b;
        $r /= 255;
        $g /= 255;
        $b /= 255;
        $max = max( $r, $g, $b );
        $min = min( $r, $g, $b );
        $h = '';
        $s = '';
        $l = ( $max + $min ) / 2;
        $d = $max - $min;
        if( $d == 0 ){
            $h = $s = 0; // achromatic
        } else {
            $s = $d / ( 1 - abs( 2 * $l - 1 ) );
            switch( $max ){
                case $r:
                    $h = 60 * fmod( ( ( $g - $b ) / $d ), 6 );
                    if ($b > $g) {
                        $h += 360;
                    }
                    break;
                case $g:
                    $h = 60 * ( ( $b - $r ) / $d + 2 );
                    break;
                case $b:
                    $h = 60 * ( ( $r - $g ) / $d + 4 );
                    break;
            }
        }

        return array( round( $h, 2 ), round( $s, 2 ), round( $l, 3 ) );
    }
}