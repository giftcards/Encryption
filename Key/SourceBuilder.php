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
use Giftcards\Encryption\Key\Factory\ContainerParametersSourceBuilder;
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
                new ArraySourceBuilder(),
                new ContainerParametersSourceBuilder()
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
            $mainSource->add(new CircularGuardSource(new FallbackSource($this->fallbacks, $mainSource)));
        }

        if ($this->map) {
            $mainSource->add(new CircularGuardSource(new MappingSource($this->map, $mainSource)));
        }

        if ($this->combined) {
            $mainSource->add(new CircularGuardSource(new CombiningSource($this->combined, $mainSource)));
        }
        
        foreach ($this->sources as $source) {
            $mainSource->add($source);
        }
        
        if ($this->cache) {
            $mainSource = new CachingSource($this->cache, $mainSource);
        }

        return $mainSource;
    }

    /**
     * @param $source
     * @param array $options
     * @param null $prefix
     * @param bool|false $addCircularGuard
     * @return $this
     *
     * Add a new key source either an instance of the interface or a name and options. i nal cases a prefix can
     * be given and a boollean stating if they hsould be wrapped in a circular guard
     */
    public function add(
        $source,
        array $options = array(),
        $prefix = null,
        $addCircularGuard = false
    ) {
        if (!$source instanceof SourceInterface) {
            if (!$prefix && $prefix !== false) {
                $prefix = $source;
            }

            $source = $this->factory->create($source, $options);
        }

        if ($prefix) {
            $source = new PrefixKeyNameSource($prefix, $source);
        }
        
        if ($addCircularGuard) {
            $source = new CircularGuardSource($source);
        }

        $this->sources[] = $source;
        return $this;
    }

    /**
     * @param $name
     * @param $fallbackName
     * @return $this
     *
     * add a fallback for a certain key name if it doesnt exist
     */
    public function addFallback($name, $fallbackName)
    {
        if (!isset($this->fallbacks[$name])) {
            $this->fallbacks[$name] = array();
        }
        $this->fallbacks[$name][] = $fallbackName;
        return $this;
    }

    /**
     * @param string $name name to map from
     * @param string $mappedName name to map to
     * @return $this
     *
     * add a map for a certain key name to another key name
     */
    public function map($name, $mappedName)
    {
        $this->map[$name] = $mappedName;
        return $this;
    }

    /**
     * @param string $left the name of the ey to use for the left side of the combined key
     * @param string $right the name of the ey to use for the right side of the combined key
     * @param string $name
     * @return $this
     *
     * add a new combined key made up of 3 other keys named by $left and $right
     */
    public function combine($left, $right, $name)
    {
        $this->combined[$name] = array(
            CombiningSource::LEFT => $left,
            CombiningSource::RIGHT => $right
        );
        return $this;
    }

    /**
     * @return $this
     *
     * adds the none source so that the key named 'none' will be available
     */
    public function includeNone()
    {
        $this->sources[] = new NoneSource();
        return $this;
    }

    /**
     * @param Cache|null $cache
     * @return $this
     *
     * turns on key caching you can optilnailly pass a doctrine cache
     * impl uf you dont just want to use an in memory cache impl
     */
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
