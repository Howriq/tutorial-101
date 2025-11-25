<?php

declare(strict_types=1);

namespace Light\App;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\EntityListenerResolver;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use Dot\Cache\Adapter\ArrayAdapter;
use Dot\Cache\Adapter\FilesystemAdapter;
use Light\App\Factory\EntityListenerResolverFactory;
use Light\App\Factory\GetIndexViewHandlerFactory;
use Light\App\Handler\GetIndexViewHandler;
use Light\App\Types\UuidType;
use Mezzio\Application;
use Roave\PsrContainerDoctrine\EntityManagerFactory;

use function getcwd;

class ConfigProvider
{
    /**
    @return array{
     *     dependencies: array<mixed>,
     *     templates: array<mixed>,
     * }
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'doctrine'     => $this->getDoctrineConfig(),
            'templates'    => $this->getTemplates(),
        ];
    }

    /**
     * @return array{
     *     delegators: array<class-string, array<class-string>>,
     *     factories: array<class-string, class-string>,
     * }
     */
    public function getDependencies(): array
    {
        return [
            'delegators' => [
                Application::class => [
                    RoutesDelegator::class,
                ],
            ],
            'factories'  => [
                'doctrine.entity_manager.orm_default' => EntityManagerFactory::class,
                GetIndexViewHandler::class            => GetIndexViewHandlerFactory::class,
                EntityListenerResolver::class         => EntityListenerResolverFactory::class,
            ],
            'aliases'    => [
                EntityManager::class          => 'doctrine.entity_manager.orm_default',
                EntityManagerInterface::class => 'doctrine.entity_manager.orm_default',
            ],
        ];
    }

    /**
     * @return array{
     *     paths: array{
     *          app: array{literal-string&non-falsy-string},
     *          error: array{literal-string&non-falsy-string},
     *          layout: array{literal-string&non-falsy-string},
     *          partial: array{literal-string&non-falsy-string},
     *     }
     * }
     */
    public function getTemplates(): array
    {
        return [
            'paths' => [
                'app'     => [__DIR__ . '/../templates/app'],
                'error'   => [__DIR__ . '/../templates/error'],
                'layout'  => [__DIR__ . '/../templates/layout'],
                'partial' => [__DIR__ . '/../templates/partial'],
            ],
        ];
    }

    private function getDoctrineConfig(): array
    {
        return [
            'cache'         => [
                'array'      => [
                    'class' => ArrayAdapter::class,
                ],
                'filesystem' => [
                    'class'     => FilesystemAdapter::class,
                    'directory' => getcwd() . '/data/cache',
                    'namespace' => 'doctrine',
                ],
            ],
            'configuration' => [
                'orm_default' => [
                    'entity_listener_resolver' => EntityListenerResolver::class,
                    'result_cache'             => 'filesystem',
                    'metadata_cache'           => 'filesystem',
                    'query_cache'              => 'filesystem',
                    'hydration_cache'          => 'array',
                    'typed_field_mapper'       => null,
                    'second_level_cache'       => [
                        'enabled'                    => true,
                        'default_lifetime'           => 3600,
                        'default_lock_lifetime'      => 60,
                        'file_lock_region_directory' => '',
                        'regions'                    => [],
                    ],
                ],
            ],
            'driver'        => [
                // The default metadata driver aggregates all other drivers into a single one.
                // Override `orm_default` only if you know what you're doing.
                'orm_default' => [
                    'class' => MappingDriverChain::class,
                ],
            ],
            'migrations'    => [
                'table_storage' => [
                    'table_name'                 => 'doctrine_migration_versions',
                    'version_column_name'        => 'version',
                    'version_column_length'      => 191,
                    'executed_at_column_name'    => 'executed_at',
                    'execution_time_column_name' => 'execution_time',
                ],
                // Modify this line based on where you would like to have you migrations
                'migrations_paths'        => [
                    'Migrations' => 'src/Migrations',
                ],
                'all_or_nothing'          => true,
                'check_database_platform' => true,
            ],
            'types'         => [
                UuidType::NAME => UuidType::class,
            ],
        ];
    }
}
