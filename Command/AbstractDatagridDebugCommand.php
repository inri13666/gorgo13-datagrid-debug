<?php

namespace Gorgo\Bundle\DatagridDebugBundle\Command;

use Oro\Bundle\DataGridBundle\Provider\ConfigurationProvider;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

abstract class AbstractDatagridDebugCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName($this->getName())
            ->addArgument('datagrid', InputArgument::REQUIRED)
            ->addOption('bind', null, InputOption::VALUE_OPTIONAL, 'JSON string or path to JSON file', '{}')
            ->addOption('additional', null, InputOption::VALUE_OPTIONAL, 'JSON string or path to JSON file', '{}');
    }

    /**
     * @param $data
     *
     * @return array|null
     */
    protected function parseJsonOption($data): ?array
    {
        if (is_file($data)) {
            $data = file_get_contents($data);
        }

        $data = json_decode($data, true);

        if (json_last_error()) {
            return null;
        }

        return $data;
    }

    /**
     * @return ConfigurationProvider
     */
    protected function getDatagridConfigurationProvider()
    {
        return $this->getContainer()->get('oro_datagrid.configuration.provider');
    }
}
