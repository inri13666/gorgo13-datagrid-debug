<?php

namespace Gorgo\Bundle\DatagridDebugBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class DebugDatagridCommand extends ContainerAwareCommand
{
    const NAME = 'gorgo:debug:datagrid';

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName(self::NAME)
            ->addArgument('datagrid', InputArgument::OPTIONAL);
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $debugManager = $this->getContainer()->get('gorgo_datagrid_debug.manager.debug');
        $exactDatagrid = $input->getArgument('datagrid');
        $datagridNames = $exactDatagrid ? [$exactDatagrid] : $debugManager->getDatagridNames();
        $table = new Table($output);
        $table->setHeaders([
            'Datagrid Name',
            'Type',
            'Parent',
        ]);
        foreach ($datagridNames as $datagridName) {
            if ($debugManager->isMixin($datagridName)) {
                continue;
            }

            $table->addRow([
                $datagridName,
                $debugManager->getType($datagridName),
                $debugManager->getParent($datagridName),
            ]);
        }
        $table->render();

        if ($exactDatagrid) {
            $configuration = $debugManager->getConfiguration($exactDatagrid);
            $data = $configuration->toArray();
            //fix `extended_from`
            $extends = $data['extended_from'] ?? null;
            if ($extends) {
                $data['extends'] = end($extends);
            }
            unset($data['extended_from']);
            $definition['datagrids'][$exactDatagrid] = $data;
            if ($configuration) {
                $output->writeln(var_export(Yaml::dump($definition, 7), true));
            }
        }
    }

    /**
     * @param $gridName
     * @param array $datagrids
     *
     * @return string
     */
    protected function getDatagridType($gridName, array $datagrids)
    {
        return '';
    }
}
