<?php

namespace Bulldesk\Granatum;

use GuzzleHttp\Client;
use Tightenco\Collect\Support\Collection;

abstract class ApiResource extends GranatumObject
{
    /**
     * The http client.
     */
    private $client;

    /**
     * The class map.
     */
    private static $resourceMap = [
        'account' => 'contas',
        'bank' => 'banco',
        'category' => 'categorias',
        'city' => 'cidades',
        'customer' => 'clientes',
        'entry' => 'lancamentos',
        'state' => 'estados',
        'transfer' => 'transferencias',
    ];

    /**
     * Get or set the Http Client.
     */
    public function setHttpClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get or set the Http Client.
     */
    public function getHttpClient(): Client
    {
        return $this->client ?: new Client;
    }

    /**
     * Get only numbers from a given value.
     */
    protected function getNumbers($value)
    {
        return preg_replace('/[^0-9]/', '', $value);
    }

    /**
     * Get all of the specified resource.
     */
    public function all(array $options = []): Collection
    {
        $response = $this->getHttpClient()->get($this->getResourceUri(), $this->getOptions($options));

        return $this->toCollection($response);
    }

    /**
     * Find a resource by a key/value.
     */
    public function findBy(string $key, $value, array $options = [])
    {
        return $this->all($options)->where($key, $value)->first();
    }

    /**
     * Get all of the specified resource.
     */
    public function create(array $data, array $options = []): self
    {
        $options = array_merge(['json' => $data], $options);

        $response = $this->getHttpClient()->post($this->getResourceUri(), $this->getOptions($options));

        return $this->createFromResponse($response);
    }

    /**
     * Get a given resource.
     */
    public function find(int $id, array $options = []): self
    {
        $response = $this->getHttpClient()->get($this->getResourceUri($id), $this->getOptions($options));

        return $this->createFromResponse($response);
    }

    /**
     * Update a given resource.
     */
    public function update($id, $data = null, array $options = []): self
    {
        if (is_array($id) && is_null($data)) {
            $data = $id;
            $id = null;
        }

        $options = array_merge(['json' => $data], $options);

        $response = $this->getHttpClient()->put($this->getResourceUri($id), $this->getOptions($options));

        return $this->createFromResponse($response);
    }

    /**
     * Delete a given resource.
     */
    public function delete(int $id = null, array $options = []): int
    {
        $response = $this->getHttpClient()->delete($this->getResourceUri($id), $this->getOptions($options));

        return $response->getStatusCode();
    }

    /**
     * Get the resource endpoint.
     */
    private function getResourceUri(int $id = null): string
    {
        return vsprintf('%s/%s/%s', [
            $this->getApiBase(),
            $this->getResource(),
            $id ?: $this->id,
        ]);
    }

    /**
     * Get the resource options.
     */
    private function getOptions(array $options = null): array
    {
        $options = array_merge_recursive($options, [
            'query' => [
                'access_token' => $this->getApiToken(),
            ],
        ]);

        return $options;
    }

    /**
     * Add a resource map.
     */
    public static function addResourceMap(string $key, string $value): void
    {
        self::$resourceMap[$key] = $value;
    }

    /**
     * Get the resource name.
     */
    public function getResource(): string
    {
        $class = basename(str_replace('\\', '/', get_called_class()));

        return $this::$resourceMap[str_replace('\\', '', strtolower($class))];
    }

    /**
     * Create a new instance.
     */
    protected function newInstance()
    {
        return (new static)->setHttpClient($this->getHttpClient());
    }

    /**
     * Get the API token.
     */
    private function getApiToken(): ?string
    {
        return Granatum::$token;
    }

    /**
     * Get the API base.
     */
    private function getApiBase(): string
    {
        return Granatum::$base;
    }
}
