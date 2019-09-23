<?php

namespace Tests;

use App\JWTAuthenticationKernel;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\User;

class JWTAuthenticationKernelTest extends WebTestCase
{
    public function testValidUsernamePassword() : void
    {
        $token = JWTAuthenticationKernel::getJWTTokenManager()->create(
            new User('foo', 'bar')
        );

        $request = Request::create(
            '/'
        );
        $request->headers->set('Authorization', $token);

        $kernel = new JWTAuthenticationKernel();

        $response = $kernel->handle($request);

        $this->assertInstanceOf(JWTUserToken::class, $response);
    }
}
