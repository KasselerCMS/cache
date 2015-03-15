<?php

namespace Kasseler\Component\Cache\Adapter;

use Kasseler\Component\Cache\CacheElement;

/**
 * Array
 */
class SessionCache extends AbstractAdapter
{
    /**
     * @var CacheElement[]
     */
    protected $data = [];

    /**
     *
     */
    public function __construct()
    {
        if (php_sapi_name() !== 'cli') {
            if (session_status() !== PHP_SESSION_ACTIVE){
                session_start();
            }
        }
        if (!isset($_SESSION['__cache'])) {
            $_SESSION['__cache'] = [];
        }
        $this->data = &$_SESSION['__cache'];
    }

    /**
     * @param mixed $key
     *
     * @return SessionCache
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

        return $this->has($key) ? $this->data[$key] : $default;
    }

    /**
     * @param mixed $key
     *
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($this->getKey($key), $this->data);
    }

    /**
     * @param mixed $key
     * @param mixed $data
     * @param mixed $ttl
     *
     * @return SessionCache
     */
    public function set($key, $data, $ttl = CacheElement::DAY)
    {
        $this->data[$this->getKey($key)] = new CacheElement($this->getKey($key), $data, $ttl);

        return $this;
    }

    /**
     * @return bool
     */
    public function drop()
    {
        $this->data = [];

        return true;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'session';
    }
}
