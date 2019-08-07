<?php
declare(strict_types=1);

namespace Gaming\Tests\Unit\Common\ObjectMapper\Collection;

use Gaming\Common\ObjectMapper\Collection\SplFixedArrayMapper;
use Gaming\Common\ObjectMapper\Mapper;
use PHPUnit\Framework\TestCase;

final class SplFixedArrayMapperTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldSerialize(): void
    {
        $expected = ['a', 'a', 'a'];
        $innerMapper = $this->createMock(Mapper::class);
        $innerMapper
            ->expects($this->exactly(3))
            ->method('serialize')
            ->willReturn('a');

        /** @var Mapper $innerMapper */
        $arrayMapper = new SplFixedArrayMapper($innerMapper);
        $serialized = $arrayMapper->serialize(
            \SplFixedArray::fromArray([1, 2, 3])
        );

        $this->assertSame($expected, $serialized);
    }

    /**
     * @test
     */
    public function itShouldDeserialize(): void
    {
        $expected = \SplFixedArray::fromArray(['a', 'a', 'a']);
        $innerMapper = $this->createMock(Mapper::class);
        $innerMapper
            ->expects($this->exactly(3))
            ->method('deserialize')
            ->willReturn('a');

        /** @var Mapper $innerMapper */
        $arrayMapper = new SplFixedArrayMapper($innerMapper);
        $serialized = $arrayMapper->deserialize([1, 2, 3]);

        $this->assertEquals($expected, $serialized);
    }
}
