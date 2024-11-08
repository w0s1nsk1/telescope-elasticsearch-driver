<?php

namespace W0s1nsk1\TelescopeElasticsearchDriver;


use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\AuthenticationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Http\Promise\Promise;
use Illuminate\Support\Facades\Log;

class TelescopeIndex
{
    /**
     * The index name.
     *
     * @var string
     */
    public string $index;
    public Client $client;

    /**
     * Create new index instance.
     *
     */
    public function __construct()
    {
        $this->index = config('telescope-elasticsearch-driver.index');
        try {
            $client = ClientBuilder::create()
                ->setHosts([config('telescope-elasticsearch-driver.host')]);

            switch(config('telescope-elasticsearch-driver.auth.method')) {
                case AuthMethod::BASIC:
                    $client->setBasicAuthentication(
                        config('telescope-elasticsearch-driver.auth.username'),
                        config('telescope-elasticsearch-driver.auth.password')
                    )->build();
                    break;
                case AuthMethod::API_KEY:
                    $client->setApiKey(
                        config('telescope-elasticsearch-driver.auth.api_key'),
                    );
                    break;
                default:
                    throw new AuthMethodUndefined();
            }

            $this->client = $client->build();
        } catch (AuthenticationException|AuthMethodUndefined $e) {
            Log::error('[TelescopeElasticsearchDriver] Auth failure', ['message' => $e->getMessage()]);
        }
    }


    /**
     * @return Elasticsearch|Promise|void
     */
    public function create()
    {
        try {
            return $this->client->indices()->create([
                'index' => $this->index,
                'body'  => [
                    'settings' => [
                        'index' => [
                            'number_of_shards'   => 1,
                            'number_of_replicas' => 0,
                        ],
                    ],
                    'mappings' => [
                        '_source'    => [
                            'enabled' => true,
                        ],
                        'properties' => $this->properties(),
                    ],
                ],
            ]);
        } catch (ClientResponseException $e) {
            Log::error('[TelescopeElasticsearchDriver] the 4xx error', ['message' => $e->getMessage()]);
        } catch (MissingParameterException $e) {
            Log::error('[TelescopeElasticsearchDriver] the 5xx error', ['message' => $e->getMessage()]);
        } catch (ServerResponseException $e) {
            Log::error('[TelescopeElasticsearchDriver] network error like NoNodeAvailableException', ['message' => $e->getMessage()]);
        }
    }

    public function properties(): array
    {
        return [
            'uuid'                    => [
                'type' => 'keyword',
            ],
            'batch_id'                => [
                'type' => 'keyword',
            ],
            'family_hash'             => [
                'type' => 'keyword',
            ],
            'should_display_on_index' => [
                'type'       => 'boolean',
                'null_value' => true,
            ],
            'type'                    => [
                'type' => 'keyword',
            ],
            'content'                 => [
                'type'    => 'object',
                'dynamic' => false,
            ],
            'tags'                    => [
                'type'       => 'nested',
                'dynamic'    => false,
                'properties' => [
                    'raw'   => [
                        'type' => 'keyword',
                    ],
                    'name'  => [
                        'type' => 'keyword',
                    ],
                    'value' => [
                        'type' => 'keyword',
                    ],
                ],
            ],
            'created_at'              => [
                'type'   => 'date',
                'format' => 'yyyy-MM-dd HH:mm:ss',
            ],
            '@timestamp'              => [
                'type' => 'date',
            ],
        ];
    }

    /**
     * @return void
     */
    public function delete(): void
    {
        try {
            $this->client->indices()->delete([
                'index' => $this->index,
            ]);
        } catch (ClientResponseException $e) {
            Log::error('[TelescopeElasticsearchDriver] the 4xx error', ['message' => $e->getMessage()]);
        } catch (MissingParameterException $e) {
            Log::error('[TelescopeElasticsearchDriver] the 5xx error', ['message' => $e->getMessage()]);
        } catch (ServerResponseException $e) {
            Log::error('[TelescopeElasticsearchDriver] network error like NoNodeAvailableException', ['message' => $e->getMessage()]);
        }
    }

    /**
     * @return bool
     */
    public function exists(): bool
    {
        try {
            return $this->client->indices()->exists([
                    'index' => $this->index,
                ])->getStatusCode() !== 404;
        } catch (ClientResponseException $e) {
            Log::error('[TelescopeElasticsearchDriver] the 4xx error', ['message' => $e->getMessage()]);
        } catch (MissingParameterException $e) {
            Log::error('[TelescopeElasticsearchDriver] the 5xx error', ['message' => $e->getMessage()]);
        } catch (ServerResponseException $e) {
            Log::error('[TelescopeElasticsearchDriver] network error like NoNodeAvailableException', ['message' => $e->getMessage()]);
        }
    }
}