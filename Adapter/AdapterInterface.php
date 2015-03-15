<?php

namespace Kasseler\Component\Cache\Adapter;
use Kasseler\Component\Cache\CacheElement;

/**
 * AdapterInterface
 */
interface AdapterInterface
{
    /**
     * Delete cache by id
     *
     * @param $keys
     *
     * @return mixed
     */
    public function delete($keys);

    /**
     * Get cache by id
     *
     * @param $keys
     * @param $default
     *
     * @return mixed
     */
    public function get($keys, $default = null);

    /**
     * Is cache exist
     *
     * @param $keys
     *
     * @return mixed
     */
    public function has($keys);

    /**
     * Set new cache
     *
     * @param $keys
     * @param $data
     * @param $ttl
     *
     * @return mixed
     */
    public function set($keys, $data, $ttl = CacheElement::DAY);

    /**
     * Clear cache
     *
     * @return bool
     */
    public function drop();

    /**
     * @return string
     */
    public function getName();
}
