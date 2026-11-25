<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class WhatsappGroup extends Model {
 protected $fillable=['instance_id','external_id','name','description','invitation_link','invitation_source','invitation_checked_at','member_count','admin_only','is_admin','announcement','archived','muted','unread_count','present','enabled','authorized','authorization_notes','authorized_at','max_daily_publications','last_message_at','metadata_at','seen_at'];
 protected function casts(): array {return ['admin_only'=>'boolean','is_admin'=>'boolean','announcement'=>'boolean','archived'=>'boolean','muted'=>'boolean','present'=>'boolean','enabled'=>'boolean','authorized'=>'boolean','last_message_at'=>'datetime','metadata_at'=>'datetime','seen_at'=>'datetime','authorized_at'=>'datetime','invitation_checked_at'=>'datetime','member_count'=>'integer','max_daily_publications'=>'integer'];}
 public function instance(){return $this->belongsTo(Instance::class);}
 public function publications(){return $this->hasMany(Publication::class);}
 public function schedule(){return $this->hasOne(GroupSchedule::class);}
 public function scopeRecent(Builder $query): Builder {return $query->where('present',true)->where('last_message_at','>=',now()->subDays(7));}
 public function scopeAuthorizedForPlanning(Builder $query): Builder {
  return $query->where('present',true)->where('authorized',true)->where('enabled',true)
   ->whereNotNull('authorized_at')->whereNotNull('metadata_at')->where('metadata_at','>=',now()->subDays(7))
   ->whereNotNull('is_admin')->where('announcement',false)
   ->where(fn($q)=>$q->where('admin_only',false)->orWhere(fn($q)=>$q->where('admin_only',true)->where('is_admin',true)))
   ->whereHas('instance',fn($q)=>$q->where('enabled',true)->where('connected',true)->where('sync_state','complete')->where('synced_at','>=',now()->subDays(7)));
 }
}
