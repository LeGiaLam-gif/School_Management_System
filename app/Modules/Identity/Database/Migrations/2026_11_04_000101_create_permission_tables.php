<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bang chuan cua spatie/laravel-permission (single-tenant, khong dung teams).
 * Doc ten bang/cot tu config('permission.*') da duoc package merge san.
 */
return new class extends Migration
{
    /**
     * @return array{tables: array<string, string>, columns: array<string, string>}
     */
    private function names(): array
    {
        return [
            'tables' => config('permission.table_names') ?: [
                'roles' => 'roles',
                'permissions' => 'permissions',
                'model_has_permissions' => 'model_has_permissions',
                'model_has_roles' => 'model_has_roles',
                'role_has_permissions' => 'role_has_permissions',
            ],
            'columns' => config('permission.column_names') ?: [
                'role_pivot_key' => null,
                'permission_pivot_key' => null,
                'model_morph_key' => 'model_id',
            ],
        ];
    }

    public function up(): void
    {
        ['tables' => $tables, 'columns' => $columns] = $this->names();
        $pivotRole = $columns['role_pivot_key'] ?? 'role_id';
        $pivotPermission = $columns['permission_pivot_key'] ?? 'permission_id';
        $morphKey = $columns['model_morph_key'] ?? 'model_id';

        Schema::create($tables['permissions'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create($tables['roles'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create($tables['model_has_permissions'], function (Blueprint $table) use ($tables, $pivotPermission, $morphKey) {
            $table->unsignedBigInteger($pivotPermission);
            $table->string('model_type');
            $table->unsignedBigInteger($morphKey);
            $table->index([$morphKey, 'model_type'], 'model_has_permissions_model_id_model_type_index');

            $table->foreign($pivotPermission)
                ->references('id')
                ->on($tables['permissions'])
                ->onDelete('cascade');

            $table->primary(
                [$pivotPermission, $morphKey, 'model_type'],
                'model_has_permissions_permission_model_type_primary'
            );
        });

        Schema::create($tables['model_has_roles'], function (Blueprint $table) use ($tables, $pivotRole, $morphKey) {
            $table->unsignedBigInteger($pivotRole);
            $table->string('model_type');
            $table->unsignedBigInteger($morphKey);
            $table->index([$morphKey, 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign($pivotRole)
                ->references('id')
                ->on($tables['roles'])
                ->onDelete('cascade');

            $table->primary(
                [$pivotRole, $morphKey, 'model_type'],
                'model_has_roles_role_model_type_primary'
            );
        });

        Schema::create($tables['role_has_permissions'], function (Blueprint $table) use ($tables, $pivotRole, $pivotPermission) {
            $table->unsignedBigInteger($pivotPermission);
            $table->unsignedBigInteger($pivotRole);

            $table->foreign($pivotPermission)
                ->references('id')
                ->on($tables['permissions'])
                ->onDelete('cascade');

            $table->foreign($pivotRole)
                ->references('id')
                ->on($tables['roles'])
                ->onDelete('cascade');

            $table->primary([$pivotPermission, $pivotRole], 'role_has_permissions_permission_id_role_id_primary');
        });

        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    public function down(): void
    {
        ['tables' => $tables] = $this->names();

        Schema::drop($tables['role_has_permissions']);
        Schema::drop($tables['model_has_roles']);
        Schema::drop($tables['model_has_permissions']);
        Schema::drop($tables['roles']);
        Schema::drop($tables['permissions']);
    }
};
