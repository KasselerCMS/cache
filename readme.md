Cache Component
=======
Cache library. Implements different adapters.

### Requirements
 - PHP >= 5.4
 - predis/predis ~1.0
 
### Installation
```sh
$ composer require kasseler/cache
```

Usage
===
```php
$cache = new Cache(new ArrayCache());
$cache->set('foo', [1,2,3,4,5]);
if ($cache->has('foo')) {
    var_export($cache->get('test')->getData());
}
$cache->drop();
```
Adapters
===
### Array
```php
$cache = new Cache(new ArrayCache());
```
### Apc
```php
$cache = new Cache(new ApcCache());
```
### File
```php
$cache = new Cache(new FileCache());
```
### Memcache
```php
$cache = new Cache(new MemcacheCache());
```
### Memcached
```php
$cache = new Cache(new MemcachedCache());
```
### MongoDB
```php
$cache = new Cache(new MongoDBCache());
```
### Predis
```php
$cache = new Cache(new PredisCache());
```
### Redis
```php
$cache = new Cache(new RedisCache());
```
### Session
```php
$cache = new Cache(new SessionCache());
```
### SQLite3
```php
$cache = new Cache(new SQLite3Cache());
```
### Void
```php
$cache = new Cache(new VoidCache());
```
### Xcache
```php
$cache = new Cache(new XcacheCache());
```
Speed testing
===
Test was conducted on php 5.6.

|                    | 10K set |              |	10K get |                  | 10K has      |
|--------------|:---------:|:----------:|:---------:|:----------:|:---------:|:----------:|
|              | time, sec | memory, Kb | time, sec | memory, Kb | time, sec | memory, Kb |
| **Session**  | 1,0591    | 5136,7890  | 104,262   | 11933,5893 | 68,2759   | 11933,5781 |
| **File**     | 55,3532   | 19,90      | 11,2696   | 20,0781    | 1,9091    | 19,0937    |
| **Mongo**    | 16,4049   | 19,3671    | 13,9658   | 20,3593    | 13,9008   | 19,375     |
| **Predis**   | 2,2291    | 138,7343   | 2,1721    | 137,625    | 1,6831    | 133,1796   |
| **Redis**    | 1,2731    | 27,1484    | 1,1151    | 27,1953    | 0,786     | 22,6562    |
| **Memcache** | 1,25      | 27,3359    | 1,8681    | 27,7890    | 0,814     | 23,1953    |
| **APC**      | 0,9251    | 17,7265    | 1,1331    | 17,8046    | 0,441     | 13,226     |
| **Xcache**   | 0,832     | 17,9140    | 0,9731    | 17,0468    | 0,439     | 13,4531    |
| **Void**     | 0,147     | 17,3593    | 0,005     | 13,0390    | 0,174     | 13,1718    |

