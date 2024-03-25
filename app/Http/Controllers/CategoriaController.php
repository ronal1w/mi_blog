<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index1()
    {
        $categoria = Categoria::all();
        
        return response()->json($categoria);
    }

    public function index2()
{
    $categorias = Categoria::with('user')->get();
    
    return response()->json($categorias);
}

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



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

   

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'title' => 'required|string',
            'descriptions' => 'required|string',
            'img' => 'required|image',
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
    

    /**
     * Display the specified resource.
     */
    public function show(Categoria $categoria)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categoria $categoria)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categoria $categoria)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categoria $categoria)
    {
        //
    }
}
