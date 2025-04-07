<?php

use App\Models\Gallery;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('albums', static function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Gallery::class, 'gallery_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('description');
            $table->string('slug');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('albums');
    }
};
