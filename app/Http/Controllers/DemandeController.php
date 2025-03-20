<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;


class DemandeController extends Controller
{
    public function demander(){
        return 'hgzhdbns';
    }


    public function store(Request $request){
        $request->validate([

            'type_demande_id' => 'required',
        ]);


         $demande = new Demande();
         $demande->detail= $request->input('detail');
         $demande->état = "pending";
         $demande->type_demande_id= $request->type_demande_id;
         $demande->user_id= Auth::user()->id;

         if ($request->hasFile('image_path')) {
            $request->file('image_path')->store('images');
            $image_path = $request->file('image_path');
            $image_pathName = time() . '.' . $image_path->getClientOriginalExtension();
            $demande->image_path = $image_pathName ;
            // $Client->save();

        }
        // dd(  $request->user());



        $demande->save();


    }
    public function getdemande($id){
        $demande= Demande::find($id);

        if ($demande){
            return $demande;

        }else{
            return response()->json([
                "status"=>"Nok",
                "message"=>"baaxoul",
                "data"=>$demande
            ],400);
        }



    }
    public function listDemandes(){

        return Demande::all();
    }

    public function update(Request $request,$id){
        // Trouver la demande par son ID
        $demande= Demande::find($id);

    // Vérifier si l'utilisateur existe
    if (!$demande) {
        return response()->json(['message' => 'Demande non trouvé'], 404);
    }

    // Valider les données de la requête
    $request->validate([
        'detail' => 'sometimes|string|max:255',
        'état' => 'sometimes|string|max:20',
        'adresse' => 'sometimes|string|max:255',
        'type_demande_id' => 'sometimes|integer|exists:type_demandes,id',
        'user_id' => 'sometimes|integer|exists:user_id,id',
        'image_path' => 'sometimes|string|max:255',
    ]);

    // dd($demande);
    // Mettre à jour les champs modifiés
     $demande->update([
         'detail'=> $request->input('detail'),
         'état' => $request->input('état'),
        'type_demande_id'=> $request->type_demande_id,
        'user_id'=> Auth::user()->id,
        'image_path'=> $request->input('image_path'),

    ]);
    // Retourner une réponse avec l'utilisateur mis à jour
    return response()->json(['message' => 'la demande est mise à jour avec succès', 'demande' => $demande]);
    }
    public function delete($id){
        $demande= Demande::find($id);
        // dd($demande);
        $demande->estactif =0;
        $demande->save();

    }


}
