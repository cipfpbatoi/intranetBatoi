<?php
namespace Database\Seeder;

use Illuminate\Database\Seeder;
use Intranet\Entities\Material;
use Intranet\Entities\Lote;
use Intranet\Entities\Articulo;


class Inventari extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */

    private function creaLote($material){
        $lote = Lote::where('descripcion',$material->descripcion)->where('inventariable',0)->first();
        if (!$lote){
            $lote = new Lote();
            $lote->descripcion = strtoupper($material->descripcion);
            if ($material->nserieprov){
                $lote->inventariable = 1;
            } else {
                $lote->inventariable = 0;
            }
            $lote->procedencia = $material->procedencia;
            $lote->proveedor = isset($material->marca)?strtoupper($material->proveedor):NULL;
            $lote->save();
        }
        return $lote;
    }

    private function llenaLote($lote,$articulos){
        $total = 0;
        foreach ($articulos as $articulo){
            $total += $this->creaArticulo($articulo,$lote->id);
        }
        $lote->unidades = $total;
        $lote->save();
    }

    private function creaArticulo($item,$id){
        $articulo = new Articulo();
        $articulo->lote_id = $id;
        if ($item->ISBN){
            $articulo->identificacion = $item->ISBN;
        } else {
            $articulo->identificacion = $item->nserieprov;
            $articulo->marca = $item->marca;
            $articulo->modelo = $item->modelo;
        }
        $articulo->descripcion = $item->descripcion;
        $articulo->estado = $item->estado;
        $articulo->espacio_id = $item->espacio;
        $articulo->unidades = $item->unidades;
        $articulo->fechaultimoinventario = $item->fechaultimoinventario;
        $articulo->save();
        return $item->unidades;

    }

    public function run()
    {
        // Limpieza material
        $allMaterials = Material::orderBy('descripcion')->get();
        foreach ($allMaterials as $material){
            if ($material->marca == '') $material->marca = NULL;
            if ($material->modelo == '') $material->modelo = NULL;
            if ($material->proveedor == '') $material->proveedor = NULL;
            if ($material->ISBN == '') $material->ISBN = NULL;
            $material->save();
        }

        // Llibres
        $baixes = Lote::create([
            'descripcion' => 'ARTICLES PENDENTS DE BAIXA DEFINITIVA',
            'inventariable' => 0,
        ]);
        $mobiliari= Lote::create([
            'descripcion' => 'MOBILIARI',
            'inventariable' => 1,
            'procedencia' => 1
        ]);
        $llibres = Lote::create([
            'descripcion' => 'LLIBRES',
            'inventariable' => 0,
        ]);

        //baixes
        $artBaixa = Material::where('estado',3)->get();
        $this->llenaLote($baixes,$artBaixa);

        //llibres
        $libros = Material::whereNotNull('ISBN')->get();
        $this->llenaLote($llibres,$libros);

        //mobiliari
        $allMobles = Material::where('descripcion','like','CADIR%')
            ->orWhere('descripcion','like','TAULA%')
            ->orWhere('descripcion','like','TAULES%')
            ->orWhere('descripcion','like','ARMAR%')
            ->orWhere('descripcion','like','SILL%')
            ->orWhere('descripcion','like','MESA%')
            ->orWhere('descripcion','like','MESIT%')
            ->get();
        $mobles = $allMobles->diff($artBaixa);
        $this->llenaLote($mobiliari,$mobles);

        $allMaterials = Material::orderBy('descripcion')->whereNull('ISBN')
            ->where('estado','<>',3)->get();
        $materials = $allMaterials->diff($mobles);
        $grouped = $materials->groupBy(function ($item, $key) {
            return strtoupper($item->descripcion).$item->procedencia.strtoupper($item->proveedor);
        });

        foreach ($grouped as $grup){
            foreach ($grup as $key => $item){
                if ($key == '0') {
                    $lote = $this->creaLote($item);
                    $total = 0;
                }
                $total += $this->creaArticulo($item,$lote->id);
            }
            $lote->unidades = $total;
            $lote->save();
        }
    }
}
