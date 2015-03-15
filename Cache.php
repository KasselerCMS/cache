<?php

namespace Kasseler\Component\Cache;
use Kasseler\Component\Cache\Adapter\AdapterInterface;

/**
 * Cache
 */
class Cache
{
    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter = null)
    {
        if ($adapter) {
            $this->setAdapter($adapter);
        }
    }

    /**
     * {@inheritdoc }
     *
     * @param AdapterInterface $adapter
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * {@inheritdoc }
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * {@inheritdoc }
     *
     * @param string $key
     *
     * @return Cache
     */
    public function delete($key)
    {
        $this->getAdapter()->delete($key);

        return $this;
    }

    /**
     * {@inheritdoc }
     *
     * @param string $key
     * @param mixed $default
     *
     * @return bool|CacheElement
     */
    public function get($key, $default = null)
    {
        $cache = $this->getAdapter()->get($key, $default);
        if ($cache instanceof CacheElement){
            return $cache;
        }

        return false;
    }

        /**
     * {@inheritdoc }
     *
     * @param string $key
     */
    public function has($key)
    {
        return $this->getAdapter()->has($key);
    }

    /**
     * {@inheritdoc }
     *
     * @param string $key
     * @param mixed  $value
     * @param null   $ttl
     *
     * @return Cache
     */
    public function set($key, $value, $ttl = CacheElement::DAY)
    {
        $this->getAdapter()->set($key, $value, $ttl);

        return $this;
    }

    /**
     * {@inheritdoc }
     */
    public function drop()
    {
        return $this->adapter->drop();
    }
}
