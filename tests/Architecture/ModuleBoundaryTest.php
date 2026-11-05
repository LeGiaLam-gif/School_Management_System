<?php

namespace Tests\Architecture;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Kiem tra quy tac ranh gioi module (muc 5.6, 6.1) bang cach quet `use` trong ma nguon.
 *
 * - Module khong duoc dung Controllers/Requests/Policies/Jobs/Listeners/Queries/Routes cua module khac.
 * - Chi duoc goi Service/Support/Contracts cua module nam trong danh sach phu thuoc cho phep.
 * - Model/Event/Provider... cua module khac duoc phep (ngoai le co chu dich: quan he Eloquent toi model loi,
 *   giao tiep qua Event).
 * - Shared khong duoc phu thuoc Controller/Service cua module.
 */
class ModuleBoundaryTest extends TestCase
{
    /**
     * Phu thuoc Service/Support/Contracts cho phep: module => cac module no duoc goi.
     *
     * @var array<string, list<string>>
     */
    private const ALLOWED = [
        'Identity' => [],
        'Academic' => ['Identity'],
        'Teaching' => ['Academic', 'Identity'],
        'Enrollment' => ['Teaching', 'Academic', 'Identity', 'Grading'], // Grading chi qua Contracts (kiem tra rieng)
        'Attendance' => ['Enrollment', 'Teaching', 'Identity'],
        'Grading' => ['Enrollment', 'Teaching', 'Academic', 'Identity'],
        'Announcement' => ['Teaching', 'Enrollment', 'Identity'],
        'Reporting' => ['Identity', 'Academic', 'Teaching', 'Enrollment', 'Attendance', 'Grading', 'Announcement'],
    ];

    /** Tang noi bo, khong module nao khac duoc dung. */
    private const PRIVATE_LAYERS = ['Controllers', 'Requests', 'Policies', 'Jobs', 'Listeners', 'Queries', 'Resources', 'Middleware', 'Providers', 'Database'];

    /** Tang dich vu: chi dung khi co trong ALLOWED. */
    private const SERVICE_LAYERS = ['Services', 'Support', 'Contracts'];

    /**
     * @return array<string, list<array{file: string, module: string, target: string, layer: string}>>
     */
    private function uses(): array
    {
        $base = dirname(__DIR__, 2).'/app/Modules';
        $found = [];

        foreach (self::ALLOWED as $module => $_) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator("{$base}/{$module}", RecursiveDirectoryIterator::SKIP_DOTS));

            foreach ($iterator as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                preg_match_all('/^use App\\\\Modules\\\\(\w+)\\\\(\w+)\\\\/m', (string) file_get_contents($file->getPathname()), $matches, PREG_SET_ORDER);

                foreach ($matches as [, $target, $layer]) {
                    if ($target !== $module) {
                        $found[$module][] = [
                            'file' => str_replace(dirname(__DIR__, 2).'/', '', $file->getPathname()),
                            'module' => $module,
                            'target' => $target,
                            'layer' => $layer,
                        ];
                    }
                }
            }
        }

        return $found;
    }

    public function test_modules_do_not_use_private_layers_of_other_modules(): void
    {
        $violations = [];

        foreach ($this->uses() as $uses) {
            foreach ($uses as $use) {
                // Seeder demo duoc phep doc Model cua module khac; Database/Factories cua module khac van la tang rieng.
                if (in_array($use['layer'], self::PRIVATE_LAYERS, true) && ! $this->isAllowedDatabaseUse($use)) {
                    $violations[] = "{$use['file']} dùng {$use['target']}\\{$use['layer']}";
                }
            }
        }

        $this->assertSame([], $violations, "Vi phạm ranh giới module:\n".implode("\n", $violations));
    }

    /**
     * Factory/Seeder cua module khac chi duoc goi tu Database/ (seeder demo goi seeder demo, factory goi factory).
     *
     * @param  array{file: string, module: string, target: string, layer: string}  $use
     */
    private function isAllowedDatabaseUse(array $use): bool
    {
        return $use['layer'] === 'Database'
            && (str_contains($use['file'], '/Database/Factories/') || str_contains($use['file'], '/Database/Seeders/'));
    }

    public function test_modules_only_call_services_of_allowed_dependencies(): void
    {
        $violations = [];

        foreach ($this->uses() as $module => $uses) {
            foreach ($uses as $use) {
                if (! in_array($use['layer'], self::SERVICE_LAYERS, true)) {
                    continue;
                }

                if (! in_array($use['target'], self::ALLOWED[$module], true)) {
                    $violations[] = "{$use['file']} dùng {$use['target']}\\{$use['layer']} (không nằm trong phụ thuộc cho phép của {$module})";
                }
            }
        }

        $this->assertSame([], $violations, "Vi phạm chiều phụ thuộc:\n".implode("\n", $violations));
    }

    public function test_enrollment_reaches_grading_only_through_contracts(): void
    {
        $violations = [];

        foreach ($this->uses()['Enrollment'] ?? [] as $use) {
            if ($use['target'] === 'Grading' && ! in_array($use['layer'], ['Contracts', 'Models'], true)) {
                $violations[] = "{$use['file']} dùng Grading\\{$use['layer']}";
            }
        }

        $this->assertSame([], $violations, implode("\n", $violations));
    }

    public function test_shared_does_not_depend_on_module_services_or_controllers(): void
    {
        $violations = [];
        $base = dirname(__DIR__, 2).'/app/Shared';
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, RecursiveDirectoryIterator::SKIP_DOTS));

        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            preg_match_all('/^use App\\\\Modules\\\\(\w+)\\\\(\w+)\\\\/m', (string) file_get_contents($file->getPathname()), $matches, PREG_SET_ORDER);

            foreach ($matches as [, $target, $layer]) {
                // Shared chi duoc tham chieu Model loi (vd AuditLog -> User).
                if ($layer !== 'Models') {
                    $violations[] = "{$file->getPathname()} dùng {$target}\\{$layer}";
                }
            }
        }

        $this->assertSame([], $violations, implode("\n", $violations));
    }

    public function test_every_module_has_a_provider_registered(): void
    {
        $providers = (string) file_get_contents(dirname(__DIR__, 2).'/bootstrap/providers.php');

        foreach (array_keys(self::ALLOWED) as $module) {
            $this->assertStringContainsString("{$module}ServiceProvider::class", $providers, "Module {$module} chưa đăng ký provider.");
            $this->assertFileExists(dirname(__DIR__, 2)."/app/Modules/{$module}/Providers/{$module}ServiceProvider.php");
        }
    }

    public function test_no_module_directory_is_missing_from_the_allowed_map(): void
    {
        $dirs = array_map('basename', glob(dirname(__DIR__, 2).'/app/Modules/*', GLOB_ONLYDIR));
        sort($dirs);

        $expected = array_keys(self::ALLOWED);
        sort($expected);

        $this->assertSame($expected, $dirs, 'Module mới phải được khai báo trong ModuleBoundaryTest::ALLOWED và docs/03-architecture/module-map.md.');
    }
}
