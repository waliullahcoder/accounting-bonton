<?php

namespace App\Http\Controllers\Admin;

use App\HelperClass;
use App\Models\User;
use App\Models\AccountTransaction;
use Illuminate\Support\Facades\DB;
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
        /*
        |--------------------------------------------------------------------------
        | Last 12 Months
        |--------------------------------------------------------------------------
        */

        $startDate = now()->startOfMonth()->subMonths(11);
        $endDate   = now()->endOfMonth();


        /*
        |--------------------------------------------------------------------------
        | Last 12 Months Income / Expense
        |--------------------------------------------------------------------------
        */

        $transactions = AccountTransaction::query()
            ->withoutGlobalScopes()

            ->join('coa_setups', function ($join) {
                $join->on(
                    'coa_setups.id',
                    '=',
                    'account_transactions.coa_setup_id'
                )
                ->whereNull('coa_setups.deleted_at');
            })

            ->whereBetween(
                'account_transactions.voucher_date',
                [
                    $startDate->toDateString(),
                    $endDate->toDateString()
                ]
            )

            ->whereNull('account_transactions.deleted_at')

            ->selectRaw('
                YEAR(account_transactions.voucher_date) as year,
                MONTH(account_transactions.voucher_date) as month,

                SUM(
                    CASE
                        WHEN coa_setups.head_type = "I"
                        THEN account_transactions.credit_amount
                        ELSE 0
                    END
                ) as total_income,

                SUM(
                    CASE
                        WHEN coa_setups.head_type = "E"
                        THEN account_transactions.debit_amount
                        ELSE 0
                    END
                ) as total_expense
            ')

            ->groupByRaw('
                YEAR(account_transactions.voucher_date),
                MONTH(account_transactions.voucher_date)
            ')

            ->orderByRaw('
                YEAR(account_transactions.voucher_date),
                MONTH(account_transactions.voucher_date)
            ')

            ->get();


        /*
        |--------------------------------------------------------------------------
        | Prepare Last 12 Months Data
        |--------------------------------------------------------------------------
        */

        $months = [];
        $income = [];
        $expense = [];

        for ($i = 11; $i >= 0; $i--) {

            $date = now()->startOfMonth()->subMonths($i);

            $key = $date->year . '-' . $date->month;

            $transaction = $transactions->first(function ($item) use ($date) {
                return (int) $item->year === (int) $date->year
                    && (int) $item->month === (int) $date->month;
            });

            $months[] = $date->format('M Y');

            $income[] = $transaction
                ? (float) $transaction->total_income
                : 0;

            $expense[] = $transaction
                ? (float) $transaction->total_expense
                : 0;
        }


        /*
        |--------------------------------------------------------------------------
        | Total
        |--------------------------------------------------------------------------
        */

        $totalIncome  = array_sum($income);
        $totalExpense = array_sum($expense);

        $netBalance = $totalIncome - $totalExpense;


        /*
|--------------------------------------------------------------------------
| Current Month Head Wise Income
|--------------------------------------------------------------------------
*/

$currentMonthIncomeHeads = AccountTransaction::with('coa')
    ->whereYear('voucher_date', now()->year)
    ->whereMonth('voucher_date', now()->month)
    ->whereHas('coa', function ($query) {
        $query->where('head_type', 'I');
    })
    ->select(
        'coa_head_code',
        'coa_setup_id',
        DB::raw('SUM(credit_amount - debit_amount) as amount')
    )
    ->groupBy('coa_setup_id', 'coa_head_code')
    ->get()
    ->map(function ($item) {
        return [
            'code' => $item->coa_head_code,
            'name' => $item->coa->head_name
                ?? $item->coa->name
                ?? $item->coa_head_code,
            'amount' => (float) $item->amount,
            'type' => 'Income',
        ];
    })
    ->filter(function ($item) {
        return $item['amount'] > 0;
    })
    ->values();


/*
|--------------------------------------------------------------------------
| Current Month Head Wise Expense
|--------------------------------------------------------------------------
*/

$currentMonthExpenseHeads = AccountTransaction::with('coa')
    ->whereYear('voucher_date', now()->year)
    ->whereMonth('voucher_date', now()->month)
    ->whereHas('coa', function ($query) {
        $query->where('head_type', 'E')
              ->where('transaction', 1);
    })
    ->select(
        'coa_head_code',
        'coa_setup_id',
        DB::raw('SUM(debit_amount - credit_amount) as amount')
    )
    ->groupBy('coa_setup_id', 'coa_head_code')
    ->get()
    ->map(function ($item) {
        return [
            'code' => $item->coa_head_code,
            'name' => $item->coa->head_name
                ?? $item->coa->name
                ?? $item->coa_head_code,
            'amount' => (float) $item->amount,
            'type' => 'Expense',
        ];
    })
    ->filter(function ($item) {
        return $item['amount'] > 0;
    })
    ->values();


/*
|--------------------------------------------------------------------------
| Current Month Pie Chart Data
|--------------------------------------------------------------------------
*/

$currentMonthPieData = $currentMonthIncomeHeads
    ->concat($currentMonthExpenseHeads)
    ->values();
//dd($currentMonthIncomeHeads,$currentMonthExpenseHeads);
        return view('admin.profile.dashbaord', compact(
            'months',
            'income',
            'year',
            'expense',
            'totalIncome',
            'totalExpense',
            'netBalance',
            'currentMonthIncomeHeads',
            'currentMonthExpenseHeads',
            'currentMonthPieData'
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
