<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use App\Traits\File;

class WatermarkController extends Controller
{
    use File;

    private $watermarkPath;

    public function __construct()
    {
        $this->watermarkPath = public_path('/watermarks');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg'
        ]);

        $watermark = $request->file('file');

        if(!$this->isEmptyDir($this->watermarkPath)) {
            array_map('unlink', array_filter((array) glob($this->watermarkPath.'/*')));
        }

        Image::make($watermark)->resize(200, null, function ($constraint) {
            $constraint->aspectRatio();
        })->save($this->watermarkPath .'/'. $this->randomFileName($watermark));

        return response()->json([
            'status'  => '200',
            'result'  => true,
            'message' => 'watermark saved successfully'
        ]);
    }
}
