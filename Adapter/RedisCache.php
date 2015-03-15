<?php

namespace Kasseler\Component\Cache\Adapter;

use Kasseler\Component\Cache\CacheElement;

/**
 * Redis
 */
class RedisCache extends AbstractAdapter
{
    /**
     * @var \Redis
     */
    protected $redis;

    /**
     * @param string $server
     * @param int    $port
     */
    public function __construct($server = 'localhost', $port = 6379)
    {
        $this->redis = new \Redis();
        $this->redis->connect($server, $port);
    }

    /**
     * @param mixed $key
     *
     * @return RedisCache
     */
    public function delete($key)
    {
        $this->redis->delete($this->getKey($key));

        return $this;
    }

    /**
     * @param mixed $key
     * @param mixed $default
     *
     * @return bool|CacheElement
     */
    public function get($key, $default = null)
    {
        if ($data = $this->redis->get($this->getKey($key))) {
            return $this->unserialize($data);
        }

        return $default;
    }

    /**
     * @param mixed $key
     *
     * @return mixed
     */
    public function has($key)
    {
        return (bool) $this->redis->exists($this->getKey($key));
    }

    /**
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
            ? $this->redis->setex($cacheElement->key(), $ttl, $this->serialize($cacheElement))
            : $this->redis->set($cacheElement->key(), $this->serialize($cacheElement))
        ;

        return $this;
    }

    /**
     * @return bool
     */
    public function drop()
    {
        return $this->redis->flushDB();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'redis';
    }
}
