<?php
namespace Unisharp\GoogleCloud\BigTable\Connection;

interface ConnectionInterface
{
    public function listInstances(array $args);
    public function getInstance(array $args);
}
