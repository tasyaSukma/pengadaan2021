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

use App\M_Admin;

class Admin extends Controller
{
    public function index(){
        return view('admin.login');
    }

    // public function adminGenerate(){
    //     M_Admin::create([
    //         'nama' => "Admin",
    //         'email' => "admin@gmail.com",
    //         'alamat' => "Jalan Alamat No.2",
    //         'password' => encrypt("admin")
    //     ]);
    // }
    
    public function masukAdmin(Request $request){
        $this->validate($request,
            [
                'email' => 'required',
                'password' => 'required'
            ]
        );

        $cek = M_Admin::where('email', $request->email)->count();
        $adm = M_Admin::where('email',$request->email)->get();
        
        if($cek>0){
            foreach ($adm as $ad) {
                if(decrypt($ad->password) == $request->password){
                        $key = env('APP_KEY');
                        $data = array(
                            "id_admin" => $ad->id_admin
                        );
                        $jwt = JWT::encode($data, $key);
                        if(M_Admin::where('id_admin', $ad->id_admin)->update(
                            [
                                'token' =>$jwt
                            ])){
                                Session::put('token',$jwt);
                                // return redirect('/');
                                return redirect('/pengajuan')->with('berhasil','Selamat datang kembali');
                        }else{
                            return redirect('/masukAdmin' ->with('gagal', 'Token gagal di update'));
                        }
                    }else{
                        return redirect('/masukAdmin')->with('gagal','Pasword Salah');
                    }
            }
        }else{
            return redirect('/masukAdmin')->with('gagal','Email Tidak Terdaftar');
        }
    }

    public function keluarAdmin(){
        $token= Session::get('token');

        if(M_Admin::where('token', $token)->update(
            [
                'token' => 'keluar'
            ]
        )){
            Session::put('token',"");
            return redirect ('/masukAdmin')->with('gagal', 'anda sudah logout, silahkan login kembali');

        }else{
            return redirect ('/masukAdmin')->with('gagal', 'Anda gagal logout');
        }
    }

    public function listAdmin(){
        $key = env('APP_KEY');
        $token = Session::get('token');
        $tokenDb = M_Admin::where('token',$token)->count();
        $decode = JWT::decode($token,$key, array('HS256'));
        $decode_array = (array) $decode;
        $adm = M_Admin::where('token',$token)->first();

        if($tokenDb > 0){
            $data['admin'] = M_Admin::where('status','1')->paginate(15);
            $data['nama'] = $adm->nama;
            return view('admin.list', $data);
        }else{
            return redirect ('/masukAdmin')->with('gagal', 'Anda gagal logout, silahkan login kembali');
        }
    }

    public function tambahAdmin(Request $request){
        $this->validate($request,
            [
                'nama' => 'required',
                'email' => 'required',
                'password' => 'required',
                'alamat' => 'required'
            ]
        );
        $token = Session::get('token');
        $tokenDb = M_Admin::where('token', $token)->count();
        if($tokenDb>0){
            if(M_Admin::create([
                "nama" => $request->nama,
                "email" => $request->email,
                "alamat" => $request->alamat,
                'password' => encrypt($request->password)
            ])){
                return redirect('/listAdmin')->with('berhasil', 'Data berhasil disimpan');
            }else{
                return redirect('/listAdmin')->with('gagal', 'Data gagal disimpan');
            }
        }else{
            return redirect ('/masukAdmin')->with('gagal', 'Anda gagal logout, silahkan login kembali');
        }
    }

    public function ubahAdmin(Request $request){
        $this->validate($request,
            [
                'u_nama' => 'required',
                'u_email' => 'required',
                'u_alamat' => 'required'
            ]
        );
        $token = Session::get('token');
        $tokenDb = M_Admin::where('token', $token)->count();
        if($tokenDb>0){
            if(M_Admin::where("id_admin", $request->id_admin)->update([
                "nama" => $request->u_nama,
                "email" => $request->u_email,
                "alamat" => $request->u_alamat
            ])){
                return redirect('/listAdmin')->with('berhasil', 'Data berhasil diubah');
            }else{
                return redirect('/listAdmin')->with('gagal', 'Data gagal diubah');
            }
        }else{
            return redirect ('/masukAdmin')->with('gagal', 'Anda gagal logout, silahkan login kembali');
        }
    }

    public function hapusAdmin($id){
       
        $token = Session::get('token');
        $tokenDb = M_Admin::where('token', $token)->count();
        if($tokenDb>0){
            if(M_Admin::where("id_admin", $id)->delete()){
                return redirect('/listAdmin')->with('berhasil', 'Data berhasil dihapus');
            }else{
                return redirect('/listAdmin')->with('gagal', 'Data gagal dihapus');
            }
        }else{
            return redirect ('/masukAdmin')->with('gagal', 'Anda gagal logout, silahkan login kembali');
        }
    }

    public function editPasswordAdmin(Request $request){
        $key = env('APP_KEY');
        $token = Session::get('token');
        $tokenDb = M_Admin::where('token',$token)->count();
        

        if($tokenDb > 0){
            $adm = M_Admin::where('token',$token)->first();

            $decode = JWT::decode($token,$key, array('HS256'));
            $decode_array = (array) $decode;

            if(decrypt($adm->password) == $request->passwordlama){
                if(M_Admin::where('id_admin',$adm->id_admin)->update([
                    "password" => encrypt($request->password)
                ])){
                    return redirect ('/masukAdmin')->with('gagal', 'Password berhasil diupdate');
                }
            }else{
                return redirect ('/masukAdmin')->with('gagal', 'Password gagal diupdate, Password Lama dan Password Baru Sama');
            }
            
        }else{
            return redirect ('/masukAdmin')->with('gagal', 'Anda telah logout, silahkan login kembali');
        }
    }
}
