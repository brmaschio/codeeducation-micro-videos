<?php

namespace Tests\Stubs\Models;

use App\Traits\UploadFiles;
use Illuminate\Database\Eloquent\Model;

class UploadFileStub extends Model
{

    use UploadFiles;

    public static $fileField = ['file', 'file2']; 

    protected function uploadDir()
    {
        return "1";
    }
}
