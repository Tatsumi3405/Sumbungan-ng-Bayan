<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('grievances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('user_name');
            $table->string('title');
            $table->text('description');
            $table->string('category')->default('other');
            $table->string('status')->default('pending');
            $table->string('address')->default('');
            // Photo is stored in the DB (base64) so it survives hosts with an
            // ephemeral disk such as Render's free tier.
            $table->boolean('has_image')->default(false);
            $table->string('image_mime', 50)->nullable();
            $table->longText('image_data')->nullable();
            $table->text('admin_feedback')->nullable();
            $table->string('responding_sector')->nullable();
            $table->unsignedInteger('message_count')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('status');
        });

        Schema::create('grievance_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grievance_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('sender_id');
            $table->string('sender_name');
            $table->string('sender_role', 20);
            $table->text('text');
            $table->timestamps();

            $table->index(['grievance_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grievance_messages');
        Schema::dropIfExists('grievances');
    }
};
