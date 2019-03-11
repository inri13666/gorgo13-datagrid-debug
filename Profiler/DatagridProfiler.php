<?php

namespace Gorgo\Bundle\DatagridDebugBundle\Profiler;

use Oro\Bundle\DataGridBundle\Datagrid\Manager;

class DatagridProfiler
{
    /**
     * @var Manager
     */
    private $datagridManager;

    /**
     * @param Manager $datagridManager
     */
    public function __construct(Manager $datagridManager)
    {
        $this->datagridManager = $datagridManager;
    }

    public function getData()
    {
    }

    public function getQuery()
    {
    }
}
