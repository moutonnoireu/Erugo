<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use App\AuthProviders\AuthProviderUser;
use Illuminate\Support\Facades\Auth;
use App\Services\SettingsService;
use App\Models\AuthProvider;
use Jumbojett\OpenIDConnectClient;

/**
 * Controller for handling social authentication methods
 * Supports Google OAuth and OpenID Connect (OIDC)
 */
class ExternalAuthController extends Controller
{
    /**
     * Settings service for retrieving OAuth configuration
     *
     * @var SettingsService
     */
    private $settings;

    private function getProviderClass($class)
    {
        return "App\\AuthProviders\\" . $class . "AuthProvider";
    }


    /**
     * Initialize the controller
     */
    public function __construct()
    {
        // Force HTTPS for all redirect URLs for security
        URL::forceScheme('https');

        // Inject the settings service
        $this->settings = app()->make(SettingsService::class);
    }

    /**
     * Redirect the user to the authentication provider's login page.
     *
     * @param int $providerId The ID of the authentication provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function providerLogin($providerId)
    {
        // Retrieve the authentication provider by ID
        $provider = AuthProvider::where('id', $providerId)->first();

        // If the provider is not found, redirect to the home page with an error message
        if (!$provider) {
            return redirect('/')->with('error', 'Provider not found');
        }

        // Instantiate the provider class and redirect to the provider's login page
        $class = $this->getProviderClass($provider->provider_class);
        $providerClass = new $class($provider);
        return $providerClass->redirect();
    }

    /**
     * Handle the callback from the authentication provider.
     *
     * @param int $providerId The ID of the authentication provider
     * @return \Illuminate\Http\JsonResponse
     */
    public function providerCallback($providerUuid)
    {
        // Retrieve the authentication provider by ID
        $provider = AuthProvider::where('uuid', $providerUuid)->first();

        // If the provider is not found, redirect to the home page with an error message
        if (!$provider) {
            return redirect('/')->with('error', 'Provider not found');
        }

        // Instantiate the provider class and handle the callback to get the user information
        $class = $this->getProviderClass($provider->provider_class);
        $providerClass = new $class($provider);
        $user = $providerClass->handleCallback();

        // Return the user information as a JSON response
        return $this->respondWithToken($user);
    }

    /**
     * Creates JWT tokens and returns response with refresh token cookie
     *
     * @param User $user The authenticated user
     * @return \Illuminate\Http\RedirectResponse
     */
    private function respondWithToken(AuthProviderUser $user)
    {

        // swap the AuthProviderUser for a User
        $user = User::where('email', $user->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 401);
        }

        // Create JWT token
        $token = Auth::login($user);

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        // Set refresh token with 24 hour expiry
        $twentyFourHours = 60 * 60 * 24;
        $refreshToken = Auth::setTTL($twentyFourHours)->tokenById($user->id);

        // Create HTTP-only secure cookie with refresh token
        $cookie = cookie('refresh_token', urlencode($refreshToken), $twentyFourHours, null, null, true, true);

        // Redirect to home with the cookie
        return redirect('/')->withCookie($cookie);
    }
}
