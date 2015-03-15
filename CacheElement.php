<?php
namespace Kasseler\Component\Cache;

final class CacheElement
{
    const MINUTE = 60;
    const HOUR = 3600;
    const DAY = 86400;
    const WEEK = 604800;
    const MONTH = 2.63e+6;

    /**
     * @var int
     */
    protected $ttl;

    /**
     * @var array
     */
    protected $key = array();

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @param array   $key An array of key
     * @param mixed   $data Data
     * @param integer $ttl A time to live, default one day
     */
    public function __construct($key, $data, $ttl = CacheElement::DAY)
    {
        $this->createdAt = new \DateTime;
        $this->key = $key;
        $this->ttl = $ttl;
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getKeys()
    {
        return $this->key;
    }

    /**
     * @return integer
     */
    public function getTtl()
    {
        return $this->ttl;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return boolean
     */
    public function isExpired()
    {
        return strtotime('now') > (int) $this->createdAt->format('U') + $this->ttl;
    }

    /**
     * @return \DateTime
     */
    public function getExpirationDate()
    {
        if ($this->isExpired()) {
            return new \DateTime();
        }
        $date = clone $this->createdAt;
        $date = $date->add(new \DateInterval(sprintf('PT%sS', $this->ttl)));
        return $date;
    }
}
