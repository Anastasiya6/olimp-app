<?php

namespace App\Console\Commands;

use App\Models\DesignationMaterial;
use Illuminate\Console\Command;

class DeleteDuplicateNorm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-duplicate-norm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /*$designationCounts = DesignationMaterial::select('designation_id')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('designation_id')
            ->having('count', '>', 1)
            ->get();*/
        $designationCounts = DesignationMaterial::select('designation_id')
            ->join('materials', 'materials.id', '=', 'designation_materials.material_id')
            ->leftJoin('group_materials', 'group_materials.material_id', '=', 'materials.id')
            ->selectRaw('COUNT(*) as count')
            ->whereNull('group_materials.material_id')
            ->groupBy('designation_id')
            ->having('count', '>', 1)
            ->get();
        $array_norm = array();
        foreach($designationCounts as $designation){

            $max_norm = 0;
            $designation_id = 0;
            $previous = 0;
            $key = 0;
            $can_delete = 1;
            $previous_designation = '';

            $searchable = DesignationMaterial::where('designation_id',$designation->designation_id)->get();
            foreach($searchable as $search){
                if($key != 0){
                    if(abs($previous - $search->norm) > 2 ){
                       // $answer = $this->ask('Разница больше 2 для designation_id = '.$search->designation_id.'(y/n)');
                        //if(strtolower($answer ) === 'n'){
                            $can_delete = 0;
                            $array_norm[$previous_designation->designation->designation][] = array([
                                                        'designation'=> $previous_designation->designation->designation,
                                                        'name' =>$previous_designation->designation->name,
                                                        'material' => $previous_designation->material->name,
                                                        'norm' => $previous_designation->norm]);
                            $array_norm[$search->designation->designation][] = array([
                                                        'designation'=> $search->designation->designation,
                                                        'name' =>$search->designation->name,
                                                        'material' => $search->material->name,
                                                        'norm' => $search->norm]);


                            continue;
                       // }
                    }
                }

                if($search->norm > $max_norm){

                    echo $search->designation_id.PHP_EOL;
                    echo $search->norm.PHP_EOL;

                    $max_norm = $search->norm;
                    $designation_id = $search->designation_id;

                }
                $previous_designation = $search;

                $previous = $search->norm;
                $key++;
            }
            if($max_norm > 0 && $designation_id > 0 && $can_delete == 1){
               // $answer = $this->ask("Удаляем все кроме нормы не равной $max_norm (y/n)");
                //if(strtolower($answer ) === 'y'){
                    DesignationMaterial::where('designation_id',$designation->designation_id)->where('norm','!=',$max_norm)->delete();
                    $this->deleteDesignation($designation->designation_id,$max_norm);

                    echo print_r(DesignationMaterial::where('designation_id',$designation->designation_id)->get()->toArray(),1);
                //}

            }

        }
        foreach($array_norm as  $value_){

            foreach($value_ as $key => $value){
                //echo print_r($value,1);
                //break;
                if($key == 0)
                    echo $value[0]['designation'].'   '.$value[0]['name'].PHP_EOL;

                echo $value[0]['material'].'   норма='.$value[0]['norm'].PHP_EOL;
            }

        echo '----------------------------------------'.PHP_EOL;

        }
      //  echo print_r($array_norm,1);

    }
    public function deleteDesignation($designation_id,$max_norm)
    {
        if(DesignationMaterial::where('designation_id',$designation_id)->where('norm',$max_norm)->count()>1){
            $materialToDelete = DesignationMaterial::where('designation_id', $designation_id)
                ->where('norm', $max_norm)
                ->first();

            if($materialToDelete) {
                $materialToDelete->delete();
            }
            $this->deleteDesignation($designation_id,$max_norm);

        }
    }
}
