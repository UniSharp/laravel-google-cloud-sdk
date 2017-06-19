<?php
namespace Unisharp\GoogleCloud\BigTable\Connection;

interface ConnectionInterface
{
    public function listInstances(array $args);
    public function getInstance(array $args);
    public function listTables(array $args);
    public function createTable(array $args);
    public function getTable(array $args);
}
