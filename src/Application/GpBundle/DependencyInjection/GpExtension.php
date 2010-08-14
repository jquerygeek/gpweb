<?php

namespace Application\GpBundle\DependencyInjection;

use Symfony\Components\DependencyInjection\Loader\LoaderExtension;
use Symfony\Components\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Components\DependencyInjection\BuilderConfiguration;
use Symfony\Components\DependencyInjection\Reference;

/**
 * Gp extension for DI
 *
 * @package     GPWeb
 * @subpackage  GpBundle
 * @category    Extension
 * @author      Nurul Ferdous <nurul.ferdous@gmail.com>
 */
class GpExtension extends LoaderExtension
{

    /**
     * Loading configuration
     *
     * @param array $config
     * @return BuilderConfiguration
     */
    public function gpLoad($config)
    {
        $configuration = new BuilderConfiguration();

        $loader = new XmlFileLoader(__DIR__ . '/../Resources/config');
        $configuration->merge($loader->load('observer.xml'));

        return $configuration;
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__ . '/../Resources/config/';
    }

    /**
     * Returns the namespace to be used for this extension (XML namespace).
     *
     * @return string The XML namespace
     */
    public function getNamespace()
    {
        return 'http://www.symfony-project.org/schema/dic/symfony';
    }

    /**
     * Returns the recommanded alias to use in XML.
     *
     * This alias is also the mandatory prefix to use when using YAML.
     *
     * @return string The alias
     */
    public function getAlias()
    {
        return 'gp';
    }

}
