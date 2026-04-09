<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_providers', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
            $table->string('driver', 32);
            $table->boolean('is_enabled')->default(true);
            $table->text('api_key_encrypted')->nullable();
            $table->string('api_key_hint', 8)->nullable();
            $table->string('default_model')->nullable();
            $table->string('base_url')->nullable();
            $table->unsignedSmallInteger('timeout_seconds')->default(60);
            $table->unsignedSmallInteger('priority')->default(100);
            $table->unsignedInteger('max_output_tokens')->nullable();
            $table->json('rate_limit_json')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['is_enabled', 'priority']);
        });

        Schema::table('ai_agents', function (Blueprint $table): void {
            $table->foreignId('default_ai_provider_id')->nullable()->after('model_id')->constrained('ai_providers')->nullOnDelete();
            $table->string('role_label')->nullable()->after('instructions');
            $table->string('business_name')->nullable();
            $table->text('business_description')->nullable();
            $table->text('business_goals')->nullable();
            $table->string('tone')->nullable();
            $table->string('response_style')->nullable();
            $table->json('languages')->nullable();
            $table->text('forbidden_topics')->nullable();
            $table->json('lead_capture_schema')->nullable();
            $table->json('escalation_rules')->nullable();
            $table->json('availability')->nullable();
            $table->string('focus', 32)->default('general');
        });

        Schema::create('leads', function (Blueprint $table): void {
            $table->id();
            $table->string('source', 32)->default('ai_chat');
            $table->string('status', 32)->default('new');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('budget')->nullable();
            $table->string('service_interest')->nullable();
            $table->text('notes')->nullable();
            $table->json('custom_fields')->nullable();
            $table->text('conversation_summary')->nullable();
            $table->text('internal_notes')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
            $table->index('email');
            $table->index('created_at');
        });

        Schema::create('ai_conversations', function (Blueprint $table): void {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('ai_agent_id')->constrained('ai_agents')->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->string('channel', 16)->default('web');
            $table->string('visitor_key_hash', 64)->nullable();
            $table->string('status', 32)->default('open');
            $table->timestamp('last_message_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['ai_agent_id', 'last_message_at']);
            $table->index('lead_id');
        });

        Schema::table('leads', function (Blueprint $table): void {
            $table->foreignId('ai_conversation_id')->nullable()->after('assigned_to')->constrained('ai_conversations')->nullOnDelete();
        });

        Schema::create('ai_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ai_conversation_id')->constrained('ai_conversations')->cascadeOnDelete();
            $table->string('role', 16);
            $table->longText('content');
            $table->string('provider_driver', 32)->nullable();
            $table->unsignedInteger('prompt_tokens')->nullable();
            $table->unsignedInteger('completion_tokens')->nullable();
            $table->timestamps();

            $table->index(['ai_conversation_id', 'created_at']);
        });

        Schema::create('activity_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 128);
            $table->nullableMorphs('subject');
            $table->json('properties')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['action', 'created_at']);
            $table->index('created_at');
        });

        Schema::create('platform_security_settings', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('chat_rate_limit_per_minute')->default(20);
            $table->unsignedInteger('chat_rate_limit_per_day')->default(200);
            $table->text('internal_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_messages');
        Schema::table('leads', function (Blueprint $table): void {
            $table->dropForeign(['ai_conversation_id']);
            $table->dropColumn('ai_conversation_id');
        });
        Schema::dropIfExists('ai_conversations');
        Schema::dropIfExists('leads');
        Schema::table('ai_agents', function (Blueprint $table): void {
            $table->dropForeign(['default_ai_provider_id']);
            $table->dropColumn([
                'default_ai_provider_id',
                'role_label',
                'business_name',
                'business_description',
                'business_goals',
                'tone',
                'response_style',
                'languages',
                'forbidden_topics',
                'lead_capture_schema',
                'escalation_rules',
                'availability',
                'focus',
            ]);
        });
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('platform_security_settings');
        Schema::dropIfExists('ai_providers');
    }
};
