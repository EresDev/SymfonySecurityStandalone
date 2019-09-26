<?php

namespace Tests;

use App\JWTTokenChecker;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\User;

class JWTTokenCheckerTest extends WebTestCase
{
    public function testValidUsernamePassword() : void
    {
        $token = JWTTokenChecker::getJWTTokenManager()->create(
            new User('foo', 'bar')
        );

        $request = Request::create(
            '/'
        );
        $request->headers->set('Authorization', $token);

        $kernel = new JWTTokenChecker();

        $response = $kernel->handle($request);

        $this->assertInstanceOf(JWTUserToken::class, $response);
    }
}
