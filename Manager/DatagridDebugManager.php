<?php

namespace Gorgo\Bundle\DatagridDebugBundle\Manager;

use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use Oro\Bundle\DataGridBundle\Provider\ConfigurationProvider;

class DatagridDebugManager
{
    /** @var ConfigurationProvider */
    private $configurationProvider;

    /** @var null|array */
    private $datagridNames = null;

    /**
     * @param ConfigurationProvider $configurationProvider
     */
    public function __construct(ConfigurationProvider $configurationProvider)
    {
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * @return array
     */
    public function getDatagridNames()
    {
        if (null === $this->datagridNames) {
            $this->configurationProvider->loadConfiguration();
            $reflection = new \ReflectionClass(ConfigurationProvider::class);
            $property = $reflection->getProperty('rawConfiguration');
            $property->setAccessible(true);
            $this->datagridNames = array_keys($property->getValue($this->configurationProvider));
            $property->setAccessible(false);
        }

        if (!$this->datagridNames) {
            $this->datagridNames = [];
        }

        return $this->datagridNames;
    }

    /**
     * @param $gridName
     *
     * @return string|null
     */
    public function getType(string $gridName): ?string
    {
        $configuration = $this->configurationProvider->getRawConfiguration($gridName);
        if (!$configuration) {
            return null;
        }

        $type = $configuration['source']['type'] ?? null;
        if (!$type && isset($configuration['extends'])) {
            return $this->getType($configuration['extends']);
        }

        return $type;
    }

    /**
     * @param string $gridName
     *
     * @return bool
     */
    public function isMixin(string $gridName): bool
    {
        $configuration = $this->configurationProvider->getRawConfiguration($gridName);

        $isMixin = $configuration['options']['mixin'] ?? false;
        if (!$isMixin && isset($configuration['extends'])) {
            return $this->isMixin($configuration['extends']);
        }

        return $isMixin;
    }

    /**
     * @param string $gridName
     *
     * @return string|null
     */
    public function getParent(string $gridName): ?string
    {
        $configuration = $this->configurationProvider->getRawConfiguration($gridName);

        return $configuration['extends'] ?? null;
    }

    /**
     * @param string $gridName
     *
     * @return DatagridConfiguration
     */
    public function getConfiguration(string $gridName)
    {
        return $this->configurationProvider->getConfiguration($gridName);
    }
}
