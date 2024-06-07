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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('TeacherUserID');
            $table->integer('TeacherName');
            $table->string('TeacherDOB');
            $table->string('TeacherCNIC');
            $table->string('TeacherPhoneNumber');
            $table->string('TeacherHomeAddress');
            $table->string('TeacherReligion');
            $table->string('TeacherSalary');
            $table->boolean('TeacherSalaryPaid');
            $table->timestamps();
            $table->foreign('TeacherUserID')
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
