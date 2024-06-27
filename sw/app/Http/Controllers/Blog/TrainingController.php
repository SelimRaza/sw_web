<?php

namespace App\Http\Controllers\Blog;

use App\Blog\Post;
use App\Blog\PostType;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TrainingController extends Controller
{
    public function __construct()
    {

    }

    public function index()
    {
            $posts = Post::where('status_id', '=', 1)->get();
            return view('blog.training.index')->with('posts', $posts);
        }

    public function show($id)
    {
            $post = Post::findorfail($id);
            return view('blog.training.show')->with('post', $post);
    }
}
