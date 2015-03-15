<?php

namespace Kasseler\Component\Cache\Adapter;

use Kasseler\Component\Cache\CacheElement;

/**
 * Xcache
 */
class XcacheCache extends AbstractAdapter
{
    /**
     * {@inheritdoc}
     *
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
     * {@inheritdoc}
     *
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
     * {@inheritdoc}
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function has($key)
    {
        return xcache_isset($this->getKey($key));
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $key
     * @param mixed $data
     * @param int $ttl
     *
     * @return XcacheCache
     */
    public function set($key, $data, $ttl = CacheElement::DAY)
    {
        $cacheElement = new CacheElement($this->getKey($key), $data, $ttl);
        xcache_set($cacheElement->getKeys(), $this->serialize($cacheElement), $ttl);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function drop()
    {
        $this->checkAuthorization();
        xcache_clear_cache(XC_TYPE_VAR);

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return 'xcache';
    }

    /**
     * {@inheritdoc}
     *
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
