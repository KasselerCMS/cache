<?php

namespace Kasseler\Component\Cache\Adapter;

use Kasseler\Component\Cache\CacheElement;

/**
 * SQLite3
 */
class SQLite3Cache extends AbstractAdapter
{
    const ID_FIELD = 'key';
    const DATA_FIELD = 'data';

    /**
     * @var \SQLite3
     */
    private $sqlite;

    /**
     * @var string
     */
    private $table = '__cache';

    /**
     * {@inheritdoc}
     */
    public function __construct($data = null, $dir = null)
    {
        if (!($data instanceof \SQLite3)) {
            $data = is_null($data)
                ? '__cache.sqlite'
                : $data
            ;
            $dir = !$dir
                ? realpath(sys_get_temp_dir()).'/cache'
                : $dir
            ;
            !file_exists($dir) && mkdir($dir);

            $created = !file_exists($dir.DIRECTORY_SEPARATOR.$data);
            $this->sqlite = new \SQLite3($dir.DIRECTORY_SEPARATOR.$data);

            if ($created) {
                $this->sqlite->exec(
                    sprintf(
                        'CREATE TABLE IF NOT EXISTS %s(%s TEXT PRIMARY KEY NOT NULL, %s BLOB)',
                        $this->table,
                        self::ID_FIELD,
                        self::DATA_FIELD
                    )
                );
            }
        } else {
            $this->sqlite = $data;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param $key
     *
     * @return SQLite3Cache
     */
    public function delete($key)
    {
        $key = is_array($key) ? $this->getKey($key) : $key;
        $statement = $this->sqlite->prepare(
            sprintf(
                'DELETE FROM %s WHERE %s = :id',
                $this->table,
                self::ID_FIELD
            )
        );
        $statement->bindValue(':id', $key);

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
        $data = $this->findById($key);

        return !$data
            ? $default
            : $data
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @param $key
     *
     * @return bool|CacheElement
     */
    public function has($key)
    {
        return (bool) $this->findById($key);
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $key
     * @param mixed $data
     * @param int $ttl
     *
     * @return SQLite3Cache
     */
    public function set($key, $data, $ttl = CacheElement::DAY)
    {
        $cacheElement = new CacheElement($this->getKey($key), $data, $ttl);
        $statement = $this->sqlite->prepare(sprintf(
            'INSERT OR REPLACE INTO %s (%s) VALUES (:id, :data)',
            $this->table,
            implode(', ', [self::ID_FIELD, self::DATA_FIELD])
        ));
        $statement->bindValue(':id', $cacheElement->key());
        $statement->bindValue(':data', $this->serialize($cacheElement));
        $statement->execute();

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function drop()
    {
        return $this->sqlite->exec(sprintf('DELETE FROM %s', $this->table));
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return 'sqlite';
    }

    /**
     * {@inheritdoc}
     *
     * @param $key
     *
     * @return bool|CacheElement
     */
    private function findById($key)
    {
        $statement = $this->sqlite->prepare(sprintf(
            'SELECT %s FROM %s WHERE %s = :id LIMIT 1',
            implode(',', [self::ID_FIELD, self::DATA_FIELD]),
            $this->table,
            self::ID_FIELD
        ));
        $statement->bindValue(':id', $this->getKey($key), SQLITE3_TEXT);
        $item = $statement->execute()->fetchArray(SQLITE3_ASSOC);
        if (false === $item) {
            return false;
        }
        /** @var CacheElement $item */
        $item = $this->unserialize($item['data']);

        if ($item->isExpired()) {
            $this->delete($item->key());
            return false;
        }

        return $item;
    }
}
