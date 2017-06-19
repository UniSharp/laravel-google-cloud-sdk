<?php
namespace Unisharp\GoogleCloud\BigTable\V2;

use google\bigtable\admin\v2\CreateTableRequest;
use google\bigtable\admin\v2\GetInstanceRequest;
use google\bigtable\admin\v2\GetTableRequest;
use google\bigtable\admin\v2\ListInstancesRequest;
use google\bigtable\admin\v2\ListTablesRequest;
use google\bigtable\admin\v2\Table;
use Google\GAX\AgentHeaderDescriptor;
use Google\GAX\ApiCallable;
use Google\GAX\CallSettings;
use Google\GAX\GrpcConstants;
use Google\GAX\GrpcCredentialsHelper;
use Google\GAX\PageStreamingDescriptor;
use google\iam\v1\IAMPolicyGrpcClient;

class BigtableTableAdminClient
{
    const SERVICE_ADDRESS = 'bigtableadmin.googleapis.com';

    /**
     * The default port of the service.
     */
    const DEFAULT_SERVICE_PORT = 443;

    /**
     * The default timeout for non-retrying methods.
     */
    const DEFAULT_TIMEOUT_MILLIS = 30000;

    /**
     * The name of the code generator, to be included in the agent header.
     */
    const CODEGEN_NAME = 'gapic';

    /**
     * The code generator version, to be included in the agent header.
     */
    const CODEGEN_VERSION = '0.1.0';


    private $grpcCredentialsHelper;
    private $iamPolicyStub;
    private $bigtableTableAdminStub;
    private $scopes;
    private $defaultCallSettings;
    private $descriptors;

    public function __construct($options = [])
    {
        $defaultOptions = [
            'serviceAddress' => self::SERVICE_ADDRESS,
            'port' => self::DEFAULT_SERVICE_PORT,
            'scopes' => [
                'https://www.googleapis.com/auth/cloud-platform',
                'https://www.googleapis.com/auth/bigtable.admin',
                'https://www.googleapis.com/auth/bigtable.admin.cluster',
                'https://www.googleapis.com/auth/bigtable.admin.instance',
                'https://www.googleapis.com/auth/cloud-bigtable.admin',
                'https://www.googleapis.com/auth/cloud-bigtable.admin.cluster'
            ],
            'retryingOverride' => null,
            'timeoutMillis' => self::DEFAULT_TIMEOUT_MILLIS,
            'appName' => 'gax',
            'appVersion' => AgentHeaderDescriptor::getGaxVersion(),
        ];
        $options = array_merge($defaultOptions, $options);

        $headerDescriptor = new AgentHeaderDescriptor([
            'clientName' => $options['appName'],
            'clientVersion' => $options['appVersion'],
            'codeGenName' => self::CODEGEN_NAME,
            'codeGenVersion' => self::CODEGEN_VERSION,
            'gaxVersion' => AgentHeaderDescriptor::getGaxVersion(),
            'phpVersion' => phpversion(),
        ]);

        $defaultDescriptors = ['headerDescriptor' => $headerDescriptor];
        $this->descriptors = [
            'createTable' => $defaultDescriptors,
            'getTable' => $defaultDescriptors,
            'listTables' => $defaultDescriptors,
            'deleteTable' => $defaultDescriptors,
            'setIamPolicy' => $defaultDescriptors,
            'getIamPolicy' => $defaultDescriptors,
            'testIamPermissions' => $defaultDescriptors,
        ];
        $pageStreamingDescriptors = self::getPageStreamingDescriptors();
        foreach ($pageStreamingDescriptors as $method => $pageStreamingDescriptor) {
            $this->descriptors[$method]['pageStreamingDescriptor'] = $pageStreamingDescriptor;
        }

        $clientConfigJsonString = file_get_contents(__DIR__.'/resources/bigtable_table_admin_client_config.json');
        $clientConfig = json_decode($clientConfigJsonString, true);
        $this->defaultCallSettings =
            CallSettings::load(
                'google.bigtable.admin.v2.BigtableTableAdmin',
                $clientConfig,
                $options['retryingOverride'],
                GrpcConstants::getStatusCodeNames(),
                $options['timeoutMillis']
            );

        $this->scopes = $options['scopes'];

        $createStubOptions = [];
        if (array_key_exists('sslCreds', $options)) {
            $createStubOptions['sslCreds'] = $options['sslCreds'];
        }
        $grpcCredentialsHelperOptions = array_diff_key($options, $defaultOptions);
        $this->grpcCredentialsHelper = new GrpcCredentialsHelper($this->scopes, $grpcCredentialsHelperOptions);

        $createIamPolicyStubFunction = function ($hostname, $opts) {
            return new IAMPolicyGrpcClient($hostname, $opts);
        };
        if (array_key_exists('createIamPolicyStubFunction', $options)) {
            $createIamPolicyStubFunction = $options['createIamPolicyStubFunction'];
        }
        $this->iamPolicyStub = $this->grpcCredentialsHelper->createStub(
            $createIamPolicyStubFunction,
            $options['serviceAddress'],
            $options['port'],
            $createStubOptions
        );

        $createBigtableTableAdminStubFunction = function ($hostname, $opts) {
            return new \google\bigtable\admin\v2\BigtableTableAdminClient($hostname, $opts);
        };

        if (array_key_exists('createBigtableTableAdminStubFunction', $options)) {
            $createBigtableTableAdminStubFunction = $options['createBigtableTableAdminStubFunction'];
        }

        $this->bigtableTableAdminStub = $this->grpcCredentialsHelper->createStub(
            $createBigtableTableAdminStubFunction,
            $options['serviceAddress'],
            $options['port'],
            $createStubOptions
        );

    }

