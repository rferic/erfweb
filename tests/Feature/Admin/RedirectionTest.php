<?php
/**
 * Created by PhpStorm.
 * User: ericrf
 * Date: 27/10/2018
 * Time: 13:50
 */

namespace Tests\Feature\Admin;

use App\Models\Core\Redirection;
use App\Models\Core\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RedirectionTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    protected $user;
    protected $numRedirections;
    protected $code;

    protected function setUp (): void
    {
        parent::setUp();

        app()['cache']->forget('spatie.permission.cache');

        Role::create(['name' => 'admin']);

        $this->code = $this->faker->numberBetween(1, 400);
        $this->numRedirections = $this->faker->numberBetween(1, 10);
        $this->user = factory(User::class)->create()->assignRole('admin');
        factory(User::class, $this->faker->numberBetween(1, 10))->create();
        factory(Redirection::class, $this->numRedirections)->create();
    }

    public function testViewIndex ()
    {
        $this->withExceptionHandling();

        $this
            ->actingAs($this->user)
            ->get(route('admin.redirections'))
            ->assertSuccessful();
    }

    public function testPostGetRedirections ()
    {
        $this->withExceptionHandling();

        $redirections = Redirection::all();

        $params = [
            'filters' => [
                'code' => $this->code,
                'slugs_origin' => [],
                'slugs_destine' => []
            ]
        ];

        if ( $this->faker->boolean ) {
            foreach ($redirections as $redirection) {
                if ($this->faker->boolean) {
                    $params['filters']['slugs_origin'][] = $redirection->slug_origin;
                }

                if ($this->faker->boolean) {
                    $params['filters']['slugs_destine'][] = $redirection->slug_destine;
                }
            }
        } else {
            $redirection = $redirections->random();
            $params['filters']['slug_origin'] = $redirection->slug_origin;
            $params['filters']['slug_destine'] = $redirection->slug_origin;
        }

        $response = $this
            ->actingAs($this->user)
            ->post(route('admin.redirections.get'), $params)
            ->assertSuccessful();

        $response = json_decode(json_encode($response))->baseResponse->original;
        $count = 0;

        foreach ( $redirections as $redirection ) {
            if (
                $redirection->code === $this->code &&
                ( COUNT($params['filters']['slugs_origin']) === null || in_array($redirection->slug_origin, $params['filters']['slugs_origin'])) &&
                ( COUNT($params['filters']['slugs_destine']) === null || in_array($redirection->slug_destine, $params['filters']['slugs_destine']))
            ) {
                $count++;
            }
        }

        $this->assertEquals(COUNT($response), $count);
    }

    public function testPostCreateRedirectionsWithoutParams ()
    {
        $this->withExceptionHandling();

        $params = [];

        $this
            ->actingAs($this->user)
            ->post(route('admin.redirections.create'), $params)
            ->assertStatus(400);
    }

    public function testPostCreateRedirectionWitParams ()
    {
        $this->withExceptionHandling();

        $params = [
            'code' => $this->faker->numberBetween(0, 400),
            'slug_origin' => $this->faker->slug,
            'slug_destine' => $this->faker->slug
        ];

        $response = $this
            ->actingAs($this->user)
            ->post(route('admin.redirections.create'), $params)
            ->assertSuccessful()
            ->assertJsonStructure(['result', 'redirection'])
            ->assertJsonFragment(['result' => true]);

        $response = json_decode(json_encode($response))->baseResponse->original;

        $this->assertEquals($response->redirection->code, $params['code']);
        $this->assertEquals($response->redirection->slug_origin, $params['slug_origin']);
        $this->assertEquals($response->redirection->slug_destine, $params['slug_destine']);
    }

    public function testDeleteDestroyRedirection ()
    {
        $this->withExceptionHandling();

        $redirection = Redirection::all()->random();

        $this
            ->actingAs($this->user)
            ->delete(route('admin.redirections.destroy', $redirection->id))
            ->assertSuccessful()
            ->assertExactJson(['result' => true]);

        $redirection = Redirection::find($redirection->id);
        $this->assertNull($redirection);
    }
}
