<?php

namespace Tests\Feature\Front;

use App\Http\Controllers\Front\AppController;
use App\Http\Helpers\AppHelper;
use App\Models\Core\App;
use App\Models\Core\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class Apptest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected function setUp (): void
    {
        parent::setUp();

        $this->seedRoles();

        factory(App::class, $this->faker->numberBetween(1, 100))->create();
    }

    public function testPostGetBadRequests ()
    {
        $this->withExceptionHandling();
        // Test without params
        $params = [];

        $this
            ->post(route('get-apps'), $params)
            ->assertStatus(400);
        // Test with params but format attribute not pass validator
        $params = [
            'format' => $this->faker->word,
            'randomOrder' => $this->faker->boolean,
            'maxItems' => $this->faker->numberBetween(1, 100),
            'perPage' => $this->faker->numberBetween(1, 100)
        ];

        $this
            ->post(route('get-apps'), $params)
            ->assertStatus(400);
        // Test with params but randomOrder attribute not pass validator
        $params = [
            'format' => 'collection',
            'randomOrder' => '',
            'maxItems' => $this->faker->numberBetween(1, 100),
            'perPage' => $this->faker->numberBetween(1, 100)
        ];

        $this
            ->post(route('get-apps'), $params)
            ->assertStatus(400);
        // Test with params but maxItems attribute not pass validator
        $params = [
            'format' => 'collection',
            'randomOrder' => $this->faker->boolean,
            'maxItems' => '',
            'perPage' => $this->faker->numberBetween(1, 100)
        ];

        $this
            ->post(route('get-apps'), $params)
            ->assertStatus(400);
        // Test with params but perPage attribute not pass validator
        $params = [
            'format' => 'pagination',
            'randomOrder' => $this->faker->boolean,
            'maxItems' => $this->faker->numberBetween(1, 100),
            'perPage' => ''
        ];

        $this
            ->post(route('get-apps'), $params)
            ->assertStatus(400);
    }

    public function testPostGetSuccessful ()
    {
        $this->withExceptionHandling();

        // Test format collection
        $params = [
            'format' => 'collection',
            'randomOrder' => $this->faker->boolean,
            'maxItems' => $this->faker->numberBetween(1, 100),
            'status' => $this->getRandomFilterStatus(),
            'types' => $this->getRandomFilterTypes()
        ];

        $this
            ->post(route('get-apps'), $params)
            ->assertSuccessful();
        // Test format pagination
        $params = [
            'format' => 'pagination',
            'randomOrder' => $this->faker->boolean,
            'perPage' => $this->faker->numberBetween(1, 100),
            'status' => $this->getRandomFilterStatus(),
            'types' => $this->getRandomFilterTypes()
        ];

        $this
            ->post(route('get-apps'), $params)
            ->assertSuccessful()
            ->assertJsonStructure([
                'current_page',
                'data',
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total'
            ]);
    }

    private function getRandomFilterStatus ()
    {
        $status = AppHelper::getStatus();
        $filter = [];

        foreach ( $status AS $item ) {
            if ( $this->faker->boolean ) {
                $filter[] = $item['key'];
            }
        }

        return $filter;
    }

    private function getRandomFilterTypes ()
    {
        $types = AppHelper::getTypes();
        $filter = [];

        foreach ( $types AS $item ) {
            if ( $this->faker->boolean ) {
                $filter[] = $item['key'];
            }
        }

        return $filter;
    }
}
