<?php

declare(strict_types=1);

namespace Jeremeamia\Slack\BlockKit\Tests\Surfaces;

use Jeremeamia\Slack\BlockKit\Blocks\Section;
use Jeremeamia\Slack\BlockKit\Blocks\Virtual\TwoColumnTable;
use Jeremeamia\Slack\BlockKit\Blocks\Virtual\VirtualBlock;
use Jeremeamia\Slack\BlockKit\Surfaces\Surface;
use Jeremeamia\Slack\BlockKit\Tests\TestCase;
use Jeremeamia\Slack\BlockKit\Type;

/**
 * @covers \Jeremeamia\Slack\BlockKit\Surfaces\Surface
 */
class SurfaceTest extends TestCase
{
    public function testCanAddSingleBlocks()
    {
        $surface = $this->getMockSurface();

        $dummyBlock = new Section();
        $surface->add($dummyBlock);
        $surface->add($dummyBlock);
        $surface->add($dummyBlock);

        $blocks = $surface->getBlocks();
        $this->assertCount(3, $blocks);
        foreach ($blocks as $block) {
            $this->assertSame($dummyBlock, $block);
        }
    }

    public function testCanAddVirtualBlocks()
    {
        $surface = $this->getMockSurface();

        $dummyBlock = new Section();
        for ($i = 0; $i < 3; $i++) {
            $virtualBlock = $this->getMockVirtualBlock();
            for ($j = 0; $j < 3; $j++) {
                $virtualBlock->add($dummyBlock);
            }
            $surface->add($virtualBlock);
        }

        $blocks = $surface->getBlocks();
        $this->assertCount(9, $blocks);
        foreach ($blocks as $block) {
            $this->assertSame($dummyBlock, $block);
        }
    }

    public function testCanAddVirtualBlockEarlyOrLateAndBlockCountIsTheSame()
    {
        $rows = [
            'a' => '1',
            'b' => '2',
            'c' => '3',
        ];

        $surface1 = $this->getMockSurface();
        $table = (new TwoColumnTable())
            ->caption('Hello, World!')
            ->cols('Foo', 'Bar')
            ->rows($rows);
        $surface1->add($table);
        $this->assertCount(4, $surface1->getBlocks());

        $surface2 = $this->getMockSurface();
        $surface2->newTwoColumnTable()
            ->caption('Hello, World!')
            ->cols('Foo', 'Bar')
            ->rows($rows);
        $this->assertCount(4, $surface2->getBlocks());
    }
}
