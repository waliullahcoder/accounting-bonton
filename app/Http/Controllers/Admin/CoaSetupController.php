<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoaSetup;
use App\Models\Scopes\CompanyScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoaSetupController extends Controller
{
    public $hasGL = 0;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coaLists = CoaSetup::root()->with(['children'])->get();
        $coa = $this->menuUi($coaLists);
        $html = '<li class="item"><span>COA</span><ul>' . $coa . '</ul></li>';

        $title = 'Chart of Accounts';
        return view('admin.coa_setup.index', compact('title', 'html'));
    }

    public static function menuUi($coaLists)
    {
        $html = '';
        foreach ($coaLists as $element) {
            $html .= '<li><a href="javascript:" onclick="loadData(' . $element->id . ')">' . $element->head_name . '</a>';
            $subchild = CoaSetupController::subMenu($element->children);
            if (!empty($subchild)) {
                $html .= '<ul>' . $subchild . '</ul>';
            }
            $html .= '</li>';
        }
        return $html;
    }

    public static function subMenu($child)
    {
        $html = '';
        foreach ($child as $element) {
            $html .= '<li><a href="javascript:" onclick="loadData(' . $element->id . ')">' . $element->head_name . '</a>';
            $subchild = CoaSetupController::subMenu($element->children);
            if (!empty($subchild)) {
                $html .= '<ul>' . $subchild . '</ul>';
            }
            $html .= '</li>';
        }
        return $html;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->ajax() && $request->has('parent_id')) {
            $headCodeQuery = CoaSetup::findOrFail($request->parent_id);
            $head_code = $headCodeQuery->head_code . '01';
            $headQuery = CoaSetup::withoutGlobalScope(CompanyScope::class)->where('parent_id',  $request->parent_id)->max('head_code');
            if ($headQuery) {
                $str = substr($headQuery, strlen($headCodeQuery->head_code)) + 1;
                if ($str < 10) {
                    $str = '0' . $str;
                }
                $head_code = $headCodeQuery->head_code . '' . $str;
            }
            return response()->json(['status' => 'success', 'parent_head' => $headCodeQuery->head_name, 'head_code' => $head_code]);
        }

        if ($request->ajax()) {
            $data = CoaSetup::with('children')->where('id', $request->id)->first();
            if (count($data->children) > 0) {
                $this->checkGL($data->children);
            }
            $hasGL = $this->hasGL;
            $children = count($data->children);
            $parent_head_name = @$data->parent->head_name;
            $parent_head_general = @$data->parent->general;
            $form_link = Route('admin.coa-setup.update', $request->id);
            return response()->json(['status' => 'success', 'data' => $data, 'form_link' => $form_link, 'parent_head_name' => $parent_head_name, 'children' => $children, 'parent_head_general' => $parent_head_general, 'hasGL' => $hasGL]);
        }
    }

    private function checkGL($children)
    {
        foreach ($children as $item) {
            if ($item->general == 1) {
                $this->hasGL = 1;
            }
            if ($item->general == 0 && count($item->children) > 0) {
                $this->checkGL($item->children);
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'head_code' => 'required',
            'head_name' => 'required',
            'head_type' => 'required',
        ]);

        $coa = CoaSetup::where('company_id', Auth::user()->company_id ?? 1)->where('head_code', $request->head_code)->first();
        if ($coa) {
            return redirect()->back()->withErrors('Accounts already added!');
        }

        CoaSetup::create([
            'company_id' => Auth::user()->company_id ?? 1,
            'parent_id' => $request->parent_id,
            'head_code' => $request->head_code,
            'head_name' => $request->head_name,
            'transaction' => $request->has('transaction') ? 1 : 0,
            'general' => $request->has('general') ? 1 : 0,
            'head_type' => $request->head_type,
            'status' => $request->has('status') ? 1 : 0,
            'created_by' => Auth::user()->id,
        ]);
        return redirect()->back()->withSuccessMessage('Created Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = CoaSetup::findOrFail($id);
        $data->update([
            'head_code' => $request->head_code,
            'head_name' => $request->head_name,
            'transaction' => $request->has('transaction') ? 1 : 0,
            'general' => $request->has('general') ? 1 : 0,
            'head_type' => $request->head_type,
            'status' => $request->has('status') ? 1 : 0,
            'updated_by' => Auth::user()->id,
        ]);
        return redirect()->back()->withSuccessMessage('Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
