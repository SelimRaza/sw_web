<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 8/28/2018
 * Time: 12:38 PM
 */

namespace App\Blog;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{

    protected $table = 'tbld_post';

    public function postType()
    {
        return PostType::find($this->post_type_id);
    }
}