<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{

    private $category;

// ==========================================================================

    // execultado antes de cada teste da classe
    public static function setUpBeforeClass(): void
    {
        // nao e possivel ter acesso a variaveis
        // usado para alterar veriaveis globais
        // usado para variaveis compartilhadas a todos metodos de teste
        parent::setUpBeforeClass();

        // exemplo, criar algo no banco de dados

    }

    // -----------------------------------------------------------------------
    // metodos execultados a cada teste de funcao
    // é sempre criado e destruido

    // sobrescreve setup do php
    // execultado antes de cada teste de funcao
    protected function setUp() : void
    {
        parent::setUp();
        $this->category = new Category();
    }

    // execultado ao final de cada teste de funcao
    protected function tearDown(): void
    {
        // podem ser realizados processo antes
        parent::tearDown();
        // ou depois da destricao completa
    }
    // -----------------------------------------------------------------------

    // execultado ao final de cada teste da classe
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        // exemplo excluir um arquivo
    }

// ==========================================================================

    public function testFillable()
    {
        $fillable = ['name', 'description', 'is_active'];

        $this->assertEquals($fillable, $this->category->getFillable());
    }

    public function testUseTraits()
    {
        $traits = [SoftDeletes::class, Uuid::class];

        $categoryTraits = array_keys(class_uses(Category::class));

        $this->assertEquals($traits, $categoryTraits);
    }

    public function testCasts()
    {
        $casts = ['id' => 'string', 'is_active' => 'boolean'];

        $this->assertEquals($casts, $this->category->getCasts());
    }

    public function testIncrementing()
    {
        $this->assertFalse($this->category->incrementing);
    }

    public function testDatesAtributes()
    {
        $dates = ['deleted_at', 'updated_at', 'created_at'];

        foreach ($dates as $date) {
            $this->assertContains($date, $this->category->getDates());
        }

        $this->assertCount(count($dates), $this->category->getDates());
    }

}
