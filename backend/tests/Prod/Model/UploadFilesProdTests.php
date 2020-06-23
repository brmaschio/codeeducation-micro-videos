<?php

namespace Tests\Prod\Model;

use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\TestCase;
use Tests\Stubs\Models\UploadFileStub;
use Tests\Traits\TestProd;
use Tests\Traits\TestStorages;

class UploadFilesProdTests extends TestCase
{

    use TestStorages, TestProd;
    private $obj;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->skipTestIfNotProd();
        
        $this->obj = new UploadFileStub();
        \Config::set('filesystems.default', 'gcs');   
        $this->deleteAllFiles();     
    }

    public function testUploadFile()
    {
        // \Storage::fake();
        $file = UploadedFile::fake()->create('video.mp4');
        $this->obj->uploadFile($file);
        \Storage::assertExists("1/{$file->hashName()}");
    }

    public function testUploadFiles()
    {
        // \Storage::fake();
        $file = UploadedFile::fake()->create('video.mp4');
        $file2 = UploadedFile::fake()->create('video-2.mp4');
        $file3 = UploadedFile::fake()->image('tumb.jpg');
        $this->obj->uploadFiles([$file, $file2, $file3]);
        \Storage::assertExists("1/{$file->hashName()}");
        \Storage::assertExists("1/{$file2->hashName()}");
        \Storage::assertExists("1/{$file3->hashName()}");
    }

    public function testeDeleteFile()
    {
        // \Storage::fake();

        $file = UploadedFile::fake()->create('video.mp4');
        $this->obj->uploadFile($file);
        $fileName = $file->hashName();
        $this->obj->deleteFile($fileName);
        \Storage::assertMissing("1/{$fileName}");

        $file = UploadedFile::fake()->create('video.mp4');
        $this->obj->uploadFile($file);
        $fileName = $file->hashName();
        $this->obj->deleteFile($file);
        \Storage::assertMissing("1/{$fileName}");
    }

    public function testDeleteFiles()
    {
        // \Storage::fake();
        $file = UploadedFile::fake()->create('video.mp4');
        $file2 = UploadedFile::fake()->create('video-2.mp4');
        $this->obj->uploadFiles([$file, $file2]);

        $fileName = $file->hashName();
        $this->obj->deleteFiles([$fileName, $file2]);

        \Storage::assertMissing("1/{$fileName}");
        \Storage::assertMissing("1/{$file2->hashName()}");
    }


    public function testeDeleteOldFile()
    {
        // \Storage::fake();
        $file = UploadedFile::fake()->create('video.mp4');
        $file2 = UploadedFile::fake()->image('tumb.jpg');
        $this->obj->uploadFiles([$file, $file2]);
        $this->obj->deleteOldFiles();
        $this->assertCount(2, \Storage::allFiles());

        $this->obj->oldFiles = [$file2->hashName()];
        $this->obj->deleteOldFiles();
        \Storage::assertMissing("1/{$file2->hashName()}");
        \Storage::assertExists("1/{$file->hashName()}");

    }
}
