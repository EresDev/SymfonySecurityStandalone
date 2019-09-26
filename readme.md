# Symfony Security without Framwork

In this project, I use Symfony Security component without Symfony framework. 
The purpose of this to understand internals of Symfony Security. 

- `SimpleAuthentication`

It uses Symfony Security only.

- `JWTTokenGenerator` and `JTWTokenChecker`

These classes use `lexik/jwt-authentication-bundle` for JWT Tokens.

All above classes work independently.

