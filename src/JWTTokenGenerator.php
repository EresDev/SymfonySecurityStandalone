<?php

namespace App;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\LcobucciJWTEncoder;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationFailureHandler;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWSProvider\LcobucciJWSProvider;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\KeyLoader\RawKeyLoader;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserChecker;

class JWTTokenGenerator implements HttpKernelInterface
{
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {

        $token = new UsernamePasswordToken(
            $request->request->get('username'),
            $request->request->get('password'),
            'default'
        );

        $userProvider = new InMemoryUserProvider([
           'foo' => [
               'password' => 'bar',
               'roles' => ['ROLE_USER']
           ]
        ]);

        $encoderFactory = new EncoderFactory([
            User::class => new PlaintextPasswordEncoder()
        ]);

        $authenticationProvider = new DaoAuthenticationProvider(
            $userProvider,
            new UserChecker(),
            'default',
            $encoderFactory
        );


        $eventDispatcher = new EventDispatcher();

        $authManager = new AuthenticationProviderManager([$authenticationProvider]);

        $authToken = $authManager->authenticate($token);

        if ($authToken->isAuthenticated()) {
            $authSuccessHandler = new AuthenticationSuccessHandler(
                new JWTManager(
                    new LcobucciJWTEncoder(
                        new LcobucciJWSProvider(
                            new RawKeyLoader(
                                __DIR__.'config/jwt/private.pem',
                                __DIR__.'config/jwt/public.pem',
                                'kjhsr8934hwrf9uu324hf923'
                            ),
                            'openssl',
                            'HS256',
                            3600,
                            3600
                        )
                    ),
                    $eventDispatcher,
                    'username'
                ),
                $eventDispatcher
            );
            $response = $authSuccessHandler->onAuthenticationSuccess($request, $authToken);
        } else {
            $authFailureHandler = new AuthenticationFailureHandler(
              $eventDispatcher
            );
            $response = $authFailureHandler->onAuthenticationFailure(
                $request,
                new AuthenticationException('Invalid login credentials!')
            );
        }


        return $response;
    }
}
