<?php

namespace oidcsociolite\laraveloidc\SocialiteProviders;

use GuzzleHttp\Exception\GuzzleException;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

class OIDCProvider extends AbstractProvider
{
    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = [
        'openid', 
        'profile',
        'email',
        'offline_access',
    ];

    /**
     * The separating character for the requested scopes.
     *
     * @var string
     */
    protected $scopeSeparator = ' ';

    /**
     * Get the authentication URL for the provider.
     *
     * @param  string  $state
     * @return string
     */
    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase($this->buildUrl('/authorize'), $state);
    }

    /**
     * Get the token URL for the provider.
     *
     * @return string
     */
    protected function getTokenUrl(): string
    {
        return $this->buildUrl('/token');
    }

    /**
     * Get the raw user for the given access token.
     *
     * @param  string  $token
     * @return array
     *
     * @throws GuzzleException
     */
    protected function getUserByToken($token): array
    {
        $uri = $this->buildUrl('/userinfo');

        $response = $this->getHttpClient()->get($uri, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Map the raw user array to a Socialite User instance.
     *
     * @param  array  $user
     * @return User
     */
    protected function mapUserToObject(array $user)
    {
        dd($user);
    }

    /**
     * Get the POST fields for the token request.
     *
     * @param  string  $code
     * @return array
     */
    protected function getTokenFields($code): array
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }

    /**
     * Build the url from the specified path and base url.
     * @param  string  $path
     * @return string
     */
    protected function buildUrl(string $path): string
    {
        if (substr($path, 0) != '/') {
            $path = '/' . $path;
        }

        return config('oidc.base_url') . $path;
    }
}
