<?php
declare(strict_types=1);

namespace LoyaltyCorp\EasyIdentity\Tests\Implementations\Auth0;

use LoyaltyCorp\EasyIdentity\Implementations\Auth0\AuthenticationApiClientFactory;
use LoyaltyCorp\EasyIdentity\Implementations\Auth0\Config;
use LoyaltyCorp\EasyIdentity\Tests\AbstractTestCase;

/**
 * @covers \LoyaltyCorp\EasyIdentity\Implementations\Auth0\AuthenticationApiClientFactory
 */
class AuthenticationApiClientFactoryTest extends AbstractTestCase
{
    /**
     * Factory should return the expected authentication api client instance.
     *
     * @return void
     *
     * @throws \LoyaltyCorp\EasyIdentity\Exceptions\RequiredDataMissingException
     */
    public function testCreate(): void
    {
        $config = new Config([
            'client_id' => 'client_id',
            'client_secret' => 'client_secret',
            'domain' => 'domain'
        ]);

        $factory = new AuthenticationApiClientFactory($config);
        $factory->create();

        // If no exception was thrown test is good.
        $this->addToAssertionCount(1);
    }
}

\class_alias(
    AuthenticationApiClientFactoryTest::class,
    StepTheFkUp\EasyIdentity\Tests\Implementations\Auth0\AuthenticationApiClientFactoryTest::class,
    false
);
