<?php
namespace Unisharp\GoogleCloud\BigTable\V2;

use google\bigtable\admin\v2\GetInstanceRequest;
use google\bigtable\admin\v2\ListInstancesRequest;
use Google\GAX\AgentHeaderDescriptor;
use Google\GAX\ApiCallable;
use Google\GAX\CallSettings;
use Google\GAX\GrpcConstants;
use Google\GAX\GrpcCredentialsHelper;
use Google\GAX\PageStreamingDescriptor;
use google\iam\v1\IAMPolicyGrpcClient;

class BigtableInstanceAdminClient
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
    private $bigtableInstanceAdminStub;
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
            'createInstance' => $defaultDescriptors,
            'getInstance' => $defaultDescriptors,
            'listInstances' => $defaultDescriptors,
            'deleteInstance' => $defaultDescriptors,
            'setIamPolicy' => $defaultDescriptors,
            'getIamPolicy' => $defaultDescriptors,
            'testIamPermissions' => $defaultDescriptors,
        ];
        $pageStreamingDescriptors = self::getPageStreamingDescriptors();
        foreach ($pageStreamingDescriptors as $method => $pageStreamingDescriptor) {
            $this->descriptors[$method]['pageStreamingDescriptor'] = $pageStreamingDescriptor;
        }

        $clientConfigJsonString = file_get_contents(__DIR__.'/resources/bigtable_instance_admin_client_config.json');
        $clientConfig = json_decode($clientConfigJsonString, true);
        $this->defaultCallSettings =
            CallSettings::load(
                'google.bigtable.admin.v2.BigtableInstanceAdmin',
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

        $createBigtableInstanceAdminStubFunction = function ($hostname, $opts) {
            return new \google\bigtable\admin\v2\BigtableInstanceAdminClient($hostname, $opts);
        };

        if (array_key_exists('createBigtableInstanceAdminStubFunction', $options)) {
            $createBigtableInstanceAdminStubFunction = $options['createBigtableInstanceAdminStubFunction'];
        }

        $this->bigtableInstanceAdminStub = $this->grpcCredentialsHelper->createStub(
            $createBigtableInstanceAdminStubFunction,
            $options['serviceAddress'],
            $options['port'],
            $createStubOptions
        );
    }

    public function listInstances($project, $optionalArgs = [])
    {
        $request = new ListInstancesRequest();
        $request->setParent($project);
        if (isset($optionalArgs['pageSize'])) {
            $request->setPageSize($optionalArgs['pageSize']);
        }
        if (isset($optionalArgs['pageToken'])) {
            $request->setPageToken($optionalArgs['pageToken']);
        }

        $mergedSettings = $this->defaultCallSettings['listInstances']->merge(
            new CallSettings($optionalArgs)
        );
        $callable = ApiCallable::createApiCall(
            $this->bigtableInstanceAdminStub,
            'ListInstances',
            $mergedSettings,
            $this->descriptors['listInstances']
        );

        return $callable(
            $request,
            [],
            ['call_credentials_callback' => $this->createCredentialsCallback()]);
    }

    public function getInstance($instance, $optionalArgs = [])
    {
        $request = new GetInstanceRequest();
        $request->setName($instance);
        $mergedSettings = $this->defaultCallSettings['getInstance']->merge(
            new CallSettings($optionalArgs)
        );
        $callable = ApiCallable::createApiCall(
            $this->bigtableInstanceAdminStub,
            'GetInstance',
            $mergedSettings,
            $this->descriptors['getInstance']
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
        $listTopicSubscriptionsPageStreamingDescriptor =
            new PageStreamingDescriptor([
                'requestPageTokenField' => 'page_token',
                'requestPageSizeField' => 'page_size',
                'responsePageTokenField' => 'next_page_token',
                'resourceField' => 'subscriptions',
            ]);

        $pageStreamingDescriptors = [
            'listInstances' => $listTopicsPageStreamingDescriptor,
            'listTopicSubscriptions' => $listTopicSubscriptionsPageStreamingDescriptor,
        ];

        return $pageStreamingDescriptors;
    }

    private function createCredentialsCallback()
    {
        return $this->grpcCredentialsHelper->createCallCredentialsCallback();
    }
}
