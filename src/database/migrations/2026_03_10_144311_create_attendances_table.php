<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->timestamp('clock_in');
            // 出勤時刻（必須）
            $table->timestamp('clock_out')->nullable();
            // 退勤時刻（任意）
            $table->date('date');
            // 勤務日
            $table->text('note')->nullable();
            // 備考

            $table->tinyInteger('status')->default(0);
            // 0: 勤務外、1: 出勤中、2: 休憩中、3: 退勤済、4: 承認待ち、5: 承認済

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
