<?php
namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Application;
use App\Models\CaseStudy;

class SiteController extends Controller
{
    public function home()      { return view('home'); }
    public function tech()      { return view('tech'); }
    public function about()     { return view('about'); }

    public function productsIndex() { $items = Product::where('status','published')->orderBy('sort')->get(); return view('products.index', compact('items')); }
    public function productShow(string $slug) { $item = Product::where('slug',$slug)->firstOrFail(); return view('products.show', compact('item')); }

    public function applicationsIndex() { $items = Application::where('status','published')->get(); return view('applications.index', compact('items')); }
    public function applicationShow(string $slug) { $item = Application::where('slug',$slug)->firstOrFail(); return view('applications.show', compact('item')); }

    public function casesIndex() { $items = CaseStudy::where('status','published')->get(); return view('cases.index', compact('items')); }
    public function caseShow(string $slug) { $item = CaseStudy::where('slug',$slug)->firstOrFail(); return view('cases.show', compact('item')); }

    public function contact() { return view('contact'); }
    public function coCreation(){ return view('co_creation'); }
}
