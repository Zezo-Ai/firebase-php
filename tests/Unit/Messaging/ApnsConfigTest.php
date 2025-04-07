<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Messaging;

use Beste\Json;
use Iterator;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/**
 * @internal
 */
final class ApnsConfigTest extends UnitTestCase
{
    #[Test]
    public function itIsEmptyWhenItIsEmpty(): void
    {
        $this->assertSame('[]', Json::encode(ApnsConfig::new()));
    }

    #[Test]
    public function itHasADefaultSound(): void
    {
        $expected = [
            'payload' => [
                'aps' => [
                    'sound' => 'default',
                ],
            ],
        ];

        $this->assertJsonStringEqualsJsonString(
            Json::encode($expected),
            Json::encode(ApnsConfig::new()->withDefaultSound()),
        );
    }

    #[Test]
    public function itHasABadge(): void
    {
        $expected = [
            'payload' => [
                'aps' => [
                    'badge' => 123,
                ],
            ],
        ];

        $this->assertJsonStringEqualsJsonString(
            Json::encode($expected),
            Json::encode(ApnsConfig::new()->withBadge(123)),
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    #[DataProvider('validDataProvider')]
    #[Test]
    public function itCanBeCreatedFromAnArray(array $data): void
    {
        $config = ApnsConfig::fromArray($data);
        $this->assertEqualsCanonicalizing($data, $config->jsonSerialize());
    }

    #[Test]
    public function itCanHaveAnImmediatePriority(): void
    {
        $config = ApnsConfig::new()->withImmediatePriority();
        $payload = Json::decode(Json::encode($config), true);

        $this->assertArrayHasKey('headers', $payload);
        $this->assertArrayHasKey('apns-priority', $payload['headers']);
        $this->assertSame('10', $payload['headers']['apns-priority']);
    }

    #[Test]
    public function itCanHaveAPowerConservingPriority(): void
    {
        $config = ApnsConfig::new()->withPowerConservingPriority();
        $payload = Json::decode(Json::encode($config), true);

        $this->assertArrayHasKey('headers', $payload);
        $this->assertArrayHasKey('apns-priority', $payload['headers']);
        $this->assertSame('5', $payload['headers']['apns-priority']);
    }

    #[Test]
    public function itCanBeGivenALiveActivityToken(): void
    {
        $config = ApnsConfig::fromArray(['live_activity_token' => 'token']);

        $payload = Json::decode(Json::encode($config), true);

        $this->assertArrayHasKey('live_activity_token', $payload);
        $this->assertSame('token', $payload['live_activity_token']);
    }

    #[Test]
    public function itHasASubtitle(): void
    {
        $expected = [
            'payload' => [
                'aps' => [
                    'subtitle' => 'i am a subtitle',
                ],
            ],
        ];

        $this->assertJsonStringEqualsJsonString(
            Json::encode($expected),
            Json::encode(ApnsConfig::new()->withSubtitle('i am a subtitle')),
        );
    }

    public static function validDataProvider(): Iterator
    {
        yield 'full_config' => [[
            // https://firebase.google.com/docs/cloud-messaging/admin/send-messages#apns_specific_fields
            'headers' => [
                'apns-priority' => '10',
            ],
            'payload' => [
                'aps' => [
                    'alert' => [
                        'title' => '$GOOGLE up 1.43% on the day',
                        'body' => '$GOOGLE gained 11.80 points to close at 835.67, up 1.43% on the day.',
                    ],
                    'badge' => 42,
                    'sound' => 'default',
                ],
            ],
            'live_activity_token' => 'token',
        ]];
    }
}
