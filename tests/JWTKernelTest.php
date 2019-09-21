<?php

namespace Tests;

use App\JWTKernel;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\User;

class JWTKernelTest extends WebTestCase
{
    public function testValidUsernamePassword() : void
    {
        $token = JWTKernel::getJWTTokenManager()->create(
            new User('foo', 'bar')
        );

        $request = Request::create(
            '/'
        );
        $request->headers->set('Authorization', $token);

        $kernel = new JWTKernel();

        $response = $kernel->handle($request);

        $this->assertInstanceOf(JWTUserToken::class, $response);
    }

//    public function testInvalidUsernamePassword() : void
//    {
//        $this->expectException(BadCredentialsException::class);
//
//        $request = Request::create('/', 'POST', ['username' => 'invalid', 'password' => 'bar']);
//
//        $kernel = new Kernel();
//
//        $response = $kernel->handle($request);
//    }
}
