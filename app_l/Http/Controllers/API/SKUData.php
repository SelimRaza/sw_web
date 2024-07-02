<?php
/**
 * Created by PhpStorm.
 * User: 205206
 * Date: 9/4/2018
 * Time: 9:37 AM
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SKUData extends Controller
{
    public function __construct()
    {
        $this->middleware('timezone');
    }
    public function empSkuList(Request $request)
    {
        $tst = DB::select("SELECT
  concat(t4.id, t4.name, t4.code, t4.ln_name, t4.sub_category_id, t4.image, t5.image,t5.name) AS column_id,
  concat(t4.id, t4.name, t4.code, t4.ln_name, t4.sub_category_id, t4.image, t5.image,t5.name) AS token,
  t4.id                                                                               AS sku_id,
  t4.name                                                                             AS sku_name,
  t4.code                                                                             AS sku_code,
  t4.ln_name                                                                          AS sku_ln_name,
  t4.sub_category_id,
  t5.name                                                                             AS sub_category_name,
  t4.image                                                                            AS sku_image,
  t4.image_icon                                                                       AS sku_icon,
  t5.image                                                                            AS sub_category_image,
  t5.image_icon                                                                       AS sub_category_icon,
  t4.ctn_size
FROM tbld_sales_group AS t1
  LEFT JOIN tbld_sales_group_employee AS t2 ON t1.id = t2.sales_group_id
  LEFT JOIN tbld_sales_gorup_sku AS t3 ON t1.id = t3.sales_group_id
  LEFT JOIN tbld_sku AS t4 ON t3.sku_id = t4.id
  LEFT JOIN tbld_sub_category AS t5 ON t4.sub_category_id = t5.id
WHERE t2.emp_id = $request->emp_id and t4.status_id=1
GROUP BY t4.id,t4.name,t4.code,t4.ln_name, t4.sub_category_id,t4.image,t4.image_icon,t5.name,t5.image,t5.image_icon,t4.ctn_size ");
        return Array("tbld_sku" => array("data" => $tst, "action" => $request->emp_id));
    }


}