<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuthProvider;
class AuthProvidersController extends Controller
{

  //admin-only index with all information returned
  public function index()
  {
    $authProviders = AuthProvider::all();

    $authProviders = $authProviders->map(function ($authProvider) {
      $icon = $authProvider->provider_class::getIcon();
      return [
        'id' => $authProvider->id,
        'name' => $authProvider->name,
        'provider_name' => $authProvider->provider_class::getName(),
        'provider_description' => $authProvider->provider_class::getDescription(),
        'enabled' => $authProvider->enabled == "true",
        'class' => $authProvider->provider_class,
        'provider_config' => $authProvider->provider_config,
        'icon' => $icon
      ];
    });

    return response()->json(
      [
        'status' => 'success',
        'message' => 'Auth providers fetched successfully',
        'data' => [
          'authProviders' => $authProviders
        ]
      ]
    );
  }

  //admin-only create
  public function create(Request $request)
  {
    $authProvider = AuthProvider::create($request->all());
    return response()->json(
      [
        'status' => 'success',
        'message' => 'Auth provider created successfully',
        'data' => [
          'authProvider' => $authProvider
        ]
      ]
    );
  }

  //public list of auth providers with only name, icon, and ID
  public function list()
  {
    $authProviders = AuthProvider::all();

    $authProviders = $authProviders->map(function ($authProvider) {
      $icon = $authProvider->provider_class::getIcon();
      return [
        'id' => $authProvider->id,
        'name' => $authProvider->name,
        'icon' => $icon
      ];
    });

    return response()->json(
      [
        'status' => 'success',
        'message' => 'Auth providers fetched successfully',
        'data' => [
          'authProviders' => $authProviders
        ]
      ]
    );
  }

}
