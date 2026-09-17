<?php

namespace App\Http\Controllers\Admin;

use App\HelperClass;
use App\Models\User;
use App\Models\AccountTransaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::check()) {
            if (in_array(Auth::user()->role, [1, 2])) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->back();
        } else {
            if (!session()->has('intended_url')) {
                session(['intended_url' => url()->previous()]);
            }
            return view('admin.auth.login');
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only('user_name', 'password');
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        } else {
            return redirect()->back()->with('error', 'Invalid Email or Password!');
        }
    }

   public function dashboard(Request $request)
    {

     $year = $request->get('year', now()->year);

	$transactions = AccountTransaction::query()
	    ->withoutGlobalScopes() // ❗ disable all scopes first
	
	    ->join('coa_setups', function ($join) {
	        $join->on('coa_setups.id', '=', 'account_transactions.coa_setup_id')
	             ->whereNull('coa_setups.deleted_at');
	    })
	
	    ->whereYear('account_transactions.voucher_date', $year)
	
	    // ✅ apply company filter ONLY once (explicit)
	    // ->where('account_transactions.company_id', auth()->user()->company_id)
	    ->whereNull('account_transactions.deleted_at')
	
	    ->selectRaw('
	        MONTH(account_transactions.voucher_date) as month,
	
	        SUM(CASE 
	            WHEN coa_setups.head_type = "I" 
	            THEN account_transactions.credit_amount 
	            ELSE 0 
	        END) as total_income,
	
	        SUM(CASE 
	            WHEN coa_setups.head_type = "E" 
	            THEN account_transactions.debit_amount 
	            ELSE 0 
	        END) as total_expense
	    ')
	    ->groupByRaw('MONTH(account_transactions.voucher_date)')
	    ->orderByRaw('MONTH(account_transactions.voucher_date)')
	    ->get();
		
        $months = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];

		$income = array_fill(0, 12, 0);
		$expense = array_fill(0, 12, 0);
		
		foreach ($transactions as $t) {
		    $index = $t->month - 1;
		
		    $income[$index] = (float) $t->total_income;
		    $expense[$index] = (float) $t->total_expense;
		}

        $totalIncome = array_sum($income);
        $totalExpense = array_sum($expense);
        $netBalance = $totalIncome - $totalExpense;

        return view('admin.profile.dashbaord', compact(
            'year',
            'months',
            'income',
            'expense',
            'totalIncome',
            'totalExpense',
            'netBalance'
        ));
    }


    /**
     * Manage Sidebar
     */
    public function sidebar()
    {
        if (!Session::has('sidebar-collapse')) {
            Session()->put('sidebar-collapse', 'active');
        } else {
            Session::forget('sidebar-collapse');
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function edit()
    {
        $admin = Auth::user();
        return view('admin.profile.index', compact('admin'));
    }

    public function changeImages(Request $request)
    {
        $data = User::findOrFail(Auth::user()->id);
        $data->update([
            'cover_image' =>  isset($request->cover_image) ? HelperClass::saveImage($request->cover_image, 1200, 'backend/images/avatar') : @$data->cover_image,
            'image' =>  isset($request->profile_image) ? HelperClass::saveImage($request->profile_image, 1200, 'backend/images/avatar') : @$data->image,
        ]);
        return redirect()->back()->withSuccessMessage('Image Changed Successfully!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'email' => 'unique:users,email,' . Auth::user()->id,
            'name' => 'required',
        ]);
        $admin = User::findOrFail(Auth::user()->id);
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->phone = $request->phone;
        $admin->address = $request->address;
        $admin->save();
        return redirect()->back()->withSuccessMessage('Information Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => ['required', Password::min(8)
                ->symbols(), 'confirmed'],
        ]);

        $admin = User::findOrFail(Auth::user()->id);
        if (Hash::check($request->old_password, $admin->password)) {
            $admin->password = bcrypt($request->new_password);
            $admin->save();
            return redirect()->back()->withSuccessMessage('Updated Successfully!');
        } else {
            return redirect()->back()->withErrors('Old Password Does not Matched!');
        }
    }

    public function logout(Request $request)
    {
        Session::put('url.intended', url()->previous());
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login.index');
    }
}
