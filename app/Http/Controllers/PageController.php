<?php
namespace App\Http\Controllers;
use App\Models\Customer;
use App\Models\User;
use Illuminate\View\View;
class PageController extends Controller
{
    public function home(): View
    {
        return view('pages.home', ['title' => 'Home', 'recordCount' => (auth()->check() ? Customer::count() + User::count() : 0)]);
    }
    public function about(): View { return view('pages.about', ['title' => 'About']); }
}
