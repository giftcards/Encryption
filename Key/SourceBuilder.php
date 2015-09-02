<?php
/**
 * Created by PhpStorm.
 * User: jderay
 * Date: 8/31/15
 * Time: 9:06 PM
 */

namespace Omni\Encryption\Key;

use Omni\Encryption\Key\Factory\ArraySourceFactory;
use Omni\Encryption\Key\Factory\IniFileSourceFactory;
use Omni\Encryption\Key\Factory\MongoSourceFactory;
use Omni\Encryption\Key\Factory\SourceFactoryInterface;
use Omni\Encryption\Key\Factory\VaultSourceFactory;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SourceBuilder
{
    /** @var SourceFactoryInterface[] */
    protected $factories;
    /** @var  SourceInterface */
    protected $sources;
    protected $map = array();
    protected $fallbacks = array();
    protected $combined = array();
    
    public static function create()
    {
        return new static(array(
            new VaultSourceFactory(),
            new MongoSourceFactory(),
            new IniFileSourceFactory(),
            new ArraySourceFactory()
        ));
    }

    public function __construct(array $factories)
    {
        $this->factories = array_combine(array_map(function (SourceFactoryInterface $factory) {
            return $factory->getName();
        }, $factories), $factories);
    }

    public function build()
    {
        $mainSource = new ChainSource();

        foreach ($this->sources as $source) {
            $mainSource->add($source);
        }
        
        if ($this->fallbacks) {
            $mainSource = new FallbackSource($this->fallbacks, $mainSource);
        }
        
        if ($this->map) {
            $mainSource = new MappingSource($this->map, $mainSource);
        }
        
        if ($this->combined) {
            $mainSource = new CombiningSource($mainSource, $this->combined);
        }

        return $mainSource;
    }

    public function add($source, array $options = array(), $prefix = null)
    {
        if (!$source instanceof SourceInterface) {
            $source = $this->createSource($source, $options, $prefix);
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
        $this->combined[$name] = array(CombiningSource::LEFT => $left, CombiningSource::RIGHT => $right);
        return $this;
    }

    public function allowNone()
    {
        $this->sources[] = new NoneSource();
        return $this;
    }

    public function createSource($name, array $options, $prefix = null)
    {
        if (!isset($this->factories[$name])) {
            throw new \InvalidArgumentException(sprintf(
                'The key source factory named "%s" is not found.',
                $name
            ));
        }

        $resolver = new OptionsResolver();
        $this->factories[$name]->configureOptionsResolver($resolver);
        $source = $this->factories[$name]->build($resolver->resolve($options));
        
        if (!$prefix && $prefix !== false) {
            $prefix = $name;
        }
        
        if ($prefix) {
            $source = new PrefixKeyNameSource($prefix, $source);
        }
        
        return $source;
    }

    public function addFactory(SourceFactoryInterface $factory)
    {
        $this->factories[$factory->getName()] = $factory;
        return $this;
    }
}
