<?php

namespace Kasseler\Component\Cache\Adapter;

use Kasseler\Component\Cache\CacheElement;
use Kasseler\Component\Cache\Exception\CacheException;

/**
 * Memcache
 */
class MemcacheCache extends AbstractAdapter
{
    /**
     * @var \Memcache
     */
    protected $adapter;

    /**
     * @param mixed  $data
     */
    public function __construct($data = null)
    {
        if (!($data instanceof \Memcache)) {
            $this->adapter = new \Memcache();
            if (is_array($data)) {
                foreach ($data as $config) {
                    $this->adapter->addServer($config['host'], $config['port']);
                }
            } else {
                $this->adapter->addServer('localhost', '11211');
            }
        } else {
            $this->adapter = $data;
        }
    }

    /**
     * @param string $host
     * @param int    $port
     * @param int    $weight
     *
     */
    public function addServer($host, $port, $weight = 0)
    {
        $this->adapter->addServer($host, $port, $weight);
    }

    /**
     * @param mixed $key
     *
     * @return MemcacheCache
     * @throws CacheException
     */
    public function delete($key)
    {
        if ($this->has($key)) {
            if (!$this->adapter->delete($this->getKey($key))) {
                throw new CacheException(sprintf('Error deleting data with the key "%s"', implode(', ', $key)));
            }
        }

        return $this;
    }

    /**
     * @param mixed $key
     * @param mixed $default
     *
     * @return bool|CacheElement
     * @throws CacheException
     */
    public function get($key, $default = null)
    {
        if ($this->has($key)){
            if(!$data = $this->adapter->get($this->getKey($key))) {
                throw new CacheException(sprintf('Error data with the keys "%s"', implode(', ', $key)));
            }
            return $this->unserialize($data);
        }

        return $default;
    }

    /**
     * @param mixed $key
     *
     * @return bool
     */
    public function has($key)
    {
        if ($this->adapter->get($this->getKey($key))) {
            return true;
        }

        return false;
    }

    /**
     * @param mixed $key
     * @param mixed $data
     * @param int $ttl
     *
     * @return MemcacheCache
     * @throws CacheException
     */
    public function set($key, $data, $ttl = CacheElement::DAY)
    {
        $cacheElement = new CacheElement($this->getKey($key), $data, $ttl);
        if (!$this->adapter->set($cacheElement->key(), $this->serialize($cacheElement), 0, $ttl)) {
            throw new CacheException(sprintf('Error saving data with the keys "%s"', implode(', ', $key)));
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function drop()
    {
        return $this->adapter->flush();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'memcache';
    }
}
