<?php
use Illuminate\Support\Facades\{Artisan,Schedule,DB};
Artisan::command('grupos:maintenance',function(){
 DB::table('publications')->where('status','submitting')->where('submitted_at','<',now()->subMinutes(15))->update(['status'=>'unknown','error'=>'Execução interrompida; verificar manualmente.','updated_at'=>now()]);
 $this->info('Manutenção concluída.');
});
Schedule::command('grupos:maintenance')->everyFiveMinutes()->withoutOverlapping(10);
