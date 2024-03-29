<?php

namespace Decorate\Extensions;

use Decorate\Facades\ImageUpload;

trait ImageUploadable {

    static $cachePath = null;
    static $pathName = 'path';
    static $imageId = 'id';

    public static function boot() {
        parent::boot();

        self::bootBefore();

        self::creating(function ($image) {
            self::creatingBefore($image);

            if(ImageUpload::isBase64($image[self::$pathName])) {
                self::$cachePath = $image[self::$pathName];
                $image[self::$pathName] = 'tmp';
            }

            self::creatingAfter($image);
        });

        self::created(function ($image) {
            self::createdBefore($image);

            if(ImageUpload::isBase64(self::$cachePath)) {
                self::saveImage(self::$cachePath, $image);
                self::$cachePath = null;
            }

            self::createdAfter($image);
        });

        self::updating(function ($image) {
            self::updatingBefore($image);

            if(ImageUpload::isBase64($image[self::$pathName])) {
                $d = self::getDir($image);
                ImageUpload::deleteDirectory($d);
                self::saveImage($image[self::$pathName], $image);
            }

            self::updatingAfter($image);
        });

        self::deleting(function ($image) {
            self::deletingBefore($image);

            self::deletingAfter($image);
        });

        self::bootAfter();
    }

    /**
     * @param $image
     * @throws \Illuminate\Validation\ValidationException
     */
    private static function saveImage($source, $image) {
        $file = ImageUpload::createUploadFile($source);
        $dir = self::getDir($image);
        $image[self::$pathName] = ImageUpload::saveMultiImage($file, $dir);
        $image->save();
    }

    private static function getDir($image) {
        $d = rtrim(self::$saveDir, '/');
        return "{$d}/{$image[self::$imageId]}";
    }

    static function bootBefore() {

    }

    static function bootAfter() {

    }

    static function creatingBefore($image) {

    }

    static function creatingAfter($image) {

    }

    static function createdBefore($image) {

    }

    static function createdAfter($image) {

    }

    static function updatingBefore($image) {

    }

    static function updatingAfter($image) {

    }

    static function deletingBefore($image) {

    }

    static function deletingAfter($image) {

    }

}
