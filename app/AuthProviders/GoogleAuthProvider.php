<?php

namespace App\AuthProviders;

use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use App\AuthProviders\AuthProviderUser;
use App\Models\AuthProvider as AuthProviderModel;

class GoogleAuthProvider extends BaseAuthProvider
{
  protected $client_id;
  protected $client_secret;
  protected $provider;

  public function __construct(AuthProviderModel $provider)
  {
    $this->client_id = $provider->provider_config->client_id;
    $this->client_secret = $provider->provider_config->client_secret;
    $this->provider = $provider;
  }

  public function redirect()
  {
    //let's check we have all the required data
    if (!$this->client_id || !$this->client_secret) {
      $this->throwMissingDataException();
    }

    // Create Google provider
    $googleProvider = Socialite::buildProvider(GoogleProvider::class, [
      'client_id' => $this->client_id,
      'client_secret' => $this->client_secret,
      'redirect' => route('social.provider.callback', ['provider' => $this->provider->id])
    ]);

    // Begin authentication flow - this will redirect the user
    return $googleProvider->redirect();
  }

  public function handleCallback(): AuthProviderUser
  {
    //let's check we have all the required data
    if (!$this->client_id || !$this->client_secret) {
      $this->throwMissingDataException();
    }

    // Create Google provider
    $googleProvider = Socialite::buildProvider(GoogleProvider::class, [
      'client_id' => $this->client_id,
      'client_secret' => $this->client_secret,
      'redirect' => route('social.provider.callback', ['provider' => $this->provider->id])
    ]);

    // Get the user information
    $user = $googleProvider->user();

    // Return the user information as an AuthProviderUser
    return new AuthProviderUser([
      'sub' => $user->id,
      'name' => $user->name,
      'email' => $user->email,
      'avatar' => $user->avatar,
      'verified' => $user->user['email_verified']
    ]);
  }

  public static function getIcon(): string
  {
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 488 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M488 261.8C488 403.3 391.1 504 248 504 110.8 504 0 393.2 0 256S110.8 8 248 8c66.8 0 123 24.5 166.3 64.9l-67.5 64.9C258.5 52.6 94.3 116.6 94.3 256c0 86.5 69.1 156.6 153.7 156.6 98.2 0 135-70.4 140.8-106.9H248v-85.3h236.1c2.3 12.7 3.9 24.9 3.9 41.4z"/></svg>';
  }

  public static function getName(): string
  {
    return 'Google';
  }

  public static function getDescription(): string
  {
    return 'Google is a popular authentication provider that allows users to sign in to your application using their Google account.';
  }
}
