<?php

namespace App\Modules\Academic\Database\Seeders;

use App\Modules\Academic\Models\AdministrativeClass;
use App\Modules\Academic\Models\Program;
use App\Modules\Identity\Models\Lecturer;
use App\Modules\Identity\Models\Student;
use Illuminate\Database\Seeder;

class AdministrativeClassSeeder extends Seeder
{
    public function run(): void
    {
        $ktpm = Program::where('code', 'KTPM')->first();
        $httt = Program::where('code', 'HTTT')->first();
        $qtkd = Program::where('code', 'QTKD')->first();

        $lecturers = Lecturer::orderBy('id')->get();
        $gv1 = $lecturers->get(0);
        $gv2 = $lecturers->get(1);
        $gv3 = $lecturers->get(2);

        $classes = [
            [
                'code' => '22DTH01',
                'name' => 'Kỹ thuật phần mềm 2022 - Lớp 01',
                'program_id' => $ktpm?->id,
                'academic_year' => 2022,
                'advisor_lecturer_id' => $gv1?->id,
            ],
            [
                'code' => '22DTH02',
                'name' => 'Kỹ thuật phần mềm 2022 - Lớp 02',
                'program_id' => $ktpm?->id,
                'academic_year' => 2022,
                'advisor_lecturer_id' => $gv2?->id,
            ],
            [
                'code' => '23DTH01',
                'name' => 'Kỹ thuật phần mềm 2023 - Lớp 01',
                'program_id' => $ktpm?->id,
                'academic_year' => 2023,
                'advisor_lecturer_id' => $gv1?->id,
            ],
            [
                'code' => '22DHT01',
                'name' => 'Hệ thống thông tin 2022 - Lớp 01',
                'program_id' => $httt?->id,
                'academic_year' => 2022,
                'advisor_lecturer_id' => $gv3?->id,
            ],
            [
                'code' => '23DQT01',
                'name' => 'Quản trị kinh doanh 2023 - Lớp 01',
                'program_id' => $qtkd?->id,
                'academic_year' => 2023,
                'advisor_lecturer_id' => null,
            ],
        ];

        foreach ($classes as $c) {
            if ($c['program_id']) {
                $class = AdministrativeClass::firstOrCreate(
                    ['code' => $c['code']],
                    $c
                );
            }
        }

        // Gán lớp hành chính cho sinh viên mẫu tương ứng theo ngành và khóa
        $students = Student::all();
        $classMap = AdministrativeClass::all()->groupBy(fn ($item) => $item->program_id.'_'.$item->academic_year);

        foreach ($students as $student) {
            $key = $student->program_id.'_'.$student->cohort_year;
            if (isset($classMap[$key]) && $classMap[$key]->isNotEmpty()) {
                $targetClass = $classMap[$key]->first();
                $student->update(['class_id' => $targetClass->id]);
            }
        }
    }
}
