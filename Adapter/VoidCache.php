<?php

namespace Kasseler\Component\Cache\Adapter;

use Kasseler\Component\Cache\CacheElement;

/**
 * NotCache
 */
class VoidCache extends AbstractAdapter
{
    /**
     * {@inheritdoc}
     *
     * @param $key
     *
     * @return $this
     */
    public function delete($key)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     *
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
     * {@inheritdoc}
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function has($key)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
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
     * {@inheritdoc}
     *
     * @return bool
     */
    public function drop()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return 'void';
    }
}