    public function listTables($instance, array $optionalArgs = [])
    {
        $request = new ListTablesRequest();
        $request->setParent($instance);
        if (isset($optionalArgs['pageSize'])) {
            $request->setPageSize($optionalArgs['pageSize']);
        }
        if (isset($optionalArgs['pageToken'])) {
            $request->setPageToken($optionalArgs['pageToken']);
        }

        $mergedSettings = $this->defaultCallSettings['listTables']->merge(
            new CallSettings($optionalArgs)
        );
        $callable = ApiCallable::createApiCall(
            $this->bigtableTableAdminStub,
            'ListTables',
            $mergedSettings,
            $this->descriptors['listTables']
        );

        return $callable(
            $request,
            [],
            ['call_credentials_callback' => $this->createCredentialsCallback()]);
    }

    public function getTable($table, $optionalArgs = [])
    {
        $request = new GetTableRequest();
        $request->setName($table);
        $mergedSettings = $this->defaultCallSettings['getTable']->merge(
            new CallSettings($optionalArgs)
        );
        $callable = ApiCallable::createApiCall(
            $this->bigtableTableAdminStub,
            'GetTable',
            $mergedSettings,
            $this->descriptors['getTable']
        );

        return $callable(
            $request,
            [],
            ['call_credentials_callback' => $this->createCredentialsCallback()]);
    }

    public function createTable($name, $parent, $optionalArgs = [])
    {
        $request = new CreateTableRequest();
        $request->setParent($parent);
        $table = new Table();
        $request->setTableId($name);
        $request->setTable($table);
        $mergedSettings = $this->defaultCallSettings['createTable']->merge(
            new CallSettings($optionalArgs)
        );
        $callable = ApiCallable::createApiCall(
            $this->bigtableTableAdminStub,
            'CreateTable',
            $mergedSettings,
            $this->descriptors['createTable']
        );

        return $callable(
            $request,
            [],
            ['call_credentials_callback' => $this->createCredentialsCallback()]);
    }

    private static function getPageStreamingDescriptors()
    {
        $listTopicsPageStreamingDescriptor =
            new PageStreamingDescriptor([
                'requestPageTokenField' => 'page_token',
                'requestPageSizeField' => 'page_size',
                'responsePageTokenField' => 'next_page_token',
                'resourceField' => 'topics',
            ]);

        $pageStreamingDescriptors = [
            'listTables' => $listTopicsPageStreamingDescriptor,
        ];

        return $pageStreamingDescriptors;
    }

    private function createCredentialsCallback()
    {
        return $this->grpcCredentialsHelper->createCallCredentialsCallback();
    }
}
