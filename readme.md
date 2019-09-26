# Symfony Security without Framwork

In this project, I use Symfony Security component without its framework. 
The purpose of doing this to understand internals of Symfony Security. 

- `SimpleAuthentication`
It use only Symfony Security.

- `JWTTokenGenerator` and `JTWTokenChecker`
These classes use `lexik/jwt-authentication-bundle` for JWT Tokens.

All above classes work independently.

