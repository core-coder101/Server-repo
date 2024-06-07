<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('StudentCNIC');
            $table->integer('StudentClassID');
            $table->integer('StudentUserID');
            $table->string('StudentGender');
            $table->integer('StudentTeacherID');
            $table->string('StudentDOB');
            $table->boolean('StudentAdmissionApproval');
            $table->string('StudentPhoneNumber');
            $table->string('StudentHomeAddress');
            $table->string('StudentMonthlyFee');
            $table->string('StudentReligion');
            $table->timestamps();
            $table->foreign('StudentUserID')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
