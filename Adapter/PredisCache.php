<?php

namespace Kasseler\Component\Cache\Adapter;

use Kasseler\Component\Cache\CacheElement;
use Predis\Client;

/**
 * Predis
 */
class PredisCache extends AbstractAdapter
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * {@inheritdoc}
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->client = new Client($config);
    }

    /**
     * {@inheritdoc}
     */
    public function __destruct()
    {
        $this->client->disconnect();
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $key
     *
     * @return RedisCache
     */
    public function delete($key)
    {
        $this->client->del($this->getKey($key));

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $key
     * @param mixed $default
     *
     * @return bool|CacheElement
     */
    public function get($key, $default = null)
    {
        if ($data = $this->client->get($this->getKey($key))) {
            return $this->unserialize($data);
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function has($key)
    {
        return $this->client->exists($this->getKey($key));
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $key
     * @param mixed $data
     * @param int $ttl
     *
     * @return RedisCache
     */
    public function set($key, $data, $ttl = CacheElement::DAY)
    {
        $cacheElement = new CacheElement($this->getKey($key), $data, $ttl);

        $ttl > 0
            ? $this->client->setex($cacheElement->getKeys(), $ttl, $this->serialize($cacheElement))
            : $this->client->set($cacheElement->getKeys(), $this->serialize($cacheElement))
        ;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function drop()
    {
        $response = $this->client->flushdb();

        return $response === true || $response == 'OK';
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return 'predis';
    }
}
