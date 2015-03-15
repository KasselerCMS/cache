<?php

namespace Kasseler\Component\Cache\Adapter;

use Kasseler\Component\Cache\CacheElement;
use Kasseler\Component\Cache\Exception\CacheException;

/**
 * File
 */
class FileCache extends AbstractAdapter
{
    const FILE_PREFIX = '__';
    const FILE_EXT = '.php.cache';

    /**
     * @var string
     */
    private $dir;

    /**
     * @param string $dir
     *
     * @throws CacheException
     */
    public function __construct($dir = null)
    {
        if ($dir === null) {
            $dir = realpath(sys_get_temp_dir()).'/cache';
        }

        $this->dir = $dir;
        if (!is_dir($this->dir)) {
            if (!mkdir($this->dir, 0777, true)) {
                throw new CacheException($this->dir.' is not writable');
            }
        }
        if (!is_writable($this->dir)) {
            throw new CacheException($this->dir.' is not writable');
        }
    }

    /**
     * @param mixed $key
     *
     * @return FileCache
     * @throws CacheException
     */
    public function delete($key)
    {
        $this->deleteFile($this->getFileName($this->getKey($key)));

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
        $data = $this->getData($key);

        return !$data
            ? $default
            : $data
        ;
    }

    /**
     * @param mixed $key
     *
     * @return mixed
     * @throws CacheException
     */
    public function has($key)
    {
        return (bool) $this->getData($key);
    }

    /**
     * @param mixed $key
     * @param mixed $data
     * @param int   $ttl
     *
     * @return FileCache
     * @throws CacheException
     */
    public function set($key, $data, $ttl = CacheElement::DAY)
    {
        $file = $this->getFileName($this->getKey($key));
        $cacheElement = new CacheElement($this->getKey($key), $data, $ttl);

        if (!file_put_contents($file, $this->serialize($cacheElement))) {
            throw new CacheException(sprintf('Error saving data with the key "%s"', $key));
        }

        return $this;
    }

    /**
     * @return bool
     * @throws CacheException
     */
    public function drop()
    {
        foreach (glob(realpath($this->dir).DIRECTORY_SEPARATOR.self::FILE_PREFIX.'*'.self::FILE_EXT) as $file) {
            if (!$this->deleteFile($file)) {
                throw new CacheException(sprintf('Error deleting file "%s"', $file));
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'file';
    }

    /**
     * @param string $file
     *
     * @return bool
     */
    protected function deleteFile($file)
    {
        if (file_exists($file) && is_file($file)) {
            return unlink($file);
        }

        return false;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getFileName($name)
    {
        return $this->dir.DIRECTORY_SEPARATOR.self::FILE_PREFIX.$name.self::FILE_EXT;
    }

    /**
     * @param $key
     *
     * @return mixed
     * @throws CacheException
     */
    protected function getData($key)
    {
        $file = $this->getFileName($this->getKey($key));
        if (!file_exists($file)) {
            return false;
        }
        /** @var CacheElement $data */
        if (!$data = $this->unserialize(file_get_contents($file))) {
            throw new CacheException(sprintf('Error with the key "%s" in cache file %s', $key, $file));
        }
        if ($data->isExpired()) {
            $this->delete($data->key());
            return false;
        }

        return $data;
    }
}
