<?php

use App\Models\Gallery;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('images', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Gallery::class, 'gallery_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('caption');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
