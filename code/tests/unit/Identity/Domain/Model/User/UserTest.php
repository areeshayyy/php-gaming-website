<?php

namespace Gambling\Identity\Domain\Model\User;

use Gambling\Identity\Domain\Model\User\Event\UserArrived;
use Gambling\Identity\Domain\Model\User\Event\UserSignedUp;
use Gambling\Identity\Domain\Model\User\Exception\UserAlreadySignedUpException;
use Gambling\Identity\Port\Adapter\HashAlgorithm\NotSecureHashAlgorithm;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldArrive(): void
    {
        $user = User::arrive();

        $domainEvents = $user->flushDomainEvents();
        $userArrived = $domainEvents[0];

        $this->assertCount(1, $domainEvents);
        $this->assertInstanceOf(UserArrived::class, $userArrived);
        $this->assertEquals($user->id()->toString(), $userArrived->aggregateId());
    }

    /**
     * @test
     */
    public function itShouldSignUpAndCanLogin(): void
    {
        $hashAlgorithm = new NotSecureHashAlgorithm();

        $user = User::arrive();
        $user->signUp(
            new Credentials(
                'marein',
                'correctPassword',
                $hashAlgorithm
            )
        );

        $domainEvents = $user->flushDomainEvents();
        $userSignedUp = $domainEvents[1];

        $this->assertCount(2, $domainEvents);
        $this->assertInstanceOf(UserSignedUp::class, $userSignedUp);
        $this->assertEquals($user->id()->toString(), $userSignedUp->aggregateId());
        $this->assertEquals('marein', $userSignedUp->payload()['username']);

        $this->assertTrue($user->canLogIn('correctPassword', $hashAlgorithm));
        $this->assertFalse($user->canLogIn('wrongPassword', $hashAlgorithm));
    }

    /**
     * @test
     */
    public function itShouldNotLogInWhenNotSignedUp(): void
    {
        $user = User::arrive();
        $canLogIn = $user->canLogIn('password', new NotSecureHashAlgorithm());

        $this->assertFalse($canLogIn);
    }

    /**
     * @test
     */
    public function itShouldThrowAnExceptionIfAlreadySignedUp(): void
    {
        $this->expectException(UserAlreadySignedUpException::class);

        $credentials = new Credentials(
            'marein',
            'password',
            new NotSecureHashAlgorithm()
        );

        $user = User::arrive();
        $user->signUp($credentials);
        $user->signUp($credentials);
    }
}
