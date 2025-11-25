<?php

declare(strict_types=1);

namespace Light\Book;

use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Light\App\Types\UuidType;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'doctrine'     => $this->getDoctrineConfig(),
        ];
    }

    private function getDependencies(): array
    {
        return [
            'delegators' => [
            ],
            'factories'  => [
            ],
        ];
    }

    private function getDoctrineConfig(): array
    {
        return [
            'driver' => [
                'orm_default'  => [
                    'drivers' => [
                        'Light\Book\Entity' => 'BookEntities',
                    ],
                ],
                'BookEntities' => [
                    'class' => AttributeDriver::class,
                    'cache' => 'array',
                    'paths' => [__DIR__ . '/Entity'],
                ],
            ],
        ];
    }
}
