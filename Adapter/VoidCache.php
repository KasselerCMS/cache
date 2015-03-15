<?php

namespace Kasseler\Component\Cache\Adapter;

use Kasseler\Component\Cache\CacheElement;

/**
 * NotCache
 */
class VoidCache extends AbstractAdapter
{
    /**
     * @param $key
     *
     * @return $this
     */
    public function delete($key)
    {
        return $this;
    }

    /**
     * @param mixed $key
     * @param mixed $default
     *
     * @return bool
     */
    public function get($key, $default = null)
    {
        return false;
    }

    /**
     * @param mixed $key
     *
     * @return bool
     */
    public function has($key)
    {
        return false;
    }

    /**
     * @param mixed $key
     * @param mixed $data
     * @param int $ttl
     *
     * @return VoidCache
     */
    public function set($key, $data, $ttl = CacheElement::DAY)
    {
        return $this;
    }

    /**
     * @return bool
     */
    public function drop()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'void';
    }
}
