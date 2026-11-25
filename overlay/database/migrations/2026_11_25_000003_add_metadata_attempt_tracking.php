<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {Schema::table('whatsapp_groups',function(Blueprint $t){$t->timestamp('metadata_attempted_at')->nullable();$t->index(['present','metadata_attempted_at']);});}
 public function down(): void {Schema::table('whatsapp_groups',function(Blueprint $t){$t->dropIndex(['present','metadata_attempted_at']);$t->dropColumn('metadata_attempted_at');});}
};
