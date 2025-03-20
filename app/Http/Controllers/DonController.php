<?php

namespace App\Http\Controllers;

use App\Models\Don;
use Illuminate\Http\Request;

class DonController extends Controller
{

    public function store(Request $request){
        $request->validate([

            'donneur_numero' => 'required',



        ]);


         $don = new Don();
         $don->detail= $request->input('detail');
         $don->somme= $request->somme;
         $don->type_don_id= $request->type_don_id;
         $don->donneur_numero=$request->input('donneur_numero');
         $don->donneur_adresse= $request->input('donneur_adresse');
         $don->save();

        //  $table->string('somme');
        //  $table->string('donneur_nom')->nullable();
        //  $table->string('donneur_numero')->nullable();
        //  $table->string('donneur_adresse')->nullable();
        //  $table->string('type_don_id')->nullable();
    }
    public function getdon($id){

        $don= Don::find($id);


        if ($don){
            return $don;

        }else{
            return response()->json([
                "status"=>"Nok",
                "message"=>"baaxoul",
                "data"=>$don
            ],400);
        }
    }
    public function listdons(){

        return Don::all();
    }

    public function update(){}
    public function delete(){}

}
