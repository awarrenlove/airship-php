<?php

namespace Airship;

use Airship\Client\ClientInterface;
use Mockery;

/**
 * @covers \Airship\Airship
 */
class AirshipTest extends TestCase
{
    public function test_determines_if_control_is_enabled()
    {
        $client = Mockery::mock(ClientInterface::class);

        $client
            ->shouldReceive('sendRequest')
            ->andReturn([
                'bitcoin-pay' => [
                    'is_enabled' => false,
                ],
                'paypal-pay' => [
                    'is_enabled' => true,
                ]
            ]);

        $airship = new Airship($client);

        $user = [
            'type' => 'User',
            'id' => '1234',
            'display_name' => 'ironman@stark.com',
        ];

        self::assertTrue($airship->isEnabled('paypal-pay', $user));
        self::assertFalse($airship->isEnabled('bitcoin-pay', $user));
        self::assertTrue($airship->isEnabled('apple-pay', $user, true), 'Default value was not returned');
        self::assertFalse($airship->isEnabled('android-pay', $user), 'Default value was not returned');
    }

    public function test_determines_if_is_eligible()
    {
        $client = Mockery::mock(ClientInterface::class);

        $client
            ->shouldReceive('sendRequest')
            ->andReturn([
                'bitcoin-pay' => [
                    'is_eligible' => false,
                ],
                'paypal-pay' => [
                    'is_eligible' => true,
                ]
            ]);

        $airship = new Airship($client);

        $user = [
            'type' => 'User',
            'id' => '1234',
            'display_name' => 'ironman@stark.com',
        ];

        self::assertTrue($airship->isEligible('paypal-pay', $user));
        self::assertFalse($airship->isEligible('bitcoin-pay', $user));
        self::assertTrue($airship->isEligible('apple-pay', $user, true), 'Default value was not returned');
        self::assertFalse($airship->isEligible('android-pay', $user), 'Default value was not returned');
    }

    public function test_gets_variations()
    {
        $client = Mockery::mock(ClientInterface::class);

        $client
            ->shouldReceive('sendRequest')
            ->andReturn([
                'bitcoin-pay' => [
                    'variation' => null,
                ],
                'paypal-pay' => [
                    'variation' => 'one-touch',
                ],
            ]);

        $airship = new Airship($client);

        $user = [
            'type' => 'User',
            'id' => '1234',
            'display_name' => 'ironman@stark.com',
        ];

        self::assertEquals('one-touch', $airship->getVariation('paypal-pay', $user));
        self::assertEquals(null, $airship->getVariation('bitcoin-pay', $user));
        self::assertEquals('apple-pay', $airship->getVariation('mobile-pay', $user, 'apple-pay'), 'Default value was not returned');
    }
}
