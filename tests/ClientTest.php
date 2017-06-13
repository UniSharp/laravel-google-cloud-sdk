<?php
namespace Tests;

use Google\Cloud\PubSub\PubSubClient;
use PHPUnit\Framework\TestCase;
use Unisharp\GoogleCloud\BigTable\BigTableClient;

class ClientTest extends TestCase
{
    public function testClient()
    {
        //$client = new PubSubClient();
        $client = new BigTableClient();
        /*
        var_dump($client->instance("projects/shareba-testing/instances/shareba-testing")->reload());die;
        foreach($client->instances() as $instance) {
            var_dump($instance->name());die;
        }
        */
    }
}
