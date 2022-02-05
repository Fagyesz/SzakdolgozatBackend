<?php

namespace Tests\Feature;

use App\Utils\StatusCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this->get('/');
        $response->assertJsonFragment(['name' => 'asd']);

        $response->assertStatus(StatusCode::OK);
    }
}
