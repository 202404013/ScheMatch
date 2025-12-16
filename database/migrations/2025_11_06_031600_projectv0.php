<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_types', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_admin');
            $table->boolean('is_registered');
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('school_year');
            $table->string('semester');
            $table->string('class_code');
            $table->string('subject');
            $table->string('section');
            $table->string('professor');
            $table->timestamps();
            $table->softDeletes();  
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });


        Schema::create('calendars', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('time');
            $table->string('section');  
            $table->foreignId('course_id')->constrained();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('friends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('friend_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::create('availabilities', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');    
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('visibility', ['public', 'friends', 'private'])->default('public');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('shared_with', function (Blueprint $table) {
            $table->id();
            $table->foreignId('availability_id')->constrained();  
            $table->foreignId('friend_id')->constrained();  
            $table->timestamps();
            $table->softDeletes();
        });


    }

    public function down(): void
    {
        Schema::dropIfExists('user_types');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('calendars');
        Schema::dropIfExists('friends');
        Schema::dropIfExists('availabilities');
        Schema::dropIfExists('shared_with');
    }
};
