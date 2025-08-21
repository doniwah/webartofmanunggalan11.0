<?php

// Buat migration baru:
// php artisan make:migration add_ticket_id_to_transactions_table

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('ticket_id')->nullable()->after('admin_fee');
            
            // Jika Anda ingin menambahkan foreign key constraint:
            // $table->foreign('ticket_id')->references('idTicket')->on('ticket')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // $table->dropForeign(['ticket_id']); // Uncomment jika ada foreign key
            $table->dropColumn('ticket_id');
        });
    }
};