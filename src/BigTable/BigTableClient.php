<?php
namespace Unisharp\GoogleCloud\BigTable;

use Google\Cloud\ClientTrait;
use RuntimeException;
use Unisharp\GoogleCloud\BigTable\Connection\Grpc;
use Unisharp\GoogleCloud\BigTable\Traits\ResourceNameTrait;

class BigTableClient
{
    use ClientTrait;
    use ResourceNameTrait;
    const FULL_CONTROL_SCOPE = 'https://www.googleapis.com/auth/cloud-platform';
    protected $connection;
    private $encode;

    public function __construct(array $config = [])
    {
        $connectionType = $this->getConnectionType($config);
        if (!isset($config['scopes'])) {
            $config['scopes'] = [self::FULL_CONTROL_SCOPE];
        }

        if ($connectionType === 'grpc') {
            $this->connection = new Grpc($this->configureAuthentication($config));
            $this->encode = false;
        } else {
            throw new RuntimeException("It's only support grpc");
        }
    }

    public function instances()
    {
        $options['pageToken'] = null;

        do {
            $response = $this->connection->listInstances($options + [
                    'project' => $this->formatName('project', $this->projectId)
                ]);

            foreach ($response['instances'] as $instance) {
                yield $this->instanceFactory($instance['name'], $instance);
            }

            // If there's a page token, we'll request the next page.
            $options['pageToken'] = isset($response['nextPageToken'])
                ? $response['nextPageToken']
                : null;
        } while ($options['pageToken']);
    }

    public function instance(string $name)
    {
        return $this->instanceFactory($name, null);
    }

    protected function instanceFactory($name, $display_name, array $info = [])
    {
        return new Instance($this->connection, $this->projectId, $name, $display_name, $this->encode, $info);
    }
}
