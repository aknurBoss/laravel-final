<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('notifications')) {
            return;
        }

        Schema::table('notifications', function (Blueprint $table) {
            if (! Schema::hasColumn('notifications', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
            }
            if (! Schema::hasColumn('notifications', 'type')) {
                $table->string('type', 64)->after('user_id');
            }
            if (! Schema::hasColumn('notifications', 'title')) {
                $table->string('title')->after('type');
            }
            if (! Schema::hasColumn('notifications', 'body')) {
                $table->text('body')->after('title');
            }
            if (! Schema::hasColumn('notifications', 'data')) {
                $table->json('data')->nullable()->after('body');
            }
            if (! Schema::hasColumn('notifications', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('data');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('notifications')) {
            return;
        }

        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            foreach (['type', 'title', 'body', 'data', 'is_read'] as $column) {
                if (Schema::hasColumn('notifications', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
