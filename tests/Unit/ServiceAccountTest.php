<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit;

use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Valinor\Mapper;
use PHPUnit\Framework\TestCase;

final class ServiceAccountTest extends TestCase
{
    /**
     * @see https://github.com/kreait/firebase-php/pull/1034
     */
    public function testItCanBeMapped(): void
    {
        $mapper = (new Mapper())->allowSuperfluousKeys()->snakeToCamelCase();

        $input = [
            'type' => 'service_account',
            'project_id' => 'project-id',
            'client_email' => 'client-email',
            'private_key' => 'private-key',
        ];

        $serviceAccount = $mapper->map(ServiceAccount::class, $input);

        $this->assertInstanceOf(ServiceAccount::class, $serviceAccount);
    }
}
