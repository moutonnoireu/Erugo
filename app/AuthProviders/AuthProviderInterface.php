<?php

namespace App\AuthProviders;
use App\Models\AuthProvider as AuthProviderModel;
use App\AuthProviders\AuthProviderUser;

interface AuthProviderInterface
{
    public function __construct(AuthProviderModel $provider);

    public function redirect();

    public function handleCallback(): AuthProviderUser;

    public static function getIcon(): string;

    public static function getName(): string;

    public static function getDescription(): string;
}

