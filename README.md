<div align="center">
    <a href="https://github.com/itsmestevieg/oauth2-nitrado/actions" title="Build">
        <img src="https://img.shields.io/github/workflow/status/itsmestevieg/oauth2-nitrado/ci?style=for-the-badge" alt="Build">
    </a>
    <a href="https://scrutinizer-ci.com/g/itsmestevieg/oauth2-nitrado/" title="Coverage">
        <img src="https://img.shields.io/codecov/c/gh/itsmestevieg/oauth2-nitrado?style=for-the-badge" alt="Coverage">
    </a>
    <a href="https://php.net" title="PHP Version">
        <img src="https://img.shields.io/badge/php-%3E%3D%207.3-8892BF.svg?style=for-the-badge" alt="PHP Version">
    </a>
    <a href="https://packagist.org/packages/itsmestevieg/oauth2-nitrado" title="Downloads">
        <img src="https://img.shields.io/packagist/dt/itsmestevieg/oauth2-nitrado.svg?style=for-the-badge" alt="Downloads">
    </a>
    <a href="https://packagist.org/packages/itsmestevieg/oauth2-nitrado" title="Latest Stable Version">
        <img src="https://img.shields.io/packagist/v/itsmestevieg/oauth2-nitrado.svg?style=for-the-badge" alt="Latest Stable Version">
    </a>
    <a href="https://packagist.org/packages/itsmestevieg/oauth2-nitrado" title="License">
        <img src="https://img.shields.io/packagist/l/itsmestevieg/oauth2-nitrado.svg?style=for-the-badge" alt="License">
    </a>
</div>

# Nitrado Provider for OAuth 2.0 Client

This package provides Nitrado OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

You can install this package using Composer:

```
composer require itsmestevieg/oauth2-nitrado
```

You will then need to:

- run `composer install` to get these dependencies added to your vendor directory
- add the autoloader to your application with this line: `require('vendor/autoload.php');`

## Usage

Usage is the same as The League's OAuth client, using `\ItsMeStevieG\OAuth2\Client\Provider\Nitrado` as the provider.

### Authorization Code Flow

```php
$provider = new ItsMeStevieG\OAuth2\Client\Provider\Nitrado([
    'clientId'     => '{nitrado-client-id}',
    'clientSecret' => '{nitrado-client-secret}',
    'redirectUri'  => 'https://example.com/callback-url',
]);

if (!isset($_GET['code'])) {
    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl([
        'scope' => [
            ItsMeStevieG\OAuth2\Client\Provider\Nitrado::SCOPE_USER_INFO,
        ]
    ]);

    $_SESSION['oauth2state'] = $provider->getState();

    header('Location: ' . $authUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    echo 'Invalid state.';
    exit;

}

// Try to get an access token (using the authorization code grant)
$token = $provider->getAccessToken('authorization_code', [
    'code' => $_GET['code']
]);

// Optional: Now you have a token you can look up a users profile data
try {

    // We got an access token, let's now get the user's details
    /** @var \ItsMeStevieG\OAuth2\Client\Provider\NitradoResourceOwner $user */
    $user = $provider->getResourceOwner($token);

    // Use these details to create a new profile
    printf('Hello %s!', $user->getUserName());

    echo '<pre>';
    var_dump($user);
    echo '</pre>';

} catch (Exception $e) {

    // Failed to get user details
    exit('Damned...');
}

echo '<pre>';
// Use this to interact with an API on the users behalf
var_dump($token->getToken());
# string(217) "CAADAppfn3msBAI7tZBLWg...

// The time (in epoch time) when an access token will expire
var_dump($token->getExpires());
# int(1436825866)
echo '</pre>';
```

### Authorization Scopes

All scopes described in the [official documentation](https://doc.nitrado.net/#api-OAuth_2-CreateAuthToken) are available through public constants in `\ItsMeStevieG\OAuth2\Client\Provider\Nitrado`:

- SCOPE_USER_INFO
- SCOPE_SERVICE
- SCOPE_SERVICE_ORDER
- SCOPE_SSH_KEYS

## Contributing

Please see [CONTRIBUTING](https://github.com/itsmestevieg/oauth2-nitrado/blob/master/CONTRIBUTING.md) for details.

## Credits

- [Steven Graham](https://github.com/ItsMeStevieG)

## License

The MIT License (MIT). Please see [License File](https://github.com/itsmestevieg/oauth2-nitrado/blob/master/LICENSE) for more information.
