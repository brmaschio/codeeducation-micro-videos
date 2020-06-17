<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

trait UploadFiles
{

    protected $oldFiles = [];

    protected abstract function uploadDir();

    public static function bootUploader()
    {
        static::updating(function (Model $model) {

            $fieldsUpdated = array_keys($model->getDirty());
            $filefieldsUpdated = array_intersect($fieldsUpdated, self::$fileFields);
            $fileFieldsToBeUpdated = Arr::where($filefieldsUpdated, function ($fileField) use ($model) {
                return $model->getOriginal($fileField);
            });

            $model->oldFiles = array_map(function ($fileField) use ($model) {
                return $model->getOriginal($fileField);
            }, $fileFieldsToBeUpdated);
        });
    }

    public function deleteOldFiles()
    {
        $this->deleteFiles($this->oldFiles);
    }

    /**
     *  @param UploadedFile[] $files
     */
    public function uploadFiles(array $files)
    {
        foreach ($files as $file) {
            $this->uploadFile($file);
        }
    }

    public function uploadFile(UploadedFile $file)
    {
        $file->store($this->uploadDir());
    }

    public function deleteFiles(array $files)
    {
        foreach ($files as $file) {
            $this->deleteFile($file);
        }
    }

    /**
     *  @param string|UploadedFile $files
     */
    public function deleteFile($file)
    {
        $filename = $file instanceof UploadedFile ? $file->hashName() : $file;
        \Storage::delete("{$this->uploadDir()}/{$filename}");
    }

    public static function extractFiles(array &$attributes = [])
    {
        $files = [];
        foreach (self::$fileField as $file) {
            if(isset($attributes[$file]) && $attributes[$file] instanceof UploadedFile){
                $files[] = $attributes[$file];
                $attributes[$file] = $attributes[$file]->hashName();
            }
        }
        return $files;
    }

}
