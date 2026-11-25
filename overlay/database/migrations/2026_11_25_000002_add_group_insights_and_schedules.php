<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::table('whatsapp_groups', function(Blueprint $t){
   $t->boolean('archived')->nullable();
   $t->boolean('muted')->nullable();
   $t->unsignedInteger('unread_count')->nullable();
   $t->string('invitation_source',20)->nullable();
   $t->timestamp('invitation_checked_at')->nullable();
   $t->boolean('announcement')->nullable();
   $t->unsignedTinyInteger('max_daily_publications')->default(1);
   $t->timestamp('authorized_at')->nullable();
   $t->index(['present','last_message_at']);
   $t->index(['present','metadata_at']);
  });
  Schema::table('instances',function(Blueprint $t){
   $t->timestamp('sync_attempted_at')->nullable();
   $t->string('sync_state',24)->default('never');
  });
  Schema::create('group_schedules',function(Blueprint $t){
   $t->id();
   $t->foreignId('whatsapp_group_id')->unique()->constrained()->restrictOnDelete();
   $t->foreignId('content_id')->constrained()->restrictOnDelete();
   $t->date('starts_on'); $t->date('ends_on');
   $t->unsignedSmallInteger('interval_days')->default(1);
   $t->json('weekdays'); $t->json('times');
   $t->boolean('enabled')->default(false);
   $t->timestamps();
  });
 }
 public function down(): void {
  Schema::dropIfExists('group_schedules');
  Schema::table('instances',fn(Blueprint $t)=>$t->dropColumn(['sync_attempted_at','sync_state']));
  Schema::table('whatsapp_groups',function(Blueprint $t){
   $t->dropIndex(['present','last_message_at']); $t->dropIndex(['present','metadata_at']);
   $t->dropColumn(['archived','muted','unread_count','invitation_source','invitation_checked_at','announcement','max_daily_publications','authorized_at']);
  });
 }
};
