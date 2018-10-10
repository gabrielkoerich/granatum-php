<?php

namespace Bulldesk\Granatum;

use GuzzleHttp\Client;
use Tightenco\Collect\Support\Collection;

abstract class ApiResource extends GranatumObject
{
    /**
     * The http client.
     *
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * The class map.
     *
     * @var array
     */
    private $resourceMap = [
        'category' => 'categorias',
        'customer' => 'clientes',
        'account' => 'contas',
        'entry' => 'lancamentos',
        'city' => 'cidades',
        'state' => 'estados',
        'bank' => 'banco',
    ];

    /**
     * Get or set the Http Client.
     *
     * @param Client $client
     */
    public function setHttpClient(Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get or set the Http Client.
     *
     * @param Client $client
     */
    public function getHttpClient()
    {
        return $this->client ?: new Client;
    }

    /**
     * Get only numbers from a given value.
     *
     * @param  string|int $value
     * @return int
     */
    protected function getNumbers($value)
    {
        return preg_replace('/[^0-9]/', '', $value);
    }

    /**
     * Get all of the specified resource.
     *
     * @return Collection
     */
    public function all(array $options = []): Collection
    {
        $response = $this->getHttpClient()->get($this->getResourceUri(), $this->getOptions($options));

        return $this->toCollection($response);
    }

    /**
     * Find a resource by a key/value.
     *
     * @param  string  $key
     * @param  string  $value
     * @return array|null
     */
    public function findBy(string $key, $value, array $options = [])
    {
        return $this->all($options)->where($key, $value)->first();
    }

    /**
     * Get all of the specified resource.
     *
     * @return mixed
     */
    public function create(array $data, array $options = []): self
    {
        $options = array_merge(['json' => $data], $options);

        $response = $this->getHttpClient()->post($this->getResourceUri(), $this->getOptions($options));

        return $this->createFromResponse($response);
    }

    /**
     * Get a given resource.
     *
     * @return mixed
     */
    public function find(int $id, array $options = []): self
    {
        $response = $this->getHttpClient()->get($this->getResourceUri($id), $this->getOptions($options));

        return $this->createFromResponse($response);
    }

    /**
     * Update a given resource.
     *
     * @return mixed
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
     *
     * @return mixed
     */
    public function delete(int $id = null, array $options = []): int
    {
        $response = $this->getHttpClient()->delete($this->getResourceUri($id), $this->getOptions($options));

        return $response->getStatusCode();
    }

    /**
     * Get the resource endpoint.
     *
     * @return string
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
     *
     * @return string
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
     * Get the resource name.
     *
     * @return string
     */
    public function getResource(): string
    {
        $class = basename(str_replace('\\', '/', get_called_class()));

        return $this->resourceMap[str_replace('\\', '', strtolower($class))];
    }

    /**
     * Create a new instance.
     *
     * @return self
     */
    protected function newInstance()
    {
        return (new static)->setHttpClient($this->getHttpClient());
    }

    /**
     * Get the API token.
     *
     * @return string
     */
    private function getApiToken(): ?string
    {
        return Granatum::$token;
    }

    /**
     * Get the API base.
     *
     * @return string
     */
    private function getApiBase(): string
    {
        return Granatum::$base;
    }
}
