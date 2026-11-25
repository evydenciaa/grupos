<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('instances', function(Blueprint $t){
   $t->id(); $t->string('name'); $t->string('external_id',100)->unique(); $t->text('token'); $t->text('client_token');
   $t->string('phone',50)->nullable(); $t->boolean('connected')->default(false); $t->boolean('enabled')->default(true); $t->timestamp('synced_at')->nullable(); $t->timestamps();
  });
  Schema::create('whatsapp_groups', function(Blueprint $t){
   $t->id(); $t->foreignId('instance_id')->constrained()->restrictOnDelete(); $t->string('external_id',100); $t->string('name'); $t->text('description')->nullable(); $t->text('invitation_link')->nullable(); $t->unsignedInteger('member_count')->nullable(); $t->boolean('admin_only')->nullable(); $t->boolean('is_admin')->nullable(); $t->boolean('present')->default(true); $t->boolean('enabled')->default(false); $t->boolean('authorized')->default(false); $t->text('authorization_notes')->nullable(); $t->timestamp('last_message_at')->nullable(); $t->timestamp('metadata_at')->nullable(); $t->timestamp('seen_at')->nullable(); $t->timestamps(); $t->unique(['instance_id','external_id']); $t->index(['present','authorized','enabled']);
  });
  Schema::create('contents', function(Blueprint $t){
   $t->id(); $t->string('title'); $t->string('kind',12); $t->string('path',500); $t->string('mime',80); $t->unsignedBigInteger('bytes'); $t->char('sha256',64); $t->text('caption'); $t->boolean('enabled')->default(true); $t->timestamp('expires_at')->nullable(); $t->timestamps();
  });
  Schema::create('publications', function(Blueprint $t){
   $t->id(); $t->foreignId('whatsapp_group_id')->constrained()->restrictOnDelete(); $t->foreignId('content_id')->constrained()->restrictOnDelete(); $t->timestamp('scheduled_at'); $t->string('status',24)->default('draft'); $t->foreignId('approved_by')->nullable()->constrained('users')->restrictOnDelete(); $t->timestamp('approved_at')->nullable(); $t->text('caption_snapshot')->nullable(); $t->string('path_snapshot',500)->nullable(); $t->char('hash_snapshot',64)->nullable(); $t->string('kind_snapshot',12)->nullable(); $t->string('message_id')->nullable(); $t->string('zaap_id')->nullable(); $t->string('error',200)->nullable(); $t->timestamp('submitted_at')->nullable(); $t->timestamps(); $t->unique(['whatsapp_group_id','scheduled_at']); $t->index(['status','scheduled_at']);
  });
  Schema::create('publication_events', function(Blueprint $t){$t->id(); $t->foreignId('publication_id')->constrained()->restrictOnDelete(); $t->string('type',30); $t->timestamp('created_at')->useCurrent();});
 }
 public function down(): void {foreach(['publication_events','publications','contents','whatsapp_groups','instances'] as $table) Schema::dropIfExists($table);}
};
