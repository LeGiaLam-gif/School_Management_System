<?php

namespace Tests\Unit\Academic;

use App\Modules\Academic\Support\PrerequisiteCycleDetector;
use PHPUnit\Framework\TestCase;

class PrerequisiteCycleDetectorTest extends TestCase
{
    public function test_self_prerequisite_is_a_cycle(): void
    {
        $this->assertTrue((new PrerequisiteCycleDetector)->wouldCreateCycle([], 1, [1]));
    }

    public function test_direct_cycle(): void
    {
        // 2 can 1; gan 1 can 2 => A can B, B can A.
        $graph = [2 => [1]];

        $this->assertTrue((new PrerequisiteCycleDetector)->wouldCreateCycle($graph, 1, [2]));
    }

    public function test_indirect_cycle(): void
    {
        // 3 can 2, 2 can 1; gan 1 can 3 => vong 1 -> 3 -> 2 -> 1.
        $graph = [3 => [2], 2 => [1]];

        $this->assertTrue((new PrerequisiteCycleDetector)->wouldCreateCycle($graph, 1, [3]));
    }

    public function test_chain_without_cycle(): void
    {
        $graph = [2 => [1]];

        $this->assertFalse((new PrerequisiteCycleDetector)->wouldCreateCycle($graph, 3, [2]));
    }

    public function test_diamond_is_not_a_cycle(): void
    {
        // 4 can 2 va 3; 2 can 1; 3 can 1.
        $graph = [2 => [1], 3 => [1]];

        $this->assertFalse((new PrerequisiteCycleDetector)->wouldCreateCycle($graph, 4, [2, 3]));
    }

    public function test_replacing_old_edges_can_remove_a_would_be_cycle(): void
    {
        // Hien 1 can 2. Thay tap tien quyet cua 1 thanh [3] (3 khong dan ve 1) => khong vong.
        $graph = [1 => [2], 3 => []];

        $this->assertFalse((new PrerequisiteCycleDetector)->wouldCreateCycle($graph, 1, [3]));
    }

    public function test_empty_prerequisites_never_cycle(): void
    {
        $this->assertFalse((new PrerequisiteCycleDetector)->wouldCreateCycle([2 => [1]], 1, []));
    }
}
