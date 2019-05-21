<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use App\Traits\File;

class ImageController extends Controller
{
    use File;

    private $imagePath;

    public function __construct()
    {
        $this->imagePath = public_path('/images');
    }

    public function index()
    {
        $images = \App\Image::all();

        return $images;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg'
        ]);

        $options = json_decode($request->input('options'), true);

        Validator::make($options, [
            'is_text_watermark' => 'required|boolean',
            'is_text_autocolor' => 'required|boolean',
            'is_image_watermark' => 'required|boolean',
            'is_crop' => 'required|boolean',
            'text_watermark' => 'alpha_num|max:255',
            'crop.width' => 'required_if:is_crop,1|integer|min:50|max:1000',
            'crop.height' => 'required_if:is_crop,1|integer|min:50|max:1000',
        ])->validate();

        //dd($options);
        $image = $request->file('file');

        $originalImageName = $this->randomFileName($image);
        $originalImageThumbnailName = $this->randomFileName($image);

        $this->createThumbnail($image)->save($this->imagePath .'/originals_thumbnail/'. $originalImageThumbnailName);

        if ($options['is_text_watermark']) {
            $textWatermarkNames = $this->createImageWithTextWatermark($image, $options['is_text_autocolor'], $options['text_watermark']);
        }

        if ($options['is_image_watermark']) {
            $imageWatermarkNames = $this->createImageWithImageWatermark($image);
        }

        if ($options['is_crop']) {
            $croppedNames = $this->cropImage($image, $options['crop']['width'], $options['crop']['height']);
        }


        $image->move($this->imagePath.'/originals', $originalImageName);

        $upload = new \App\Image();
        $upload->name = $originalImageName;
        $upload->original_thumbnail_name = $originalImageThumbnailName;
        $upload->watermark_image_name = $imageWatermarkNames[0] ?? NULL;
        $upload->watermark_image_thumbnail_name = $imageWatermarkNames[1] ?? NULL;
        $upload->watermark_text_name = $textWatermarkNames[0] ?? NULL;
        $upload->watermark_text_thumbnail_name = $textWatermarkNames[1] ?? NULL;
        $upload->cropped_name = $croppedNames[0] ?? NULL;
        $upload->cropped_thumbnail_name = $croppedNames[1] ?? NULL;
        $upload->original_name = basename($image->getClientOriginalName());
        $upload->save();

        return response()->json([
            'status'  => '200',
            'result'  => true,
            'message' => 'Image saved Successfully'
        ]);
    }

    public function createImageWithTextWatermark($image, $autoColorText, $text)
    {
        if (empty($text)) $text = 'default text';
        $pathOrigin = '/watermarks_text/';
        $pathThumbnail = '/watermarks_text_thumbnail/';
        $color = '#fdf6e3';
        $imageTextWatermark = Image::make($image);

        if ($autoColorText) {
            $color = $this->isLightImage($image);
        }

        $imageTextWatermark->text($text, 100, 100, function($font) use ($color) {
            $font->file('fonts/RemachineScript_Personal_Use.ttf');
            $font->color($color);
            $font->size(100);
        });

        return $this->saveImagesWithWatermarks($image, $imageTextWatermark, $pathOrigin, $pathThumbnail);
    }

    public function createImageWithImageWatermark($image)
    {
        $watermarkPath = public_path('/watermarks');

        if ($this->isEmptyDir($watermarkPath)) {
            return false;
        }

        $pathOrigin = '/watermarks_image/';
        $pathThumbnail = '/watermarks_image_thumbnail/';

        $imageFileWatermark = Image::make($image);

        $watermarkImg = scandir($watermarkPath);

        $watermark = Image::make(public_path('/watermarks'). '/' . $watermarkImg[2]);

        $imageFileWatermark->insert($watermark, 'bottom-right', 10, 10);

        return $this->saveImagesWithWatermarks($image, $imageFileWatermark, $pathOrigin, $pathThumbnail);
    }

    public function createThumbnail($image)
    {
        $resize = Image::make($image)->resize(200, null, function ($constraint) {
            $constraint->aspectRatio();
        });

        return $resize;
    }

    public function saveImagesWithWatermarks($image, $watermarkImage, $pathOrigin, $pathThumbnail)
    {
        $imageWatermarkName = $this->randomFileName($image);
        $imageWatermarkThumbnailName = $this->randomFileName($image);

        $namesImagesWatermark = [$imageWatermarkName, $imageWatermarkThumbnailName];

        $watermarkImage->save($this->imagePath .$pathOrigin. $imageWatermarkName);

        $this->createThumbnail($watermarkImage)
            ->save($this->imagePath .$pathThumbnail. $imageWatermarkThumbnailName);

        return $namesImagesWatermark;
    }

    public function cropImage($image, $width, $height)
    {
        if (empty($width)) $width = 300;
        if (empty($height)) $height = 200;

        $imageCrop = Image::make($image);

        $name = $this->randomFileName($image);
        $thumbnailName = $this->randomFileName($image);

        $imageCrop->fit($width, $height, function ($constraint) {
            $constraint->upsize();
        })->save($this->imagePath .'/cropped/'. $name);

        $this->createThumbnail($imageCrop)
            ->save($this->imagePath .'/cropped_thumbnail/'. $thumbnailName);;

        return [$name, $thumbnailName];
    }

    public function isLightImage($image)
    {
        $image = Image::make($image);
        $width = $image->width();
        $height = $image->height();
        $lightArr = [];

        for ($w = 0; $w < $width; $w++) {
            for ($h = 0; $h < $height; $h++) {
                $color = $image->pickColor($w, $h);
                $hsl = $this->rgbToHsl($color[0],$color[1], $color[2]);
                $lightArr[] = $hsl[2];
            }
        }

        $averageLight = round(array_sum($lightArr)/count($lightArr),2);

        if ($averageLight >= 0.50) {
            return '#000000';
        } else {
            return '#ffffff';
        }
    }


}
