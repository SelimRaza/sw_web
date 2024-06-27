<?php

namespace App\Http\Controllers\Blog;

use App\Blog\Post;
use App\Blog\PostType;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    private $access_key = 'tbld_blog';
    private $currentUser;
    private $userMenu;

    public function __construct()
    {
        $this->middleware('timezone');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentUser = Auth::user();
            $subMenu = SubMenu::where(['wsmn_ukey' => $this->access_key,'cont_id' => $this->currentUser->employee()->cont_id])->first();
            if ($subMenu!=null) {
                $this->userMenu = UserMenu::where(['aemp_id' => $this->currentUser->employee()->id, 'wsmn_id' => $subMenu->id])->first();
            } else {
                $this->userMenu = (object)array('wsmu_vsbl' => 0, 'wsmu_crat' => 0, 'wsmu_read' => 0, 'wsmu_updt' => 0, 'wsmu_delt' => 0,);
            }
            return $next($request);
        });
    }

    public function index()
    {
        if ($this->userMenu->wsmu_vsbl) {
            $posts = Post::all();
            return view('blog.post.index')->with('posts', $posts)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }


    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $postTypes = PostType::all();
            return view('blog.post.create')->with('postTypes', $postTypes);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        // dd($request);


        $post = new Post();
        $post->title = $request->title;
        $post->body = $request->body;
        $post->post_type_id = $request->post_type_id;
        $post->status_id = 1;
        $post->file = "";
        if ($request->hasFile('input_file')) {
            $input_doc_file = time() . '.' . $request->input_file->getClientOriginalExtension();
            $request->input_file->move('uploads/blog/attached/', $input_doc_file);
            $post->file = $input_doc_file;
        }
        $post->country_id = $this->currentUser->employee()->cont_id;
        $post->created_by = $this->currentUser->employee()->id;
        $post->updated_by = $this->currentUser->employee()->id;
        $post->save();
        return redirect()->back()->with('success', 'successfully Added');
    }


    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $post = Post::findorfail($id);
            return view('blog.post.show')->with('post', $post);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $post = Post::findorfail($id);
            $postTypes = PostType::all();
            return view('blog.post.edit')->with('post', $post)->with('postTypes', $postTypes);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function update(Request $request, $id)
    {
        $post = Post::findorfail($id);
        $post->title = $request->title;
        $post->body = $request->body;
        $post->post_type_id = $request->post_type_id;
        $post->file = "";
        if ($request->hasFile('input_file')) {
            $input_doc_file = time() . '.' . $request->input_file->getClientOriginalExtension();
            $request->input_file->move('uploads/blog/attached/', $input_doc_file);
            $post->file = $input_doc_file;
        }
        $post->updated_by = $this->currentUser->employee()->id;
        $post->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $post = Post::findorfail($id);
            $post->status_id = $post->status_id == 1 ? 2 : 1;
            $post->updated_by = $this->currentUser->employee()->id;
            $post->save();
            return redirect('/post');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }
}
