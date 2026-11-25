<?php
use Illuminate\Support\Facades\{Artisan,Schedule,DB};
Artisan::command('grupos:maintenance',function(){
 DB::table('publications')->where('status','submitting')->where('submitted_at','<',now()->subMinutes(15))->update(['status'=>'unknown','error'=>'Execução interrompida; verificar manualmente.','updated_at'=>now()]);
 $this->info('Manutenção concluída.');
});
Schedule::command('grupos:maintenance')->everyFiveMinutes()->withoutOverlapping(10);

Artisan::command('grupos:refresh-metadata',function(){
 $state=app(\App\Services\MetadataRefresh::class)->run();
 $this->info('Metadata refresh: '.$state);
 return $state==='failed'?1:0;
});
Schedule::command('grupos:refresh-metadata')->everyFiveMinutes()->withoutOverlapping(5)->when(fn()=>config('grupos.metadata_auto_refresh'));
