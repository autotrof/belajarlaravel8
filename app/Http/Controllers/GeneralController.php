<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\MasterItem;
use \App\Models\User;
use Illuminate\Support\Facades\Auth;

class GeneralController extends Controller
{
    public function index()
    {
        $satu_item = MasterItem::where('id',2)->first();
        // $satu_item->nama = "Jaket";
        $satu_item->delete();

        // $satu_item->update([
        //     'nama'=>'Topi',
        //     'harga_beli'=>30000
        // ]);

        // dd($satu_item);
        // $all_items = MasterItem::select(['nama','harga_beli'])->get()->toArray();
        // dd($all_items);
        $data = [
            'title'=>"CONTOH",
            'teks'=>'Ini Halaman index',
            // 'item'=>$all_items
        ];
        // MasterItem::create([
            // 'nama'=>"Baju Merah",
            // 'deskripsi'=>'Ini produk baju merah',
            // 'harga_beli'=>200000
        // ]);
        // $master_item = new MasterItem();
        // $master_item->fill([
        //     'nama'=>"Baju Merah",
        //     'deskripsi'=>'Ini produk baju merah',
        //     'harga_beli'=>200000
        // ]);
        // $master_item->nama = "Baju Kuning";
        // $master_item->deskripsi = "Ini Deskripsi Baju Kuning";
        // $master_item->harga_beli = 21000;
        // $master_item->save();
        return view('halaman_index',$data);
    }

    public function admin()
    {
        return view('index');
    }

    public function getListMasterItem()
    {
        return MasterItem::all();
    }

    public function insertMasterItem()
    {
        MasterItem::create(request()->except(['_token']));
        return response()->json(true);
    }

    public function getSingleItem()
    {
        $item = MasterItem::findOrFail(request()->input('id'));
        return $item;
    }

    public function updateMasterItem()
    {
        MasterItem::where('id',request()->input('id'))
        ->update(request()->only(['nama','harga_beli','deskripsi']))
        ;
        return response()->json(true);
    }

    public function deleteSingleItem()
    {
        $id = request()->input('id');
        $master_item = MasterItem::findOrFail($id);
        $master_item->delete();
        return response()->json(true);
    }

    public function loginPage()
    {
        return view('login');
    }

    public function doLogin()
    {
        // dd(request()->all());
        $user = User::where('email',request()->input('email'))->firstOrFail();
        // if (\Hash::check(request()->input('password'), $user->password)) {
        //     return response()->json(true);
        // }
        $credentials = request()->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        // $credentials = request()->only([
        //     'email','password'
        // ]);
        if (Auth::attempt($credentials)) {
            request()->session()->regenerate();
            return response()->json(true);
        }
        return response()->json(false);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('login');
    }

    public function dataGudang()
    {
        $columns = [
            'foto',
            'nama',
            'deskripsi',
            'harga_beli',
            'updated_at'
        ];
        $orderBy = $columns[request()->input("order.0.column")];
        $data = MasterItem::select('*');

        if(request()->input("search.value")){
            $data = $data->where(function($query){
                $query->whereRaw('LOWER(nama) like ?',['%'.strtolower(request()->input("search.value")).'%'])
                ->orWhereRaw('LOWER(deskripsi) like ?',['%'.strtolower(request()->input("search.value")).'%'])
                ;
            });
        }

        $recordsFiltered = $data->get()->count();
        $data = $data
            ->skip(request()->input('start'))
            ->take(request()->input('length'))
            ->orderBy($orderBy,request()->input("order.0.dir"))
            ->get()
            ;
        $recordsTotal = $data->count();

        return response()->json([
            'draw'=>request()->input('draw'),
            'recordsTotal'=>$recordsTotal,
            'recordsFiltered'=>$recordsFiltered,
            'data'=>$data,
            'all_request'=>request()->all()
        ]);
    }
}
