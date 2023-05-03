<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\RandomUserService;
use App\Repositories\RandomUserRepository;

class RandomUserControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_a_random_user()
    {
        $repository = new RandomUserRepository;
        $service = new RandomUserService($repository);

        $response = $this->get('/random-user');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'username',
            'email',
            'address' => [
                'street',
                'suite',
                'city',
                'zipcode',
                'geo' => [
                    'lat',
                    'lng'
                ]
            ],
            'phone',
            'website',
            'company' => [
                'name',
                'catchphrase',
                'bs'
            ],
        ]);
        $this->assertContains($response->json(), $service->getTestUser());
    }
}
