<?php

namespace Tests;

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class KernelTest extends WebTestCase
{
    public function testValidUsernamePassword() : void
    {
        $request = Request::create('/', 'GET', ['username' => 'foo', 'password' => 'bar']);

        $kernel = new Kernel();

        $response = $kernel->handle($request);

        $this->assertEquals('Greeting foo', $response->getContent());
    }
}
