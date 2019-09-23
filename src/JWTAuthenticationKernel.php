<?php

namespace App;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\LcobucciJWTEncoder;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWSProvider\LcobucciJWSProvider;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\KeyLoader\RawKeyLoader;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;

class JWTAuthenticationKernel implements HttpKernelInterface
{
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $jwtTokenManager = self::getJWTTokenManager();



        $tokenExtractor = new AuthorizationHeaderTokenExtractor(
            '',
            'Authorization'
        );

        //Guard implementation
        $guardAuthenticator = new JWTTokenAuthenticator(
            $jwtTokenManager,
            new EventDispatcher(),
            $tokenExtractor
        );

        $preAuthenticationJWTUserToken = $guardAuthenticator->getCredentials($request);

        if($preAuthenticationJWTUserToken) { // we found token in request
            $userProvider = new InMemoryUserProvider([
                'foo' => [
                    'password' => 'bar',
                    'roles' => ['ROLE_USER']
                ]
            ]);

            $user = $guardAuthenticator->getUser(
                $preAuthenticationJWTUserToken,
                $userProvider
            );


            //$guardAuthenticator->start($request, null);

            return $guardAuthenticator->createAuthenticatedToken(
                $user,
                'default'
            );
        }else{
            return 'Unable to find Auth Token in request.';
        }

    }

    public static function getJWTTokenManager() : JWTTokenManagerInterface
    {
        //able to create and load JSON web signatures
        $lcobucciJWSProvider = new LcobucciJWSProvider(
            new RawKeyLoader(
                __DIR__.'config/jwt/private.pem',
                __DIR__.'config/jwt/public.pem',
                'kjhsr8934hwrf9uu324hf923'
            ), //Reads crypto keys.
            'openssl',
            'HS256',
            '3600',
            '3600'
        );

        //Json Web Token encoder/decoder based on the lcobucci/jwt library.
        $jwtEncoder = new LcobucciJWTEncoder(
            $lcobucciJWSProvider
        );

        $eventDispatcher = new EventDispatcher();

        //Provides convenient methods to manage JWT creation/verification.
        $jwtTokenManager = new JWTManager(
            $jwtEncoder,
            $eventDispatcher,

            'username'
        );

        return $jwtTokenManager;
    }
}
