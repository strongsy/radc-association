<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('replies', static function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('mail_id'); // Foreign key for mails
            $table->unsignedBigInteger('user_id')->nullable(); // Foreign key for users (optional)
            $table->string('subject');
            $table->text('message'); // Reply content
            $table->timestamps();
            $table->softDeletes();

            // Define foreign keys
            $table->foreign('mail_id')->references('id')->on('mails')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null'); // Optional


        });
    }

    public function down(): void
    {
        Schema::dropIfExists('replies');
    }
};
