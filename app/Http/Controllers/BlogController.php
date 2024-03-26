<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        $blog = Blog::with('user','categoria')->get()->map(function ($blog) {
            return [
                'id' => $blog->id,
                'title' => $blog->title,
                'content' => $blog->content,
                'img' => $blog->img,
                'user' => $blog->user->name,
                'email' => $blog->user->email,
                'categoria' => $blog->categoria->title,
            ];
        });
        return response()->json($blog);
    }


    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'title' => 'required|string',
            'content' => 'required|string',
            'img' => 'required|image|max:3072',
            'categoria_id' => 'required|exists:categorias,id',
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
    
        $blog = Blog::create([
            'title' => $request->title,
            'content' => $request->content,
            'img' => 'images/'.$imageName,
            'categoria_id' => $request->categoria_id,
            'user_id' => $request->user_id,
        ]);
    
        $response = [
            'status' => 'success',
            'message' => 'Blog is added successfully.',
            'data' => $blog,
        ];
    
        return response()->json($response, 200);
    }

    public function show($id)
    {
        
        $blog = Blog::with('user', 'categoria')->find($id);

if(!$blog){
    return response()->json([
        'status' => 'failed',
        'message' => 'Blog not found.',
    ], 404);
}

return response()->json([
    'status' => 'success',
    'message' => 'Blog retrieved successfully.',
    'data' => [
        'id' => $blog->id,
        'title' => $blog->title,
        'content' => $blog->content,
        'img' => $blog->img,
        'categoria' => $blog->categoria->title,
        'user-name' => $blog->user->name,
        'user-email' => $blog->user->email,
        'created_at' => $blog->created_at,
        'updated_at' => $blog->updated_at
    ],
], 200);

    }

    public function update(Request $request, $id)
    {
        $blog = Blog::find($id);
    
        if(!$blog){
            return response()->json([
                'status' => 'failed',
                'message' => 'Blog not found.',
            ], 404);
        }
    
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'img' => 'nullable|image',
            'categoria_id' => 'required|exists:categorias,id',
            'user_id' => 'required|exists:users,id',
        ]);
    
        if ($request->hasFile('img')) {
            // Eliminar la imagen anterior si existe
            if ($blog->img) {
                $imagePath = public_path($blog->img);
                if(file_exists($imagePath)){
                    unlink($imagePath);
                }
            }
    
            // Guardar la nueva imagen
            $imageName = time().'.'.$request->img->extension();  
            $request->img->move(public_path('images'), $imageName);
            $blog->img = 'images/'.$imageName;
        }
    
        $blog->title = $request->title;
        $blog->content = $request->content;
        $blog->categoria_id = $request->categoria_id;
        $blog->user_id = $request->user_id;
    
        $blog->save();
    
        return response()->json([
            'status' => 'success',
            'message' => 'Blog updated successfully.',
            'data' => $blog,
        ], 200);
    }
    

    public function destroy($id)
{
    $blog = Blog::find($id);

    if(!$blog){
        return response()->json([
            'status' => 'failed',
            'message' => 'Blog not found.',
        ], 404);
    }

    // Eliminar la imagen asociada si existe
    if($blog->img){
        $imagePath = public_path($blog->img);
        if(file_exists($imagePath)){
            unlink($imagePath);
        }
    }

    $blog->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'Blog deleted successfully.',
    ], 200);
}

}
