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

//import storage
use Illuminate\Support\Facades\Storage;

use App\M_Pengadaan;
use App\M_Admin;
use App\M_Supplier;

class Pengadaan extends Controller
{
    //
    public function index(){
        $key = env('APP_KEY');
        $token = Session::get('token');
        $tokenDb = M_Admin::where('token',$token)->count();
        $decode = JWT::decode($token,$key, array('HS256'));
        $decode_array = (array) $decode;
        $adm = M_Admin::where('token',$token)->first();
        if($tokenDb > 0){
            $data['pengadaan'] = M_Pengadaan::where('status','1')->paginate(15);
            $data['nama'] = $adm->nama;
            return view('pengadaan.list',$data);
        }else{
            return redirect ('/masukAdmin')->with('gagal', 'Anda gagal logout, silahkan login kembali');
        }        
    }

    public function tambahPengadaan(Request $request){

        $token = Session::get('token');
        $tokenDb = M_Admin::where('token', $token)->count();
        if($tokenDb>0){
            $this->validate($request,
                [
                    'nama_pengadaan' => 'required',
                    'deskripsi' => 'required',
                    'gambar' => 'required|image|mimes:jpg,png,jpeg|max:10000',
                    'anggaran' => 'required'
                ]
            );
            $path = $request->file('gambar')->store('public/gambar');
            if(M_Pengadaan::create([
                'nama_pengadaan' => $request->nama_pengadaan,
                'deskripsi' => $request->deskripsi,
                'gambar' => $path,
                'anggaran' => $request->anggaran
            ])){
                return redirect('/listPengadaan')->with('berhasil', 'Data berhasil disimpan');
            }else{
                return redirect('/listPengadaan')->with('gagal', 'Data gagal disimpan');
            }
        }else{
            return redirect ('/masukAdmin')->with('gagal', 'Anda gagal logout, silahkan login kembali');
        }

    }

    public function hapusGambar($id){
        $token = Session::get('token');
        $tokenDb = M_Admin::where('token', $token)->count();
        if($tokenDb>0){
            $pengadaan = M_Pengadaan::where("id_pengadaan",$id)->count();
            if($pengadaan>0){
                $dataPengadaan = M_Pengadaan::where('id_pengadaan',$id)->first();
                if(Storage::delete($dataPengadaan->gambar)){
                    if(M_Pengadaan::where("id_pengadaan",$id)->update(["gambar"=>"-"])){
                        return redirect('/listPengadaan')->with('berhasil', 'Gambar berhasil dihapus');
                    }else{
                        return redirect('/listPengadaan')->with('gagal', 'Gambar gagal dihapus');
                    }
                    
                }else{
                    return redirect('/listPengadaan')->with('gagal', 'Gambar gagal dihapus');
                }
                return redirect('/listPengadaan')->with('berhasil', 'Gambar berhasil dihapus');
            }else{
                return redirect('/listPengadaan')->with('gagal', 'Data tidak ditemukan');
            }
        }else{
            return redirect ('/masukAdmin')->with('gagal', 'Anda gagal logout, silahkan login kembali');
        }
    }

    public function uploadGambar(Request $request){
        $token = Session::get('token');
        $tokenDb = M_Admin::where('token', $token)->count();
        if($tokenDb>0){
            $this->validate($request,
                [
                    'gambar' => 'required|image|mimes:jpg,png,jpeg|max:10000'
                ]
            );
            $path = $request->file('gambar')->store('public/gambar');
            if(M_Pengadaan::where('id_pengadaan',$request->id_pengadaan)->update([
                'gambar' => $path,
            ])){
                return redirect('/listPengadaan')->with('berhasil', 'Data berhasil disimpan');
            }else{
                return redirect('/listPengadaan')->with('gagal', 'Data gagal disimpan');
            }
        }else{
            return redirect ('/masukAdmin')->with('gagal', 'Anda gagal logout, silahkan login kembali');
        }
    }

    public function ubahPengadaan(Request $request){
        $token = Session::get('token');
        $tokenDb = M_Admin::where('token', $token)->count();
        if($tokenDb>0){
            $this->validate($request,
                [
                    'u_nama_pengadaan' => 'required',
                    'u_deskripsi' => 'required',
                    'u_anggaran' => 'required'
                ]
            );
            if(M_Pengadaan::where('id_pengadaan',$request->id_pengadaan)->update([
                'nama_pengadaan' => $request->u_nama_pengadaan,
                'deskripsi' => $request->u_deskripsi,
                'anggaran' => $request->u_anggaran
            ])){
                return redirect('/listPengadaan')->with('berhasil', 'Data berhasil diubah');
            }else{
                return redirect('/listPengadaan')->with('gagal', 'Data gagal diubah');
            }
        }else{
            return redirect ('/masukAdmin')->with('gagal', 'Anda gagal logout, silahkan login kembali');
        }
    }

    public function hapusPengadaan($id){
       
        $token = Session::get('token');
        $tokenDb = M_Admin::where('token', $token)->count();
        if($tokenDb>0){
            $pengadaan = M_Pengadaan::where("id_pengadaan",$id)->count();
            if($pengadaan>0){
                $dataPengadaan = M_Pengadaan::where('id_pengadaan',$id)->first();
                if(Storage::delete($dataPengadaan->gambar)){
                    if(M_Pengadaan::where("id_pengadaan",$id)->delete()){
                        return redirect('/listPengadaan')->with('berhasil', 'Data berhasil dihapus');
                    }else{
                        return redirect('/listPengadaan')->with('gagal', 'Data gagal dihapus');
                    }
                    
                }else{
                    return redirect('/listPengadaan')->with('gagal', 'Data gagal dihapus');
                }
                return redirect('/listPengadaan')->with('berhasil', 'Data berhasil dihapus');
            }else{
                return redirect('/listPengadaan')->with('gagal', 'Data tidak ditemukan');
            }
        }else{
            return redirect ('/masukAdmin')->with('gagal', 'Anda gagal logout, silahkan login kembali');
        }
    }

    public function listSupplier(){
        $key = env('APP_KEY');
        $token = Session::get('token');
        $tokenDb = M_Supplier::where('token',$token)->count();
        $decode = JWT::decode($token,$key, array('HS256'));
        $decode_array = (array) $decode;

        if($tokenDb > 0){
            $data['token'] = $token;
            $data['pengadaan'] = M_Pengadaan::where('status','1')->paginate(15);
            $sup = M_Supplier::where('id_supplier',$decode_array['id_supplier'])->first();
            $data['nama_usaha'] = $sup->nama_usaha;
            return view('supplier.pengadaan',$data);
        }else{
            return redirect ('/masukSupplier')->with('gagal', 'Anda gagal logout, silahkan login kembali');
        }        
    }
}
