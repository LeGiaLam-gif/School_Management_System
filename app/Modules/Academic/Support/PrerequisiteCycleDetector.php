<?php

namespace App\Modules\Academic\Support;

/**
 * Phat hien vong lap dieu kien tien quyet (muc 6.3): A can B, B can A.
 * Lop thuan (khong phu thuoc framework/DB) de test don vi nhanh.
 */
class PrerequisiteCycleDetector
{
    /**
     * Gan tap tien quyet moi cho $courseId co tao vong lap khong?
     *
     * @param  array<int, list<int>>  $graph  ban do hien co: hoc phan => cac tien quyet
     * @param  list<int>  $newPrerequisiteIds  tap tien quyet moi cua $courseId (thay the tap cu)
     */
    public function wouldCreateCycle(array $graph, int $courseId, array $newPrerequisiteIds): bool
    {
        if (in_array($courseId, $newPrerequisiteIds, true)) {
            return true;
        }

        // Co vong lap khi tu cac tien quyet moi co the di nguoc ve lai $courseId.
        $graph[$courseId] = $newPrerequisiteIds;
        $visited = [];
        $stack = $newPrerequisiteIds;

        while ($stack !== []) {
            $node = array_pop($stack);

            if ($node === $courseId) {
                return true;
            }

            if (isset($visited[$node])) {
                continue;
            }

            $visited[$node] = true;

            foreach ($graph[$node] ?? [] as $prerequisite) {
                $stack[] = $prerequisite;
            }
        }

        return false;
    }
}
