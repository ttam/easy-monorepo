<?php

declare(strict_types=1);

namespace EonX\EasyApiToken\Tests;

use EonX\EasyApiToken\External\Interfaces\JwtDriverInterface;
use EonX\EasyApiToken\Interfaces\Tokens\Factories\JwtEasyApiTokenFactoryInterface;
use EonX\EasyApiToken\Tokens\Factories\JwtEasyApiTokenFactory;
use Nette\Utils\Strings;

abstract class AbstractJwtTokenTestCase extends AbstractTestCase
{
    protected function createJwtEasyApiTokenFactory(JwtDriverInterface $jwtDriver): JwtEasyApiTokenFactoryInterface
    {
        return new JwtEasyApiTokenFactory($jwtDriver);
    }

    /**
     * @return false|resource
     */
    protected function getOpenSslPrivateKey()
    {
        return \openssl_pkey_get_private(\sprintf('file://%s', __DIR__ . '/keys/jwt-private.pem'));
    }

    /**
     * @return false|resource
     */
    protected function getOpenSslPublicKey()
    {
        return \openssl_pkey_get_public(\sprintf('file://%s', __DIR__ . '/keys/jwt-public.pem'));
    }

    protected function isAlgoRs(string $algo): bool
    {
        return Strings::startsWith(\strtolower($algo), 'rs');
    }
}
