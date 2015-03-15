<?php

namespace Kasseler\Component\Cache\Adapter;

use Kasseler\Component\Cache\CacheElement;
use Kasseler\Component\Cache\Exception\CacheException;

/**
 * Memcached
 */
class MemcachedCache extends AbstractAdapter
{
    /**
     * @var \Memcached
     */
    protected $adapter;

    /**
     * {@inheritdoc}
     *
     * @param mixed  $data
     */
    public function __construct($data = null)
    {
        if (!($data instanceof \Memcached)) {
            $this->adapter = new \Memcached();
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
     * {@inheritdoc}
     *
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
     * {@inheritdoc}
     *
     * @param mixed $key
     *
     * @return MemcachedCache
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
     * {@inheritdoc}
     *
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
     * {@inheritdoc}
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function has($key)
    {
        $cur_compression = $this->adapter->getOption(\Memcached::OPT_COMPRESSION);
        $this->adapter->setOption(\Memcached::OPT_COMPRESSION, false);
        $result = $this->adapter->append($this->getKey($key), '');
        $this->adapter->setOption(\Memcached::OPT_COMPRESSION, $cur_compression);

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $key
     * @param mixed $data
     * @param int $ttl
     *
     * @return MemcachedCache
     * @throws CacheException
     */
    public function set($key, $data, $ttl = CacheElement::DAY)
    {
        $cacheElement = new CacheElement($this->getKey($key), $data, $ttl);
        if (!$this->adapter->set($cacheElement->key(), $this->serialize($cacheElement), $ttl)) {
            throw new CacheException(sprintf('Error saving data with the keys "%s"', implode(', ', $key)));
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function drop()
    {
        return $this->adapter->flush();
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return 'memcached';
    }
}
