<?php

declare(strict_types=1);

namespace Koriym\Attributes;

use Koriym\Attributes\Annotation\Cacheable;
use Koriym\Attributes\Annotation\HttpCache;
use Koriym\Attributes\Annotation\Inject;
use Koriym\Attributes\Annotation\Loggable;
use Koriym\Attributes\Annotation\PaidMemberOnly;
use Koriym\Attributes\Annotation\Transactional;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

use function array_map;
use function assert;
use function class_exists;
use function get_class;

class AttributesTest extends TestCase
{
    protected AttributesReader $reader;

    protected function setUp(): void
    {
        $this->reader = new AttributesReader();
    }

    public function testIsInstanceOfAttributes(): void
    {
        $actual = $this->reader;
        $this->assertInstanceOf(AttributesReader::class, $actual);
    }

    public function testGetClassAnnotationItem(): void
    {
        $class = new ReflectionClass(Fake::class);
        $annotationName = Cacheable::class;
        assert(class_exists($annotationName));
        $cacheable = $this->reader->getClassAnnotation($class, $annotationName);
        $this->assertInstanceOf(Cacheable::class, $cacheable);
    }

    public function testGetClassAnnotationList(): void
    {
        $class = new ReflectionClass(Fake::class);
        $attributes = $this->reader->getClassAnnotations($class);
        $actural = array_map(static function (object $attribute): string {
            return get_class($attribute);
        }, $attributes);
        $expected = [PaidMemberOnly::class, Cacheable::class];
        $this->assertEqualsCanonicalizing($expected, $actural);
    }

    public function testGetMethodAnnotationItem(): void
    {
        $method = new ReflectionMethod(Fake::class, 'subscribe');
        $annotationName = HttpCache::class;
        assert(class_exists($annotationName));
        $cacheable = $this->reader->getMethodAnnotation($method, $annotationName);
        $this->assertInstanceOf($annotationName, $cacheable);
    }

    public function testGetMethodAnnotation(): void
    {
        $method = new ReflectionMethod(Fake::class, 'subscribe');
        $attributes = $this->reader->getMethodAnnotations($method);
        $actural = array_map(static function (object $attribute): string {
            return get_class($attribute);
        }, $attributes);
        $expected = [Loggable::class, HttpCache::class, Transactional::class];
        $this->assertEqualsCanonicalizing($expected, $actural);
    }

    public function testGetPropertyAnnotationItem(): void
    {
        $prop = new ReflectionProperty(Fake::class, 'prop');
        $annotationName = Inject::class;
        assert(class_exists($annotationName));
        $cacheable = $this->reader->getPropertyAnnotation($prop, $annotationName);
        $this->assertInstanceOf($annotationName, $cacheable);
    }

    public function testGetPropertyAnnotationList(): void
    {
        $prop = new ReflectionProperty(Fake::class, 'prop');
        $attributes = $this->reader->getPropertyAnnotations($prop);
        $actural = array_map(static function (object $attribute): string {
            return get_class($attribute);
        }, $attributes);
        $expected = [Inject::class, PaidMemberOnly::class];
        $this->assertEqualsCanonicalizing($expected, $actural);
    }
}
