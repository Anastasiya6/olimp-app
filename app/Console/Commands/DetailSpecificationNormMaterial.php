<?php

namespace App\Console\Commands;

use App\Models\Designation;
use App\Models\ReportApplicationStatement;
use App\Models\Specification;
use App\Services\Statements\ApplicationStatementService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Mpdf\Tag\P;

class DetailSpecificationNormMaterial extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:detail-specification-norm-material';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(ApplicationStatementService $service)
    {
        echo 'start '.now().PHP_EOL;
        $service->make(28069);
        echo 'end '.now();
        exit;

        $designation = Designation::where('id',88402)->with(['children', 'parents','designationMaterial.material'])->first();

        if($designation){

            echo $designation->designation.' '.$designation->name.PHP_EOL;

        }
        $this->nodeNode($designation->id);

        exit;
        $designations = Designation::where('id',87380)->with(['children', 'parents','designationMaterial.material'])->get();
        $str = '---';

        foreach ($designations as $designation) {
            echo $designation->designation.'   ',$designation->name. PHP_EOL;
           // echo print_r($designation->designationMaterial,1);
            $this->node($designation,$str);
        }

        exit;
        $items = ReportApplicationStatement::/*whereHas('designation', function ($query) {
            $query->where('department_id', '08');
        })
            ->*/has('designationMaterial.material')
            ->with('designationEntry')
            ->get();

        $data = $items->sortBy('id')->map(function ($item) {
            return [
                'id' => $item->designationMaterial->material->id,
                'material_name' => $item->designationMaterial->material->name,
                'detail_name' => $item->designationEntry->designation,
                'quantity_total' => $item->quantity_total,
                'unit' => $item->designationMaterial->material->unit->unit,
                'norm' => $item->designationMaterial->norm,
            ];
        });

        $previousMaterial = null;
        $fileContent = '';
        foreach ($data as $row) {
            if ($row['material_name'] !== $previousMaterial) {
                $fileContent .= $row['material_name'] . PHP_EOL;
                $previousMaterial = $row['material_name'];
            }

            $fileContent .= str_repeat(' ', strlen($row['material_name'])) . sprintf("%-80s%-10s%-10s%-10s", $row['detail_name'], $row['quantity_total'], $row['norm'], $row['quantity_total']*$row['norm']) . PHP_EOL;

        }
        $filePath = 'specif.txt';

        // Записываем данные в файл
        Storage::disk('public')->put($filePath, $fileContent);

        // Возвращаем путь к файлу для скачивания или отображения
        return $filePath;

        // Записываем данные в файл
        $filePath = 'output.txt';
        file_put_contents($filePath, $fileContent);

        foreach ($data as $row) {
            if ($row['material_name'] !== $previousMaterial) {
                echo $row['material_name'] . PHP_EOL;
                $previousMaterial = $row['material_name'];
            }
            echo str_repeat(' ', strlen($row['material_name'])) . $row['detail_name'] . PHP_EOL;
        }

    }

    public function nodeNode($designation_id,$str='',$tire='---')
    {
        $specifications = Specification::where('designation_id',$designation_id)->with('designationEntry','designationMaterial')->get();

        // Проверяем, что коллекция не пуста
        if ($specifications->isNotEmpty()) {

            if($str)
                echo $str.PHP_EOL;

            foreach ($specifications as $specification) {

                echo $tire.$specification->designationEntry->designation . ' ' . $specification->designationEntry->name;

                if ($specification->designationMaterial->isNotEmpty()) {

                    foreach ($specification->designationMaterial as $material) {

                        echo ' '.$material->material->name.'*** '.$specification->quantity.PHP_EOL;
                    }
                }else{
                    echo PHP_EOL;
                }
                $this->nodeNode($specification->designation_entry_id,$specification->designationEntry->designation . ' ' . $specification->designationEntry->name,$tire.'---');
                /*$specification_children = Specification::where('designation_id', $specification->designation_entry_id)->with('designationEntry')->get();

                // Проверяем, что коллекция не пуста
                if ($specification_children->isNotEmpty()) {

                    echo $specification->designationEntry->designation . ' ' . $specification->designationEntry->name . PHP_EOL;

                    foreach ($specification_children as $child) {

                        //echo $child->id.PHP_EOL;
                        echo '---------'.$child->designationEntry->designation . ' ' . $child->designationEntry->name;
                        // echo print_r($child->designationMaterial,1);
                        if ($child->designationMaterial->isNotEmpty()) {

                            foreach ($child->designationMaterial as $child_material) {
                                echo ' '.$child_material->material->name.PHP_EOL;
                            }
                        }else{
                            echo PHP_EOL;
                        }
                    }
                }*/
            }
        }
    }
    public function node($designation,$str)
    {
        foreach ($designation->children as $child) {
            //echo print_r($child,1);
            echo $str.$child->designation ;
            foreach($child->designationMaterial as $material){
                echo '  '.$material->material->name.'  '.$material->norm;

            }
            echo  PHP_EOL;
            //$str = $str.'--';
            $this->node($child,$str.'---');
        }
    }
}
