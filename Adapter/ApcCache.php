<?php

namespace Kasseler\Component\Cache\Adapter;

use Kasseler\Component\Cache\CacheElement;
use Kasseler\Component\Cache\Exception\CacheException;

/**
 * Apc
 */
class ApcCache extends AbstractAdapter
{
    /**
     * @param mixed $key
     *
     * @return ApcCache
     * @throws CacheException
     */
    public function delete($key)
    {
        if ($this->has($key)) {
            if (!apc_delete($this->getKey($key))) {
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
        if ($this->has($key)) {
            if (!$data = apc_fetch($this->getKey($key))) {
                throw new CacheException(sprintf('Error data with the keys "%s"', implode(', ', $key)));
            }
            return $this->unserialize($data);
        }

        return $default;
    }

    /**
     * @param mixed $key
     *
     * @return bool|\string[]
     */
    public function has($key)
    {
        return apc_exists($this->getKey($key));
    }

    /**
     * @param mixed $key
     * @param mixed $data
     * @param int   $ttl
     *
     * @return ApcCache
     * @throws CacheException
     */
    public function set($key, $data, $ttl = CacheElement::DAY)
    {
        $cacheElement = new CacheElement($this->getKey($key), $data, $ttl);
        if (!apc_store($cacheElement->key(), $this->serialize($cacheElement), $cacheElement->ttl())) {
            throw new CacheException(sprintf('Error saving data with the keys "%s"', implode(', ', $key)));
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function drop()
    {
        return apc_clear_cache('user') && apc_clear_cache();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'apc';
    }
}
