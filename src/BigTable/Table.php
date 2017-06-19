<?php
namespace Unisharp\GoogleCloud\BigTable;

use Google\Cloud\Exception\NotFoundException;
use Unisharp\GoogleCloud\BigTable\Connection\ConnectionInterface;
use Unisharp\GoogleCloud\BigTable\Traits\ResourceNameTrait;

class Table extends BaseObject
{
    use ResourceNameTrait;
    protected $name;
    protected $instanceName;
    public function __construct(
        ConnectionInterface $connection,
        $projectId,
        $name,
        $instanceName,
        $encode,
        $info = null
    ) {
        $this->name = $name;
        $this->instanceName = $instanceName;
        parent::__construct($connection, $projectId, $encode, $info);
    }

    public function name()
    {
        return $this->name;
    }

    public function create($options = [])
    {
        $this->connection->createTable($options + [
            'name' => $this->name,
            'instance' => $this->formatName('instance', $this->instanceName, $this->projectId)
        ]);
    }

    public function exists(array $options = [])
    {
        try {
            $this->info($options);
            return true;
        } catch (NotFoundException $e) {
            return false;
        }
    }

    public function reload(array $options = [])
    {
        return $this->info = $this->connection->getTable($options + [
            'table' => $this->formatName('table', $this->name, $this->instanceName, $this->projectId),
        ]);
    }

    public function info(array $options = [])
    {
        if (!$this->info) {
            $this->reload($options);
        }

        return $this->info;
    }
}
