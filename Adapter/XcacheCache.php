<?php

namespace Kasseler\Component\Cache\Adapter;

use Kasseler\Component\Cache\CacheElement;

/**
 * Xcache
 */
class XcacheCache extends AbstractAdapter
{
    /**
     * @param $key
     *
     * @return XcacheCache
     */
    public function delete($key)
    {
        xcache_unset($this->getKey($key));

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
        $key = $this->getKey($key);
        if ($this->has($key)) {
            return $this->unserialize(xcache_get($key));
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
        return xcache_isset($this->getKey($key));
    }

    /**
     * @param mixed $key
     * @param mixed $data
     * @param int $ttl
     *
     * @return XcacheCache
     */
    public function set($key, $data, $ttl = CacheElement::DAY)
    {
        $cacheElement = new CacheElement($this->getKey($key), $data, $ttl);
        xcache_set($cacheElement->key(), $this->serialize($cacheElement), $ttl);

        return $this;
    }

    /**
     * @return bool
     */
    public function drop()
    {
        $this->checkAuthorization();
        xcache_clear_cache(XC_TYPE_VAR);

        return true;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'xcache';
    }

    /**
     * @return bool
     */
    protected function checkAuthorization()
    {
        if (ini_get('xcache.admin.enable_auth')) {
            throw new \BadMethodCallException('"xcache.admin.enable_auth" to "Off" in your php.ini.');
        }

        return true;
    }
}
