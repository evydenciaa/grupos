<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class GroupSchedule extends Model {
 protected $fillable=['whatsapp_group_id','content_id','starts_on','ends_on','interval_days','weekdays','times','enabled'];
 protected function casts(): array {return ['weekdays'=>'array','times'=>'array','enabled'=>'boolean','interval_days'=>'integer'];}
 public function group(){return $this->belongsTo(WhatsappGroup::class,'whatsapp_group_id');}
 public function content(){return $this->belongsTo(Content::class);}
}
