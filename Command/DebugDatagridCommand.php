<?php

namespace Gorgo\Bundle\DatagridDebugBundle\Command;

use Oro\Bundle\DataGridBundle\Datagrid\ParameterBag;
use Oro\Bundle\DataGridBundle\Datasource\Orm\OrmDatasource;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class DebugDatagridCommand extends AbstractDatagridDebugCommand
{
    const NAME = 'gorgo:debug:datagrid';

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $gridName = $input->getArgument('datagrid');

        $table = new Table($output);
        $table->setHeaders([
            'Datagrid Name',
            'Type',
            'Parent',
        ]);
        $configuration = $this->getDatagridConfigurationProvider()->getConfiguration($gridName);

        $data = $configuration->toArray();
        //fix `extended_from`
        $extends = $data['extended_from'] ?? null;
        if ($extends) {
            $data['extends'] = end($extends);
        }
        unset($data['extended_from']);

        $table->addRow([
            $configuration->getName(),
            $configuration->getDatasourceType(),
            $data['extends'] ?? null,
        ]);

        $table->render();

        $definition['datagrids'][$gridName] = $data;
        $parameters = new ParameterBag($this->parseJsonOption($input->getOption('bind')));
        $additionalParameters = $this->parseJsonOption($input->getOption('additional'));
        $builder = $this->getContainer()->get('oro_datagrid.datagrid.builder');
        $datagrid = $builder->build($configuration, $parameters, $additionalParameters);
        $output->writeln('');
        $output->writeln('Datagrid configuration:');
        $output->writeln('');
        $output->writeln(Yaml::dump($datagrid->getConfig()->toArray(), 7));
        $output->writeln('');
        $dataSource = $datagrid->getDatasource();
        if ($dataSource instanceof OrmDatasource) {
            $output->writeln('SQL:');
            $query = $dataSource->getQueryBuilder()->getQuery();
            $output->writeln($query->getSQL());
        } else {
            $output->writeln(sprintf('%s', get_class($dataSource)));
        }
    }
}
