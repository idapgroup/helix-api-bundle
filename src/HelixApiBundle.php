<?php

namespace IdapGroup\HelixApiBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class HelixApiBundle extends AbstractBundle
{
    /**
     * @param array $config
     * @param ContainerConfigurator $containerConfigurator
     * @param ContainerBuilder $containerBuilder
     * @return void
     */
    public function loadExtension(array $config, ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void
    {
        $containerConfigurator->import('../config/services.yaml');
    }
}