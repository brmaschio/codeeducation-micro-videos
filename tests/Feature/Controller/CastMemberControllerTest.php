<?php

namespace Tests\Controller\Feature;

use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CastMemberControllerTest extends TestCase
{

    use DatabaseMigrations, TestValidations, TestSaves;

    private $castMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->castMember = factory(CastMember::class)->create([
            'type' => CastMember::TYPE_DIRECTOR
        ]);
    }

    public function testIndex()
    {
        $response = $this->get(route('cast_members.index'));
        $response->assertStatus(200)->assertJson([$this->castMember->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route('cast_members.show', ['cast_member' => $this->castMember->id]));
        $response->assertStatus(200)->assertJson($this->castMember->toArray());
    }

    public function testInvalidationData()
    {
        $data = ['name' => '', 'type' => ''];
        $this->assertInvalidationStoreActions($data, 'required');
        $this->assertInvalidationUpdateActions($data, 'required');
    }

    public function testStore()
    {
        $data = ['name' => 'test', 'type' => CastMember::TYPE_DIRECTOR];

        $dataCheck = ['deleted_at' => null];

        $response = $this->assertStore($data, $data + $dataCheck);
        $response->assertJsonStructure(['created_at', 'updated_at']);
    }

    public function testUpdate()
    {
        $data = ['name' => 'test_update', 'type' => CastMember::TYPE_ACTOR];

        $dataCheck = ['deleted_at' => null];
        $response = $this->assertUpdate($data, $data + $dataCheck);
        $response->assertJsonStructure(['created_at', 'updated_at']);
    }

    public function testDelete()
    {
        $response = $this->json('DELETE', route('cast_members.destroy', ['cast_member' => $this->castMember->id]));

        $response->assertStatus(204);
        $this->assertNull(CastMember::find($this->castMember->id));
    }

    protected function routeStore()
    {
        return route('cast_members.store');
    }

    protected function routeUpdate()
    {
        return route('cast_members.update', [$this->castMember->id]);
    }

    protected function model()
    {
        return CastMember::class;
    }
}
