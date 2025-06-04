<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit;

use Beste\Json;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Test;

use function putenv;

/**
 * @internal
 *
 * @phpstan-import-type ServiceAccountShape from Factory
 */
final class FactoryTest extends UnitTestCase
{
    /**
     * @var non-empty-string
     */
    private string $serviceAccountFilePath;

    /**
     * @var ServiceAccountShape
     */
    private array $serviceAccountArray;

    protected function setUp(): void
    {
        $this->serviceAccountFilePath = self::$fixturesDir.'/ServiceAccount/valid.json';
        $this->serviceAccountArray = Json::decodeFile($this->serviceAccountFilePath, true);
    }

    #[DoesNotPerformAssertions]
    #[Test]
    public function itUsesTheCredentialsFromTheGoogleApplicationCredentialsEnvironmentVariable(): void
    {
        putenv('GOOGLE_APPLICATION_CREDENTIALS='.$this->serviceAccountFilePath);

        $this->assertServices(new Factory());

        putenv('GOOGLE_APPLICATION_CREDENTIALS');
    }

    #[DoesNotPerformAssertions]
    #[Test]
    public function itCanBeConfiguredWithThePathToAServiceAccount(): void
    {
        $factory = (new Factory())->withServiceAccount($this->serviceAccountFilePath);

        $this->assertServices($factory);
    }

    #[DoesNotPerformAssertions]
    #[Test]
    public function itCanBeConfiguredWithAServiceAccountArray(): void
    {
        $factory = (new Factory())->withServiceAccount($this->serviceAccountArray);

        $this->assertServices($factory);
    }

    private function assertServices(Factory $factory): void
    {
        $factory->createAuth();
        $factory->createDatabase();
        $factory->createFirestore();
        $factory->createMessaging();
        $factory->createRemoteConfig();
        $factory->createStorage();
    }
}
