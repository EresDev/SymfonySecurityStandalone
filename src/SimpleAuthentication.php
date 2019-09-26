<?php

namespace App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserChecker;

class SimpleAuthentication implements HttpKernelInterface
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

        $authManager = new AuthenticationProviderManager([
            new DaoAuthenticationProvider(
                $userProvider,
                new UserChecker(),
                'default',
                $encoderFactory
            )
        ]);

        $authToken = $authManager->authenticate($token);

        return 'Greeting '. $authToken->getUsername();
    }
}
