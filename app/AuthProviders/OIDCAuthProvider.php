<?php

namespace App\AuthProviders;

use App\Models\AuthProvider as AuthProviderModel;
use Jumbojett\OpenIDConnectClient;
use App\AuthProviders\AuthProviderUser;


class OIDCAuthProvider extends BaseAuthProvider
{

  protected $client_id;
  protected $client_secret;
  protected $base_url;
  protected $provider;

  public function __construct(AuthProviderModel $provider)
  {
    $this->client_id = $provider->provider_config->client_id;
    $this->client_secret = $provider->provider_config->client_secret;
    $this->base_url = $provider->provider_config->base_url;
    $this->provider = $provider;
  }

  public function redirect()
  {
    //let's check we have all the required data
    if (!$this->client_id || !$this->client_secret || !$this->base_url) {
      $this->throwMissingDataException();
    }

    // Create OIDC client
    $oidc = new OpenIDConnectClient(
      $this->base_url,
      $this->client_id,
      $this->client_secret
    );

    // Set callback URL and required scopes
    $oidc->setRedirectURL(route('social.provider.callback', ['provider' => $this->provider->id]));
    $oidc->addScope(['openid', 'email', 'profile']);

    // Begin authentication flow - this will redirect the user
    $oidc->authenticate();

    // This code will only run if authentication fails to redirect
    $this->throwAuthFailureException();
  }

  public function handleCallback(): AuthProviderUser
  {
    //let's check we have all the required data
    if (!$this->client_id || !$this->client_secret || !$this->base_url) {
      $this->throwMissingDataException();
    }

    // Create OIDC client
    $oidc = new OpenIDConnectClient(
      $this->base_url,
      $this->client_id,
      $this->client_secret
    );

    // Set callback URL and required scopes
    $oidc->setRedirectURL(route('social.provider.callback', ['provider' => $this->provider->id]));
    $oidc->addScope(['openid', 'email', 'profile']);

    // Complete authentication and get user info
    $oidc->authenticate();
    $userInfo = $oidc->requestUserInfo();

    // Return the user information as an AuthProviderUser
    return new AuthProviderUser([
      'sub' => $userInfo->sub,
      'name' => $userInfo->name,
      'email' => $userInfo->email,
      'avatar' => $userInfo->picture,
      'verified' => $userInfo->email_verified,
    ]);
  }

  public static function getName(): string
  {
    return 'OpenID Connect';
  }

  public static function getDescription(): string
  {
    return 'OpenID Connect is a standard for authentication and authorization that allows users to sign in to your application using their Google, Microsoft, or other OpenID Connect-compatible accounts.';
  }
}
