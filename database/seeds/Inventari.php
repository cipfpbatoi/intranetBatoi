<?php

use Illuminate\Database\Seeder;
use Intranet\Entities\Material;
use Intranet\Entities\Lote;
use Intranet\Entities\Articulo;


class Inventari extends Seeder
{
    /**
     * Run the database seeds.
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

    private function creaArticulo($item,$id){
        $articulo = new Articulo();
        $articulo->lote_id = $id;
        if ($item->ISBN){
            $articulo->descripcion = $item->descripcion;
            $articulo->identificacion = $item->ISBN;
        } else {
            $articulo->identificacion = $item->nserieprov;
            $articulo->marca = $item->marca;
            $articulo->modelo = $item->modelo;
        }
        $articulo->estado = $item->estado;
        $articulo->espacio_id = $item->espacio;
        $articulo->unidades = $item->unidades;
        $articulo->fechaultimoinventario = $item->fechaultimoinventario;
        $articulo->fechaBaja = $item->fechaBaja;
        $articulo->save();
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
        $llibre = Lote::create([
            'descripcion' => 'LIBROS',
            'inventariable' => 0,
        ]);
        $libros = Material::whereNotNull('ISBN')->get();
        foreach ($libros as $libro){
            $this->creaArticulo($libro,$llibre->id);
        }
        $llibre->unidades = count($libros);
        $llibre->save();


        $allMaterials = Material::orderBy('descripcion')->whereNull('ISBN')->get();
        $grouped = $allMaterials->groupBy(function ($item, $key) {
            return strtoupper($item->descripcion).$item->procedencia.strtoupper($item->proveedor);
        });

        foreach ($grouped as $grup){
            foreach ($grup as $key => $item){
                if ($key == '0') {
                    $lote = $this->creaLote($item);
                    $total = $lote->unidades;
                }
                $this->creaArticulo($item,$lote->id);
                $total ++;
            }
            $lote->unidades = $total;
            $lote->save();
        }
    }
}
