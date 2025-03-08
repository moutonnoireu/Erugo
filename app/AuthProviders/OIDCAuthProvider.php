<?php

namespace App\AuthProviders;

use App\Models\AuthProvider as AuthProviderModel;
use Jumbojett\OpenIDConnectClient;
use App\AuthProviders\AuthProviderUser;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Validator;


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

  private function createClient()
  {

    //let's check we have all the required data
    if (!$this->client_id || !$this->client_secret || !$this->base_url) {
      $this->throwMissingDataException();
    }

    $client =  new OpenIDConnectClient(
      $this->base_url,
      $this->client_id,
      $this->client_secret
    );
    // Set callback URL and required scopes
    $client->setRedirectURL(route('social.provider.callback', ['provider' => $this->provider->uuid]));
    $client->addScope(['openid', 'email', 'profile']);
    return $client;
  }
  public function redirect()
  {
    // Create OIDC client
    $oidc =  $this->createClient();

    // Begin authentication flow - this will redirect the user
    $oidc->authenticate();

    // This code will only run if authentication fails to redirect
    $this->throwAuthFailureException();
  }

  public function handleCallback(): AuthProviderUser
  {
    // Create OIDC client
    $oidc = $this->createClient();

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

  public static function getIcon(): string
  {
    return '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 120 120"><path d="m 75.180374,15.11293 -15.99577,7.797938 0,79.945522 C 40.931432,100.568 27.193065,90.619126 27.193065,78.662788 c 0,-11.334002 12.358733,-20.879977 29.192279,-23.793706 l 0,-10.163979 C 30.637155,47.81728 11.197296,61.839238 11.197296,78.662788 c 0,17.429891 20.856984,31.825422 47.987308,34.224282 l 15.99577,-7.53134 0,-90.2428 z m 2.79926,29.592173 0,10.163979 c 6.261409,1.083679 11.913385,3.061436 16.528961,5.731817 l -8.664375,4.898704 30.95849,6.731553 -2.23275,-22.927269 -8.23115,4.632108 C 98.692362,49.310409 88.899095,46.024898 77.979634,44.705103 z" /></svg>';
  }

  public static function getName(): string
  {
    return 'OpenID Connect';
  }

  public static function getDescription(): string
  {
    return 'OpenID Connect is a standard for authentication and authorization that allows users to sign in to your application using their Google, Microsoft, or other OpenID Connect-compatible accounts.';
  }

  public static function getValidator(array $data): Validator
  {
    return ValidatorFacade::make($data, [
      'client_id' => ['required', 'string'],
      'client_secret' => ['required', 'string'],
      'base_url' => ['required', 'url'],
    ]);
  }

  public static function getInformationUrl(): ?string
  {
    return 'https://openid.net/connect/';
  }

  public static function getEmptyProviderConfig(): array
  {
    return [
      'client_id' => '',
      'client_secret' => '',
      'base_url' => '',
    ];
  }
}
