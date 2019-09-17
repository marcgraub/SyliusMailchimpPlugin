<?php

/** @noinspection PhpUnusedLocalVariableInspection */

declare(strict_types=1);

namespace Setono\SyliusMailchimpPlugin\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SetonoSyliusMailchimpExtension extends AbstractResourceExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->registerResources('setono_sylius_mailchimp', $config['driver'], $config['resources'], $container);

        if ($config['subscribe']) {
            $loader->load('services/conditional/subscribe.xml');
        }

        if ($config['queue']) {
            if (!class_exists('Enqueue\Bundle\EnqueueBundle')) {
                throw new \RuntimeException('Unable to use queues as the enqueue/enqueue-bundle is not installed.');
            }

            // Load handler decorators to work asynchronously via enqueue
            $loader->load('services/handler_async.xml');
        }
        $container->setParameter('setono_sylius_mailchimp.queue', $config['queue']);

        $container->setParameter('setono_sylius_mailchimp.merge_fields', $config['merge_fields']);

        $loader->load('services.xml');
    }
}
