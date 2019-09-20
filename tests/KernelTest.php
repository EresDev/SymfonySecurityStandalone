<?php

namespace Tests;

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class KernelTest extends WebTestCase
{
    public function testValidUsernamePassword() : void
    {
        $request = Request::create('/', 'POST', ['username' => 'foo', 'password' => 'bar']);

        $kernel = new Kernel();

        $response = $kernel->handle($request);

        $this->assertEquals('Greeting foo', $response);
    }

    public function testInvalidUsernamePassword() : void
    {
        $this->expectException(BadCredentialsException::class);

        $request = Request::create('/', 'POST', ['username' => 'invalid', 'password' => 'bar']);

        $kernel = new Kernel();

        $response = $kernel->handle($request);
    }
}
