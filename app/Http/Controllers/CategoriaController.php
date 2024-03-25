<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class CategoriaController extends Controller
{

/*
    public function index2()
{
    $categorias = Categoria::with('user')->get();
    
    return response()->json($categorias);
}*/

public function index()
{
    $categorias = Categoria::with('user')->get()->map(function ($categoria) {
        return [
            'id' => $categoria->id,
            'title' => $categoria->title,
            'descriptions' => $categoria->descriptions,
            'img' => $categoria->img,
            'user' => $categoria->user->name,
            'email' => $categoria->user->email,
            'created_at' => $categoria->created_at,
            'updated_at' => $categoria->updated_at
        ];
    });
    
    return response()->json($categorias);
}
   

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'title' => 'required|string',
            'descriptions' => 'required|string',
            'img' => 'required|image|max:3072',
            'user_id' => 'required|exists:users,id',
        ]);
    
        if($validate->fails()){  
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation Error!',
                'data' => $validate->errors(),
            ], 403);    
        }
    
        $imageName = time().'.'.$request->img->extension();  
        $request->img->move(public_path('images'), $imageName);
    
        $categoria = Categoria::create([
            'title' => $request->title,
            'descriptions' => $request->descriptions,
            'img' => 'images/'.$imageName,
            'user_id' => $request->user_id,
        ]);
    
        $response = [
            'status' => 'success',
            'message' => 'Categoria is added successfully.',
            'data' => $categoria,
        ];
    
        return response()->json($response, 200);
    }
    

    public function show($id)
    {
        $categoria = Categoria::find($id);
    
        if(!$categoria){
            return response()->json([
                'status' => 'failed',
                'message' => 'Categoria not found.',
            ], 404);
        }
    
        return response()->json([
            'status' => 'success',
            'message' => 'Categoria retrieved successfully.',
            'data' => $categoria,
        ], 200);
    }
    


    public function update(Request $request, $id)
{
    $validate = Validator::make($request->all(), [
        'title' => 'required|string',
        'descriptions' => 'required|string',
        'img' => 'nullable|image',
        'user_id' => 'required|exists:users,id',
    ]);

    if($validate->fails()){  
        return response()->json([
            'status' => 'failed',
            'message' => 'Validation Error!',
            'data' => $validate->errors(),
        ], 403);    
    }

    $categoria = Categoria::find($id);
    if(!$categoria){
        return response()->json([
            'status' => 'failed',
            'message' => 'Categoria not found.',
        ], 404);
    }

    $categoria->title = $request->title;
    $categoria->descriptions = $request->descriptions;
    if($request->has('img')){
        // Eliminar la imagen antigua si existe
        if($categoria->img){
            $oldImagePath = public_path($categoria->img);
            if(file_exists($oldImagePath)){
                unlink($oldImagePath);
            }
        }

        // Guardar la nueva imagen
        $imageName = time().'.'.$request->img->extension();  
        $request->img->move(public_path('images'), $imageName);
        $categoria->img = 'images/'.$imageName;
    }
    $categoria->user_id = $request->user_id;
    $categoria->save();

    $response = [
        'status' => 'success',
        'message' => 'Categoria updated successfully.',
        'data' => $categoria,
    ];

    return response()->json($response, 200);
}

public function destroy($id)
{
    $categoria = Categoria::find($id);

    if(!$categoria){
        return response()->json([
            'status' => 'failed',
            'message' => 'Categoria not found.',
        ], 404);
    }

    // Eliminar la imagen asociada si existe
    if($categoria->img){
        $imagePath = public_path($categoria->img);
        if(file_exists($imagePath)){
            unlink($imagePath);
        }
    }

    $categoria->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'Category deleted successfully.',
    ], 200);
}

}
