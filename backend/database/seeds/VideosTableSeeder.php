<?php

use App\Models\CastMember;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

class VideosTableSeeder extends Seeder
{

    private $allGenres = [];
    private $allCastMembers = [];
    private $relations = [
        'categories_id' => [],
        'genres_id' => [],
        'cast_members_id' => []
    ];

    public function run()
    {

        $dir = \Storage::getDriver()->getAdapter()->getPathPrefix();
        \File::deleteDirectory($dir, true);

        $self = $this;
        $this->allGenres = Genre::all();
        $this->allCastMembers = CastMember::all();

        Model::reguard();

        factory(Video::class, 100)->make()->each(function (Video $video) use ($self) {
            $self->fetchRelations();
            Video::create(array_merge(
                $video->toArray(),
                [
                    'tumb_file' => $self->getImageFile(),
                    'banner_file' => $self->getImageFile(),
                    'trailer_file' => $self->getVideoFile(),
                    'video_file' => $self->getVideoFile(),
                ],
                $this->relations
            ));
        });

        Model::unguard();
    }

    public function fetchRelations()
    {

        $subGenres = $this->allGenres->random(2)->load('categories');
        $categoriesId = [];

        foreach ($subGenres as $genre) {
            array_push($categoriesId, ...$genre->categories->pluck('id')->toArray());
        }

        $categoriesId = array_unique($categoriesId);
        $genresId = $subGenres->pluck('id')->toArray();

        $this->relations['categories_id'] = $categoriesId;
        $this->relations['genres_id'] = $genresId;
        $this->relations['cast_members_id'] = $this->allCastMembers->random(3)->pluck('id')->toArray();

    }

    public function getImageFile()
    {

        return new UploadedFile(
            storage_path('faker/teste.jpeg'),
            'teste.jpeg'
        );
    }

    public function getVideoFile()
    {

        return new UploadedFile(
            storage_path('faker/teste.mp4'),
            'teste.mp4'
        );
    }
}
