<?php

namespace Kasseler\Component\Cache\Adapter;

use Kasseler\Component\Cache\CacheElement;

/**
 * Array
 */
class ArrayCache extends AbstractAdapter
{
    /**
     * @var CacheElement[]
     */
    protected $data = [];

    /**
     * {@inheritdoc}
     *
     * @param mixed $key
     *
     * @return ArrayCache
     */
    public function delete($key)
    {
        $key = $this->getKey($key);
        if ($this->has($key)) {
            unset($this->data[$this->getKey($key)]);
        }

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
            return $this->data[$key];
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
        return array_key_exists($this->getKey($key), $this->data);
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $key
     * @param mixed $data
     * @param int   $ttl
     *
     * @return $this
     */
    public function set($key, $data, $ttl = CacheElement::DAY)
    {
        $this->data[$this->getKey($key)] = new CacheElement($this->getKey($key), $data, $ttl);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function drop()
    {
        $this->data = [];

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return 'array';
    }
}
