<?php

namespace Kasseler\Component\Cache\Adapter;

/**
 * AbstractAdapter
 */
abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * @param $string
     *
     * @return bool
     */
    protected function isKeyValid($string)
    {
        return is_string($string) && !empty($string) && preg_match('/^[a-f0-9]{32}$/', $string);
    }

    /**
     * @param $key
     *
     * @return string
     */
    protected function getKey($key)
    {
        if (!$this->isKeyValid($key)){
            is_array($key) && ksort($key);
            return md5(serialize($key));
        }

        return $key;
    }

    /**
     * @param $value
     *
     * @return string
     */
    protected function serialize($value)
    {
        return addslashes(serialize($value));
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    protected function unserialize($value)
    {
        return unserialize(stripcslashes($value));
    }
}
