<?php

namespace Tests;

use App\JWTTokenGenerator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class JWTTokenGeneratorTest extends WebTestCase
{
    public function testValidUsernamePassword() : void
    {
        $request = Request::create('/', 'POST', ['username' => 'foo', 'password' => 'bar']);

        $kernel = new JWTTokenGenerator();

        $response = $kernel->handle($request);

        $responseArray = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('token', $responseArray);
    }

    public function testInvalidUsernamePassword() : void
    {
        $this->expectException(BadCredentialsException::class);

        $request = Request::create('/', 'POST', ['username' => 'invalid', 'password' => 'bar']);

        $kernel = new JWTTokenGenerator();

        $kernel->handle($request);
    }
}
