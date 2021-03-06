<?php

declare(strict_types=1);

namespace EonX\EasySecurity\Tests\Modifiers;

use EonX\EasyApiToken\Tokens\ApiKeyEasyApiToken;
use EonX\EasyApiToken\Tokens\JwtEasyApiToken;
use EonX\EasySecurity\Context;
use EonX\EasySecurity\Interfaces\ContextInterface;
use EonX\EasySecurity\Interfaces\UserInterface;
use EonX\EasySecurity\Interfaces\UserProviderInterface;
use EonX\EasySecurity\Modifiers\UserFromJwtModifier;
use EonX\EasySecurity\Tests\AbstractTestCase;
use EonX\EasySecurity\Tests\Stubs\UserInterfaceStub;
use EonX\EasySecurity\Tests\Stubs\UserProviderInterfaceStub;
use stdClass;
use Symfony\Component\HttpFoundation\Request;

final class UserFromJwtModifierTest extends AbstractTestCase
{
    /**
     * @return iterable<mixed>
     */
    public function modifyProvider(): iterable
    {
        yield 'No user resolved because no token given' => [
            new UserProviderInterfaceStub(),
        ];

        $context = new Context();
        $context->setToken(new ApiKeyEasyApiToken('api-key'));

        yield 'No user resolved because token not jwt' => [
            new UserProviderInterfaceStub(),
            $context,
        ];

        $context->setToken(new JwtEasyApiToken([], 'jwt'));

        yield 'No user resolved because no sub claim' => [
            new UserProviderInterfaceStub(),
            $context,
        ];

        $context->setToken(new JwtEasyApiToken(['sub' => ''], 'jwt'));

        yield 'No user resolved because sub claim empty' => [
            new UserProviderInterfaceStub(),
            $context,
        ];

        $context->setToken(new JwtEasyApiToken(['sub' => 'user-id'], 'jwt'));

        yield 'No user resolved because provider returned null' => [
            new UserProviderInterfaceStub(),
            $context,
        ];

        $context->setToken(new JwtEasyApiToken([
            'sub' => 'user-id',
            static::$mainJwtClaim => new stdClass(), // To cover getClaimSafely
        ], 'jwt'));

        yield 'User resolved' => [
            new UserProviderInterfaceStub($user = new UserInterfaceStub('user-id')),
            $context,
            $user,
        ];
    }

    /**
     * @dataProvider modifyProvider
     */
    public function testResolve(
        UserProviderInterface $userProvider,
        ?ContextInterface $context = null,
        ?UserInterface $user = null
    ): void {
        $context = $context ?? new Context();
        $modifier = new UserFromJwtModifier($userProvider, static::$mainJwtClaim);

        $modifier->modify($context, new Request());

        self::assertEquals($user, $context->getUser());
        self::assertEquals(0, $modifier->getPriority());
    }
}
