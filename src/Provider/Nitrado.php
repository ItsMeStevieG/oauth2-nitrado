<?php

declare(strict_types=1);

namespace ItsMeStevieG\OAuth2\Client\Provider;

use ItsMeStevieG\OAuth2\Client\Provider\Exception\NitradoIdentityProviderException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class Nitrado extends AbstractProvider
{
    use BearerAuthorizationTrait;

    public $scope;
    public const BASE_NITRADO_URL = 'https://oauth.nitrado.net/oauth/v2/';
    public const RESPONSE_TYPE = 'code';

    // Available scopes.

    // Access to a user's basic account information (username, email address, ...)
    public const SCOPE_USER_INFO = 'user_info';

    // Access to a customers's rented services.
    public const SCOPE_SERVICE = 'service';

    // Access to billing-related endpoints.
    public const SCOPE_SERVICE_ORDER = 'service_order';

    // Access to user's ssh public keys.
    public const SCOPE_SSH_KEYS = 'ssh_keys';

    public function __construct(array $options = [], array $collaborators = [])
    {
        if (!isset($options['responseType']) || $options['responseType'] !== self::RESPONSE_TYPE) {
            $options['responseType'] = self::RESPONSE_TYPE;
        }

        parent::__construct($options, $collaborators);
    }

    /**
     * Returns the base URL for authorizing a client.
     */
    public function getBaseAuthorizationUrl(): string
    {
        return self::BASE_NITRADO_URL . 'auth';
    }

    /**
     * Returns the base URL for requesting an access token.
     */
    public function getBaseAccessTokenUrl(array $params): string
    {
        return self::BASE_NITRADO_URL . 'token';
    }

    /**
     * Returns the URL for requesting the resource owner's details.
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return 'https://api.nitrado.net/user';
    }

    /**
     * Returns the default scopes used by this provider.
     *
     * This should only be the scopes that are required to request the details
     * of the resource owner, rather than all the available scopes.
     */
    protected function getDefaultScopes(): array
    {
        return [SCOPE_USER_INFO];
    }

    protected function getAuthorizationParameters(array $options)
    {
        if (\is_array($this->scope)) {
            $separator = $this->getScopeSeparator();
            $this->scope = implode($separator, $this->scope);
        }

        $params = array_merge(
            parent::getAuthorizationParameters($options),
            array_filter([
                'scope' => $this->scope,
            ])
        );

        return $params;
    }

    /**
     * Sets to the Scope Separator to Space instead of comma.
     */
    protected function getScopeSeparator()
    {
        return ' ';
    }

    /**
     * Checks a provider response for errors.
     *
     * @param array|string $data Parsed response data
     *
     * @throws IdentityProviderException
     */
    protected function checkResponse(ResponseInterface $response, $data): void
    {
        if ($response->getStatusCode() >= 400) {
            $error = $data['error_description'] ?? $data['error'] ?? $response->getReasonPhrase();
            $statusCode = $response->getStatusCode();

            if (\is_array($data['error'])) {
                $error = $data['error']['message'];
                $statusCode = $data['error']['status'];
            }

            throw new NitradoIdentityProviderException($error, $statusCode, $response);
        }
    }

    /**
     * Generates a resource owner object from a successful resource owner
     * details request.
     */
    protected function createResourceOwner(array $response, AccessToken $token): ResourceOwnerInterface
    {
        return new NitradoResourceOwner($response);
    }
}
