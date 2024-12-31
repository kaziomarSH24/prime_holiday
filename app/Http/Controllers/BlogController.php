<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    /**
     * Blog category methods
     */
    public function getCategories()
    {
        $categories = BlogCategory::all();
        if ($categories->isEmpty()) {
            return response()->json(['message' => 'No categories found'], 404);
        }

        return response()->json($categories);
    }

    //store category
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        $category = new BlogCategory();
        $category->name = $request->name;
        $category->save();

        return response()->json(['message' => 'Category created successfully']);
    }

    //delete category
    public function deleteCategory($id)
    {
        $category = BlogCategory::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }

    /**
     * Blog methods
     */

    //get all blogs
    public function getBlogs(Request $request)
    {
        $perPage = $request->per_page ?? 10;
        $blogs = Blog::with('category')->paginate($perPage);
        if ($blogs->isEmpty()) {
            return response()->json(['message' => 'No blogs found'], 404);
        }

        return response()->json([
            'success' => true,
            'blogs' => $blogs
        ]);
    }

    //store blog
    public function storeBlog(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer',
            'title' => 'required|string',
            'content' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time(). rand(100, 999) . '.' . $image->getClientOriginalExtension();

            //check if image directory exists
            $uploadPath = public_path('uploads/images/blogs/');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $image->move($uploadPath, $imageName);
        }
        $imageUrl = url('uploads/images/blogs/' . $imageName);

        $blog = new Blog();
        $blog->category_id = $request->category_id;
        $blog->title = $request->title;
        $blog->content = $request->content;
        $blog->image = $imageUrl;
        $blog->save();

        return response()->json([
            'success' => true,
            'message' => 'Blog created successfully',
            'blog' => $blog
        ]);

    }

    //show blog
    public function getBlog($id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json([
                'success' => false,
                'message' => 'Blog not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'blog' => $blog
        ]);
    }

    //update blog
    public function updateBlog(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer',
            'title' => 'required|string',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], 404);
        }

        //image upload
        if ($request->hasFile('image')) {

            //delete old image
            $oldImagePath = parse_url($blog->image);

            if (isset($oldImagePath['path']) && file_exists(public_path($oldImagePath['path']))) {
                unlink(public_path($oldImagePath['path']));
            }

            $image = $request->file('image');
            $imageName = time(). rand(100, 999) . '.' . $image->getClientOriginalExtension();

            //check if image directory exists
            $uploadPath = public_path('uploads/images/blogs/');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $image->move($uploadPath, $imageName);
            $imageUrl = url('uploads/images/blogs/' . $imageName);
            $blog->image = $imageUrl;
        }


        $blog->category_id = $request->category_id;
        $blog->title = $request->title;
        $blog->content = $request->content;
        $blog->save();

        return response()->json([
            'success' => true,
            'message' => 'Blog updated successfully',
            'blog' => $blog
        ]);
    }

    //delete blog
    public function deleteBlog($id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json(['message' => 'Blog not found'], 404);
        }

        //delete image
        $imagePath = parse_url($blog->image);
        if (isset($imagePath['path']) && file_exists(public_path($imagePath['path']))) {
            unlink(public_path($imagePath['path']));
        }

        $blog->delete();
        return response()->json(['message' => 'Blog deleted successfully']);
    }

    //get category blogs
    public function categoriesWiseBlog(Request $request)
    {
        $perPage = $request->per_page ?? 10;
        $categories = BlogCategory::all();

        foreach ($categories as $category) {
            $category->blogs = Blog::where('category_id', $category->id)
                            ->paginate($perPage)
                            ->through(function ($blog) {
                                return [
                                    'id' => $blog->id,
                                    'title' => $blog->title,
                                    'content' => $blog->content,
                                    'image' => $blog->image,
                                    'created_at' => $blog->created_at->format('M d, Y')
                                ];
                            });
        }

        if ($categories->isEmpty()) {
            return response()->json(['message' => 'No blogs found'], 404);
        }



        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);


    }
}
