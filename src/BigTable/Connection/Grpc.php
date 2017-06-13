<?php
namespace Unisharp\GoogleCloud\BigTable\Connection;

use Google\Cloud\EmulatorTrait;
use Google\Cloud\GrpcRequestWrapper;
use Google\Cloud\GrpcTrait;
use Unisharp\GoogleCloud\BigTable\V1\Client;
use Unisharp\GoogleCloud\BigTable\V2\BigtableInstanceAdminClient;

class Grpc implements ConnectionInterface
{
    use EmulatorTrait;
    use GrpcTrait;

    const BASE_URI = 'https://bigtable.googleapis.com/';
    protected $bigtableInstanceAdminClient;
    public function __construct(array $config)
    {
        $this->setRequestWrapper(new GrpcRequestWrapper($config));
        $grpcConfig = $this->getGaxConfig();
        $emulatorHost = getenv('BIGTABLE_EMULATOR_HOST');
        $baseUri = $this->getEmulatorBaseUri(self::BASE_URI, $emulatorHost);

        if ($emulatorHost) {
            $grpcConfig += [
                'serviceAddress' => parse_url($baseUri, PHP_URL_HOST),
                'port' => parse_url($baseUri, PHP_URL_PORT),
                'sslCreds' => ChannelCredentials::createInsecure()
            ];
        }

        $this->bigtableInstanceAdminClient = new BigtableInstanceAdminClient($grpcConfig);
    }

    public function listInstances(array $args)
    {
        return $this->send([$this->bigtableInstanceAdminClient, 'listInstances'], [
            $this->pluck('project', $args),
            $args
        ]);
    }

    public function getInstance(array $args)
    {
        return $this->send([$this->bigtableInstanceAdminClient, 'getInstance'], [
            $this->pluck('instance', $args),
            $args
        ]);
    }
}
