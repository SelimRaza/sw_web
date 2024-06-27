<?php

namespace App\Http\Controllers\GroupDataSetup;

use App\MasterData\SKU;
use App\MasterData\SubCategory;
use App\Menu\SubMenu;
use App\Menu\UserMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Image;
use Response;

class GroupSKUController extends Controller
{
    private $access_key = 'tbld_group_sku';
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
           // $skus = SKU::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            $country_id = $this->currentUser->employee()->cont_id;
            $emp_id = $this->currentUser->employee()->id;
            $skus = DB::select("SELECT
  t1.id as id,
  t1.name as name,
  t1.code as code,
  t1.status_id as  status_id,
  t3.name as sales_group,
  t5.name as subCategoryName,
  t1.image_icon
FROM tbld_sku AS t1 
INNER JOIN tbld_sales_gorup_sku AS t2 ON t1.id = t2.sku_id
INNER JOIN tbld_sales_group as t3 ON t2.sales_group_id=t3.id
INNER JOIN tbld_sales_group_employee as t4 ON t2.sales_group_id=t4.sales_group_id
INNER JOIN tbld_sub_category as t5 ON t1.sub_category_id=t5.id
WHERE t4.emp_id= $emp_id and t1.country_id=$country_id");
            return view('master_data.group_sku.index')->with('skus', $skus)->with('permission', $this->userMenu);
        } else {
            return view('theme.access_limit');
        }
    }


    public function create()
    {
        if ($this->userMenu->wsmu_crat) {
            $subCategorys = SubCategory::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            return view('master_data.group_sku.create')->with('subCategorys', $subCategorys);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function store(Request $request)
    {
        $imageName = "";
        $imageIcon = "";
        if ($request->input_img != "") {
            $imageIcon = time() . '.' . $request->input_img->getClientOriginalExtension();
            $file = Input::file('input_img');
            Image::make($file->getRealPath())->fit(128, 128)->save('uploads/image_icon/' . $imageIcon);
            $imageName = time() . '.' . $request->input_img->getClientOriginalExtension();
            $request->input_img->move('uploads/sku_image/', $imageName);

        }
        $sku = new SKU();
        $sku->name = $request->name;
        $sku->code = $request->code;
        $sku->ln_name = $request->ln_name;
        $sku->sub_category_id = $request->sub_category_id;
        $sku->ctn_size = $request->ctn_size;
        $sku->sku_size = $request->sku_size;
        $sku->status_id = 1;
        $sku->country_id = $this->currentUser->employee()->cont_id;
        $sku->created_by = $this->currentUser->employee()->id;
        $sku->updated_by = $this->currentUser->employee()->id;
        $sku->image = $imageName;
        $sku->image_icon = $imageIcon;
        $sku->save();
        return redirect()->back()->with('success', 'successfully Added');
    }


    public function show($id)
    {
        if ($this->userMenu->wsmu_read) {
            $sku = SKU::findorfail($id);
            return view('master_data.group_sku.show')->with('sku', $sku);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }


    public function edit($id)
    {
        if ($this->userMenu->wsmu_updt) {
            $sku = SKU::findorfail($id);
            $subCategorys = SubCategory::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
            return view('master_data.group_sku.edit')->with('subCategorys', $subCategorys)->with('sku', $sku);
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }

    }

    public function update(Request $request, $id)
    {
        $sku = SKU::findorfail($id);
        $sku->name = $request->name;
        $sku->sub_category_id = $request->sub_category_id;
        $sku->code = $request->code;
        $sku->ctn_size = $request->ctn_size;
        $sku->updated_by = $this->currentUser->employee()->id;
        if (isset($request->input_img)) {
            $imageIcon = time() . '.' . $request->input_img->getClientOriginalExtension();
            $file = Input::file('input_img');
            Image::make($file->getRealPath())->fit(128, 128)->save('uploads/image_icon/' . $imageIcon);
            $imageName = time() . '.' . $request->input_img->getClientOriginalExtension();
            $request->input_img->move('uploads/sku_image/', $imageName);
            $sku->image = $imageName;
            $sku->image_icon = $imageIcon;

        }
        $sku->save();
        return redirect()->back()->with('success', 'successfully Updated');
    }

    public function destroy($id)
    {
        if ($this->userMenu->wsmu_delt) {
            $sku = SKU::findorfail($id);
            $sku->status_id = $sku->status_id == 1 ? 2 : 1;
            $sku->updated_by = $this->currentUser->employee()->id;
            $sku->save();
            return redirect('/group_sku');
        } else {
            return redirect()->back()->with('danger', 'Access Limited');
        }
    }

    public function filterSKU(Request $request)
    {
        $skus = SKU::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
        return Response::json($skus);

    }

    public function filterSKU1(Request $request)
    {
        $skus = SKU::where('country_id', '=', $this->currentUser->employee()->cont_id)->get();
        $sku_dropdown = '<option></option>';
        foreach ($skus as $sku) {
            $sku_dropdown .= '<option value="' . $sku->id . '" data-item_code="' . $sku->id
                . '" data-price="' . $sku->ctn_size . '">' . $sku->name . ' ('
                . $sku->name . ')' . '</option>';
        }
        $new_line = '';
        $new_line .= '<tr>';
        $new_line .= '<td><select name="sku_id[]" onchange="get_unit(id)" id="sku_id' . $request->count
            . '" class="form-control" required>' . $sku_dropdown . '</select></td>';
        $new_line .= '<td><input type="text" name="item_code[]" id="item_code' . $request->count
            . '" class="form-control" readonly required/>
                         <input type="hidden" name="ctn_size[]" id="ctn_size' . $request->count . '" value = ""></td>';
        $new_line .= '<td><input type="text" name="unit_price[]" id="unit_price' . $request->count
            . '"  class="form-control"  required/></td>';
        $new_line .= '<td><input type="text" name="quantity[]" id="quantity' . $request->count
            . '" class="form-control integer-input" onkeyup="get_sub_total_qty(id);" required/></td>';
        $new_line .= '<td><input type="text" name="value[]" id="value' . $request->count
            . '" class="form-control"  required/></td>';
        $new_line .= '<td><input type="text" name="value[]" id="value' . $request->count
            . '" class="form-control"  required/></td>';
        $new_line .= '<td><span class="btn btn-xs red removeLine"><i class="fa fa-times"></i>delete</span></td>';

        $new_line .= '</tr>';
        // echo $new_line;
        return $new_line;

    }

}
