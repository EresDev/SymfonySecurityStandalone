<?php

namespace Tests;

use App\SimpleAuthentication;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class SimpleAuthenticationTest extends WebTestCase
{
    public function testValidUsernamePassword() : void
    {
        $request = Request::create('/', 'POST', ['username' => 'foo', 'password' => 'bar']);

        $kernel = new SimpleAuthentication();

        $response = $kernel->handle($request);

        $this->assertEquals('Greeting foo', $response);
    }

    public function testInvalidUsernamePassword() : void
    {
        $this->expectException(BadCredentialsException::class);

        $request = Request::create('/', 'POST', ['username' => 'invalid', 'password' => 'bar']);

        $kernel = new SimpleAuthentication();

        $response = $kernel->handle($request);
    }
}
