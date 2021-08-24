<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;
//session library
use Illuminate\Support\Facades\Session;
use App\M_Supplier;

class Registrasi extends Controller
{
    //
    public function index(){
        $key = env('APP_KEY');
        $token = Session::get('token');
        $data['token'] = $token;
        return view('registrasi.registrasi',$data);
    }

    public function regis(Request $request){

        $key = env('APP_KEY');
        $token = Session::get('token');
        $tokenDb = M_Supplier::where('token',$token)->count();

        $this->validate($request,
            [
                'nama_usaha' => 'required',
                'email' => 'required',
                'alamat' => 'required',
                'npwp' => 'required',
                'password' => 'required'
            ]
        );

        if(M_Supplier::create(
            [
                'nama_usaha' => $request->nama_usaha,
                'email' => $request->email,
                'alamat' => $request->alamat,
                'no_npwp' => $request->npwp,
                'password' => encrypt($request->password)
            ]
        )){
            return redirect('/registrasi')->with('berhasil', 'Data berhasil disimpan');
        }else{
            return redirect('/registrasi')->with('gagal', 'Data gagal disimpan');
        }
    }
}
