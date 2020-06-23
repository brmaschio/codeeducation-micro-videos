<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tests\Stubs\Models\UploadFileStub;

class UpdateFileStubTest extends TestCase
{

    private $obj;

    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new UploadFileStub();
        
        UploadFileStub::dropTable();
        UploadFileStub::makeTable();
    }

    public function testeMakeOldFieldsOnSaving()
    {
        $this->obj->fill(['name' => 'teste', 'file' => 'file.mp4', 'file2' => 'image.jpg']);
        $this->obj->save();

        $this->assertCount(0, $this->obj->oldFiles);

        $this->obj->update(['name' => 'teste updated', 'file' => 'newFile.mp4']);

        $this->assertEqualsCanonicalizing(['file.mp4'], $this->obj->oldFiles);
    }

    public function testMakeOldFilesOnSaveNullable()
    {
        $obj = UploadFileStub::created([
            'name' => 'test'
        ]);

        $this->assertCount(0, $this->obj->oldFiles);

        $this->obj->update([
            'name' => 'teste updated',
            'file' => 'file.mp4'
        ]);

        $this->assertEquals([], $this->obj->oldFiles);
    }
}
