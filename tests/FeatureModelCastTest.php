<?php

namespace Spatie\Enum\Laravel\Tests;

use Spatie\Enum\Laravel\Exceptions\NotNullableEnumField;
use Spatie\Enum\Laravel\Tests\Extra\Post;
use Spatie\Enum\Laravel\Tests\Extra\StatusEnum;
use stdClass;
use TypeError;

final class FeatureModelCastTest extends TestCase
{
    /** @test */
    public function it_saves_the_value_of_an_enum()
    {
        $model = Post::create([
            'status' => StatusEnum::draft(),
        ]);

        $model->refresh();

        $this->assertTrue($model->status->equals(StatusEnum::draft()));
        $this->assertEquals('draft', $model->getOriginal('status'));
    }

    /** @test */
    public function an_invalid_value_throws_an_error()
    {
        $this->expectException(TypeError::class);

        Post::create([
            'status' => new stdClass(),
        ]);
    }

    /** @test */
    public function it_saves_a_null_nullable_enum()
    {
        $model = Post::create([
            'status' => StatusEnum::draft(),
            'nullable_enum' => null,
        ]);

        $model->refresh();

        $this->assertTrue($model->status->equals(StatusEnum::draft()));
        $this->assertEquals('draft', $model->getOriginal('status'));
        $this->assertNull($model->nullable_enum);
    }

    /** @test */
    public function it_saves_an_enum_of_nullable_enum()
    {
        $model = Post::create([
            'status' => StatusEnum::draft(),
            'nullable_enum' => StatusEnum::draft(),
        ]);

        $model->refresh();

        $this->assertTrue($model->status->equals(StatusEnum::draft()));
        $this->assertEquals('draft', $model->getOriginal('status'));
        $this->assertTrue($model->nullable_enum->equals(StatusEnum::draft()));
        $this->assertEquals('draft', $model->getOriginal('nullable_enum'));
    }

    /** @test */
    public function it_saves_an_enum_of_array_of_enums()
    {
        $model = Post::create([
            'status' => StatusEnum::draft(),
            'array_of_enums' => [StatusEnum::draft(), StatusEnum::archived()],
        ]);

        $model->refresh();

        $this->assertTrue($model->status->equals(StatusEnum::draft()));
        $this->assertEquals('draft', $model->getOriginal('status'));
        $this->assertIsArray($model->array_of_enums);
        $this->assertCount(2, $model->array_of_enums);
        $this->assertTrue(StatusEnum::draft()->equals(...$model->array_of_enums));
        $this->assertTrue(StatusEnum::archived()->equals(...$model->array_of_enums));
    }

    /** @test */
    public function it_saves_a_null_value_for_nullable_array_of_enums()
    {
        $model = Post::create([
            'status' => StatusEnum::draft(),
            'nullable_array_of_enums' => null,
        ]);

        $model->refresh();

        $this->assertTrue($model->status->equals(StatusEnum::draft()));
        $this->assertEquals('draft', $model->getOriginal('status'));
        $this->assertNull($model->nullable_array_of_enums);
    }

    /** @test */
    public function it_saves_a_null_value_for_nullable_set_of_enums()
    {
        $model = Post::create([
            'status' => StatusEnum::draft(),
            'nullable_set_of_enums' => null,
        ]);

        $model->refresh();

        $this->assertTrue($model->status->equals(StatusEnum::draft()));
        $this->assertEquals('draft', $model->getOriginal('status'));
        $this->assertNull($model->nullable_set_of_enums);
    }

    /** @test */
    public function it_saves_an_empty_array_for_nullable_set_of_enums()
    {
        $model = Post::create([
            'status' => StatusEnum::draft(),
            'nullable_set_of_enums' => [],
        ]);

        $model->refresh();

        $this->assertTrue($model->status->equals(StatusEnum::draft()));
        $this->assertEquals('draft', $model->getOriginal('status'));
        $this->assertNull($model->nullable_set_of_enums);
    }

    /** @test */
    public function it_tries_to_save_an_empty_array_for_not_nullable_set_of_enums()
    {
        $this->expectException(NotNullableEnumField::class);

        $model = Post::create([
            'set_of_enums' => [],
        ]);

        $model->refresh();
    }

    /** @test */
    public function it_saves_an_enum_of_set_of_enums()
    {
        $model = Post::create([
            'status' => StatusEnum::draft(),
            'set_of_enums' => [StatusEnum::draft(), StatusEnum::archived()],
        ]);

        $model->refresh();

        $this->assertTrue($model->status->equals(StatusEnum::draft()));
        $this->assertEquals('draft', $model->getRawOriginal('status'));
        $this->assertEquals('draft,stored archive', $model->getRawOriginal('set_of_enums'));
        $this->assertIsArray($model->set_of_enums);
        $this->assertCount(2, $model->set_of_enums);
        $this->assertTrue(StatusEnum::draft()->equals(...$model->set_of_enums));
        $this->assertTrue(StatusEnum::archived()->equals(...$model->set_of_enums));
    }

    /** @test */
    public function it_can_cast_enum_to_json(): void
    {
        $model = Post::create([
            'status' => StatusEnum::draft(),
        ]);

        $model->refresh();

        $this->assertSame('"draft"', $model->status->toJson());
    }
}
