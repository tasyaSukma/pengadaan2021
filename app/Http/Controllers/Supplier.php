<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//session library
use Illuminate\Support\Facades\Session;

//JWT library
use \Firebase\JWT\JWT;

//Response library
use Illuminate\Response;

//Validator Library
use Illuminate\Support\Facades\Validator;

//encrypt function library
use Illuminate\Contracts\Encryption\DecryptException;

use App\M_Supplier;
use App\M_Admin;

class Supplier extends Controller
{
    //
    public function login(){
        return view('supplier.login');
    }

    public function masukSupplier(Request $request){
        $this->validate($request,
            [
                'email' => 'required',
                'password' => 'required'
            ]
        );

        $cek = M_Supplier::where('email',$request->email)->count();

        $sup = M_Supplier::where('email',$request->email)->get();

        if($cek>0){
            foreach($sup as $su){
                if(decrypt($su->password)== $request->password){
                    $key = env('APP_KEY');
                    $data = array(
                        "id_supplier" => $su->id_supplier
                    );
                    $jwt = JWT::encode($data,$key);
                    if(M_Supplier::where('id_supplier', $su->id_supplier)->update(
                        [
                            'token' => $jwt
                        ])){
                        //kalau berhasil update token di database
                        Session::put('token',$jwt);
                        return redirect('/listSupplier');
                    }else{
                        return redirect('/login')->with('gagal','Token gagal di update');
                    }
                }else{
                    return redirect('/login')->with('gagal','Password Salah');
                }
            }
        }else{
            return redirect('/masukSupplier')->with('gagal','Email Tidak Terdaftar');
        }

    }

    public function keluarSupplier(){
        $token= Session::get('token');

        if(M_Supplier::where('token', $token)->update(
            [
                'token' => 'keluar'
            ]
        )){
            Session::put('token',"");
            return redirect ('/');

        }else{
            return redirect ('/masukSupplier')->with('gagal', 'Anda gagal logout');
        }
    }

    public function listSupplier(){
        $key = env('APP_KEY');
        $token = Session::get('token');
        $tokenDb = M_Admin::where('token',$token)->count();
        $decode = JWT::decode($token,$key, array('HS256'));
        $decode_array = (array) $decode;
        $adm = M_Admin::where('token',$token)->first(); 
        $sup = M_Supplier::where('token',$token)->first();

        if($tokenDb > 0){
            $data['supplier'] = M_Supplier::paginate(15);
            $data['nama'] = $adm->nama;
            return view('admin.listSup', $data);
        }else{
            return redirect ('/masukAdmin')->with('gagal', 'Anda gagal logout, silahkan login kembali');
        }
    }

    public function nonAktif($id){
        $token = Session::get('token');
        $tokenDb = M_Admin::where('token',$token)->count();

        if($tokenDb > 0){
            if(M_Supplier::where('id_supplier',$id)->update([
                "status" => 0
            ])){
                return redirect ('/listSup')->with('berhasil', 'Data berhasil diupdate');
            }else{
                return redirect ('/listSup')->with('gagal', 'Data gagal diupdate');
            }
        }else{
            return redirect ('/masukAdmin')->with('gagal', 'Anda gagal logout, silahkan login kembali');
        }
    }

    public function Aktif($id){
        $token = Session::get('token');
        $tokenDb = M_Admin::where('token',$token)->count();

        if($tokenDb > 0){
            if(M_Supplier::where('id_supplier',$id)->update([
                "status" => 1
            ])){
                return redirect ('/listSup')->with('berhasil', 'Data berhasil diupdate');
            }else{
                return redirect ('/listSup')->with('gagal', 'Data gagal diupdate');
            }
        }else{
            return redirect ('/masukAdmin')->with('gagal', 'Anda gagal logout, silahkan login kembali');
        }
    }

    public function editPasswordSupplier(Request $request){
        $key = env('APP_KEY');
        $token = Session::get('token');
        $tokenDb = M_Supplier::where('token',$token)->count();
        

        if($tokenDb > 0){
            $sup = M_Supplier::where('token',$token)->first();

            $decode = JWT::decode($token,$key, array('HS256'));
            $decode_array = (array) $decode;
            
            if(decrypt($sup->password) == $request->passwordlama){
                if(M_Supplier::where('id_supplier',$decode_array['id_supplier'])->update([
                    "password" => encrypt($request->password)
                ])){
                    return redirect ('/login')->with('gagal', 'Password berhasil diupdate');
                }
            }else{
                return redirect ('/login')->with('gagal', 'Password gagal diupdate, Password Lama dan Password Baru Sama');
            }
            
        }else{
            return redirect ('/masukSupplier')->with('gagal', 'Anda telah logout, silahkan login kembali');
        }
    }
}
