<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/31/15
 * Time: 9:06 PM
 */

namespace Giftcards\Encryption\Key;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\Cache;
use Giftcards\Encryption\Factory\Factory;
use Giftcards\Encryption\Key\Factory\ArraySourceBuilder;
use Giftcards\Encryption\Key\Factory\IniFileSourceBuilder;
use Giftcards\Encryption\Key\Factory\MongoSourceBuilder;
use Giftcards\Encryption\Key\Factory\VaultSourceBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SourceBuilder
{
    /** @var  Factory */
    protected $factory;
    /** @var  SourceInterface[] */
    protected $sources = array();
    protected $map = array();
    protected $fallbacks = array();
    protected $combined = array();
    protected $cache;

    public static function newInstance()
    {
        return new static(new Factory(
            'Giftcards\Encryption\Key\SourceInterface',
            array(
                new VaultSourceBuilder(),
                new MongoSourceBuilder(),
                new IniFileSourceBuilder(),
                new ArraySourceBuilder()
            )
        ));
    }

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    public function build()
    {
        $mainSource = new ChainSource();
        
        if ($this->fallbacks) {
            $mainSource->add(new FallbackSource($this->fallbacks, $mainSource));
        }

        if ($this->map) {
            $mainSource->add(new MappingSource($this->map, $mainSource));
        }

        if ($this->combined) {
            $mainSource->add(new CombiningSource($this->combined, $mainSource));
        }
        
        foreach ($this->sources as $source) {
            $mainSource->add($source);
        }
        
        if ($this->cache) {
            $mainSource = new CachingSource($this->cache, $mainSource);
        }

        return $mainSource;
    }

    public function add($source, array $options = array(), $prefix = null)
    {
        if (!$source instanceof SourceInterface) {
            if (!$prefix && $prefix !== false) {
                $prefix = $source;
            }

            $source = $this->factory->create($source, $options);
        }

        if ($prefix) {
            $source = new PrefixKeyNameSource($prefix, $source);
        }

        $this->sources[] = $source;
        return $this;
    }

    public function addFallback($name, $fallbackName)
    {
        if (!isset($this->fallbacks[$name])) {
            $this->fallbacks[$name] = array();
        }
        $this->fallbacks[$name][] = $fallbackName;
        return $this;
    }

    public function map($name, $mappedName)
    {
        $this->map[$name] = $mappedName;
        return $this;
    }

    public function combine($left, $right, $name)
    {
        $this->combined[$name] = array(
            CombiningSource::LEFT => $left,
            CombiningSource::RIGHT => $right
        );
        return $this;
    }

    public function includeNone()
    {
        $this->sources[] = new NoneSource();
        return $this;
    }

    public function cache(Cache $cache = null)
    {
        $this->cache = $cache ?: new ArrayCache();
        return $this;
    }

    public function getFactory()
    {
        return $this->factory;
    }
}
