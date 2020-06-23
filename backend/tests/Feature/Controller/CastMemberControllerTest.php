<?php

namespace Tests\Feature\Controller;

use App\Http\Resources\CastMemberResource;
use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CastMemberControllerTest extends TestCase
{

    use DatabaseMigrations, TestValidations, TestSaves;

    private $serializeFields = ['id','name','type','created_at','updated_at','deleted_at'];
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
        $response->assertStatus(200)->assertJson([
            'meta' => ['per_page' => 15]
        ])->assertJsonStructure([
            'data' => ['*' => $this->serializeFields],
            'links' => [],
            'meta'  => [],
        ]);

        $resource = CastMemberResource::collection(collect([$this->castMember]));
        $response->assertJson($resource->response()->getData(true));
    }

    public function testShow()
    {
        $response = $this->get(route('cast_members.show', ['cast_member' => $this->castMember->id]));
        $response->assertStatus(200);

        $id = $response->json('data.id');
        $resource = new CastMemberResource(CastMember::find($id));
        $response->assertJson($resource->response()->getData(true));
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
        $response->assertJsonStructure(['data' => $this->serializeFields]);

        $id = $response->json('data.id');
        $resource = new CastMemberResource(CastMember::find($id));
        $response->assertJson($resource->response()->getData(true));
    }

    public function testUpdate()
    {
        $data = ['name' => 'test_update', 'type' => CastMember::TYPE_ACTOR];

        $dataCheck = ['deleted_at' => null];
        $response = $this->assertUpdate($data, $data + $dataCheck);
        $response->assertJsonStructure(['data' => $this->serializeFields]);

        $id = $response->json('data.id');
        $resource = new CastMemberResource(CastMember::find($id));
        $response->assertJson($resource->response()->getData(true));
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
