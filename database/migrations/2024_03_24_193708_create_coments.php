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
        Schema::create('coments', function (Blueprint $table) {
            $table->id();
            $table->string("coments");
            $table->unsignedBigInteger('blog_id')->unsigned();
            $table->unsignedBigInteger('user_id')->unsigned();
            $table->timestamps();
            $table->foreign('blog_id')->references('id')->on('blogs');
            $table->foreign('user_id')->references('id')->on('users');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coments');
    }
};
