<?php

namespace Kasseler\Component\Cache\Adapter;

use Kasseler\Component\Cache\CacheElement;
use Kasseler\Component\Cache\Exception\CacheException;

/**
 * MongoDB
 */
class MongoDBCache extends AbstractAdapter
{
    /**
     * @var \MongoDB
     */
    protected $database;

    /**
     * @var \Mongo
     */
    protected $mongo;

    /**
     * @var \MongoCollection
     */
    protected $collection;

    /**
     * {@inheritdoc}
     *
     * @param string $server
     * @param string $database
     * @param string $collection
     * @param array  $options
     *
     * @throws CacheException
     */
    public function __construct($server = 'mongodb://localhost:27017', $database = '__cache', $collection = '__cache', $options = ['connect' => true])
    {
        $this->mongo = new \MongoClient($server, $options);
        if (!$this->mongo) {
            throw new CacheException('Mongo connection fails');
        }
        $this->database = $this->mongo->selectDB($database);
        $this->collection = $this->database->selectCollection($collection);
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $key
     *
     * @return MongoDBCache
     */
    public function delete($key)
    {
        $this->getCollection()->remove(['key' => $this->getKey($key)]);

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
        $data = $this->getData($key);

        return !$data
            ? $default
            : $data
        ;
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
        return (bool) $this->getData($key);
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $key
     * @param mixed $data
     * @param int   $ttl
     *
     * @return MongoDBCache
     */
    public function set($key, $data, $ttl = CacheElement::DAY)
    {
        $cacheElement = new CacheElement($this->getKey($key), $data, $ttl);
        $this->delete($key);
        $this->getCollection()->insert([
            'key' => $cacheElement->key(),
            'value' => $this->serialize($cacheElement),
            'ttl' => (int) $ttl + time(),
        ]);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function drop()
    {
        $this->getCollection()->drop();

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return 'mango';
    }

    /**
     * @return \MongoCollection
     */
    protected function getCollection()
    {
        return $this->collection;
    }

    /**
     * {@inheritdoc}
     *
     * @param $key
     *
     * @return bool|mixed
     */
    protected function getData($key)
    {
        $data = $this->getCollection()->findOne(['key' => $this->getKey($key)]);

        if (count($data)) {
            $data = array_values($data);
            if (time() > $data[3]) {
                $this->delete($key);
                return false;
            }
            return $this->unserialize($data[2]);
        }

        return false;
    }
}
