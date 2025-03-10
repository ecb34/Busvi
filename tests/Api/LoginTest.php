<?php

namespace Tests\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\User;

class LoginTest extends TestCase {
  use DatabaseTransactions;

  /** @test */
  public function test_login_correcto(){

    $user = factory(User::class)->create(['password' => bcrypt('123123'), 'role' => 'user']);

    $params = [
      'lang' => 'es',
      'username' => $user->username,
      'password' => '123123',
      'push_token' => str_random(60),
    ];

    $response = $this->json('POST', '/api/login', $params);

    $response
      ->assertStatus(200)
      ->assertJsonStructure(['username', 'token', 'rol'])
      ->assertJson([
        'username' => $user->username,
        'token' => $user->api_token,
        'rol' => 'user',
      ]);

  }

  /** @test */
  public function test_login_incorrecto(){

    $user = factory(User::class)->create(['password' => bcrypt('123123'), 'role' => 'user']);

    $params = [
      'lang' => 'es',
      'username' => $user->username,
      'password' => '123',
      'push_token' => str_random(60),
    ];

    $response = $this->json('POST', '/api/login', $params);

    $response
      ->assertStatus(500)
      ->assertJsonStructure(['msg']);

  }

}
