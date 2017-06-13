<?php
namespace Unisharp\GoogleCloud\BigTable;

use Unisharp\GoogleCloud\BigTable\Connection\ConnectionInterface;

abstract class BaseObject
{
    protected $connection;

    protected $projectId;

    protected $encode;

    protected $iam;

    public function __construct(
        ConnectionInterface $connection,
        $projectId,
        $encode,
        $info = null
    ) {
        $this->connection = $connection;
        $this->projectId = $projectId;
        $this->encode = $encode;
        $this->info = $info;
    }
}
