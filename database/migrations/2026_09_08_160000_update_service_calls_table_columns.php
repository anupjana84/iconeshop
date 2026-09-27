<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('service_calls')) {
            Schema::table('service_calls', function (Blueprint $table) {
                if (!Schema::hasColumn('service_calls', 'bill_date')) {
                    $table->date('bill_date')->nullable()->after('status');
                }
                if (!Schema::hasColumn('service_calls', 'product_id')) {
                    $table->unsignedBigInteger('product_id')->nullable()->after('bill_date');
                }
                if (!Schema::hasColumn('service_calls', 'sl_no')) {
                    $table->string('sl_no')->nullable()->after('product_id');
                }
                if (!Schema::hasColumn('service_calls', 'product_name')) {
                    $table->string('product_name')->nullable()->after('sl_no');
                }
                if (!Schema::hasColumn('service_calls', 'serial_no')) {
                    $table->string('serial_no')->nullable()->after('product_name');
                }
                if (!Schema::hasColumn('service_calls', 'product_id_2')) {
                    $table->unsignedBigInteger('product_id_2')->nullable()->after('serial_no');
                }
                if (!Schema::hasColumn('service_calls', 'sl_no_2')) {
                    $table->string('sl_no_2')->nullable()->after('product_id_2');
                }
                if (!Schema::hasColumn('service_calls', 'product_name_2')) {
                    $table->string('product_name_2')->nullable()->after('sl_no_2');
                }
                if (!Schema::hasColumn('service_calls', 'serial_no_2')) {
                    $table->string('serial_no_2')->nullable()->after('product_name_2');
                }
                if (!Schema::hasColumn('service_calls', 'case_id_1')) {
                    $table->string('case_id_1')->nullable()->after('serial_no_2');
                }
                if (!Schema::hasColumn('service_calls', 'case_id_2')) {
                    $table->string('case_id_2')->nullable()->after('case_id_1');
                }
                if (!Schema::hasColumn('service_calls', 'invoice_image')) {
                    $table->longText('invoice_image')->nullable()->after('case_id_2');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('service_calls')) {
            Schema::table('service_calls', function (Blueprint $table) {
                $columns = [
                    'bill_date', 'product_id', 'sl_no', 'product_name', 'serial_no',
                    'product_id_2', 'sl_no_2', 'product_name_2', 'serial_no_2',
                    'case_id_1', 'case_id_2', 'invoice_image'
                ];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('service_calls', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
