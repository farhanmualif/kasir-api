<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // First, let's drop the foreign key if it still exists
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE CONSTRAINT_SCHEMA = '" . env('DB_DATABASE') . "'
            AND TABLE_NAME = 'discounts'
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ");

        foreach ($foreignKeys as $foreignKey) {
            Schema::table('discounts', function (Blueprint $table) use ($foreignKey) {
                $table->dropForeign($foreignKey->CONSTRAINT_NAME);
            });
        }

        // Now check if columns exist before trying to add or drop them
        $columns = Schema::getColumnListing('discounts');

        Schema::table('discounts', function (Blueprint $table) use ($columns) {
            // Drop columns if they still exist
            if (in_array('product_id', $columns)) {
                $table->dropColumn('product_id');
            }

            if (in_array('discount', $columns)) {
                $table->dropColumn('discount');
            }

            // Add columns if they don't exist
            if (!in_array('name', $columns)) {
                $table->string('name');
            }

            if (!in_array('persentase', $columns)) {
                $table->decimal('persentase', 5, 2);
            }

            if (!in_array('description', $columns)) {
                $table->text('description')->nullable();
            }

            if (!in_array('created_at', $columns)) {
                $table->timestamp('created_at')->nullable();
            }

            if (!in_array('updated_at', $columns)) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('discounts', function (Blueprint $table) {
            // Revert changes in case of rollback
            $table->unsignedBigInteger('product_id')->nullable();
            $table->decimal('discount', 8, 2)->nullable();

            $table->dropColumn('name');
            $table->dropColumn('persentase');
            $table->dropColumn('description');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
};
