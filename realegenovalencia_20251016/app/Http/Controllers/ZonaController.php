<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ZonaController extends Controller
{
    //
    public function obtenerZonas(){
        $Zona = new Zona();
        $valores = $Zona::all();
        $respuesta = [
            "success"=> true,
            "msg" => "Valores devueltos por el EndPoint",
            "data" => $valores,
            "error"=> "",
            "total" => sizeof($valores)
        ];

        return response()->json($respuesta,200);
    }

    public function obtenerZona($idzona){
        $Zona = new Zona();
        $valores = $Zona->where('id_zona',$idzona)->get();
        $respuesta = [
            "success"=> true,
            "msg" => "Valores devueltos por el EndPoint",
            "data" => $valores,
            "error"=> "",
            "total" => sizeof($valores)
        ];

        return response()->json($respuesta,200);
    }
}
