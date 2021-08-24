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

use App\M_Admin;
use App\M_Pengajuan;
use App\M_Supplier;
use App\M_Pengadaan;
use App\M_Laporan;

class Pengajuan extends Controller
{
    //
    public function pengajuan(){
        $key = env('APP_KEY');
        $token = Session::get('token');
        $tokenDb = M_Admin::where('token',$token)->count();
        $decode = JWT::decode($token,$key, array('HS256'));
        $decode_array = (array) $decode;

        //status 1 - belum diterima masih menunggu
        //status 2 - sudah selesai
        //status 0 - ditolak 

        if($tokenDb > 0){
            $pengajuan = M_Pengajuan::where('status','1')->paginate(15);
            $dataP = array();
            foreach($pengajuan as $p){
                $pengadaan = M_Pengadaan::where('id_pengadaan', $p->id_pengadaan)->first();
                $supplier = M_Supplier::where('id_supplier', $p->id_supplier)->first();
                $dataP[] = array(
                    "id_pengajuan" => $p->id_pengajuan,
                    "nama_pengadaan" => $pengadaan->nama_pengadaan,
                    "gambar" => $pengadaan->gambar,
                    "anggaran_pengadaan" => $pengadaan->anggaran,
                    "proposal" => $p->proposal,
                    "anggaran_pengajuan" => $p->anggaran,
                    "status_pengajuan" => $p->status,
                    "nama_supplier" => $supplier->nama_usaha,
                    "email_supplier" => $supplier->email,
                    "alamat_supplier" => $supplier->alamat
                );
            }

            $data['pengajuan'] = $dataP;
            $adm = M_Admin::where('token',$token)->first();
            $data['nama'] = $adm->nama;
            return view('pengajuan.list',$data);
        }else{
            return redirect('/masukAdmin')->with('gagal','Silahkan login terlebih dahulu');
        }
    }

    public function getAdminName(){
        
    }

    public function tambahPengajuan(Request $request){

        $key = env('APP_KEY');
        $token = Session::get('token');
        $tokenDb = M_Supplier::where('token', $token)->count();
        $decode = JWT::decode($token,$key, array('HS256'));
        $decode_array = (array) $decode;

        if($tokenDb>0){
            $this->validate($request,
                [
                    'id_pengadaan' => 'required',
                    'proposal' => 'required|mimes:pdf|max:10240',
                    'anggaran' => 'required'
                ]
            );

            $pengajuan = M_Pengajuan::where('id_supplier',$decode_array['id_supplier'])->where('id_pengadaan',$request->id_pengadaan)->count();

            if($pengajuan == 0){
                $path = $request->file('proposal')->store('public/proposal');
                if(M_Pengajuan::create([
                    'id_pengadaan' => $request->id_pengadaan,
                    'id_supplier' => $decode_array['id_supplier'],
                    'anggaran' => $request->anggaran,
                    'proposal' => $path                
                ])){
                    return redirect('/listSupplier')->with('berhasil', 'Pengajuan berhasil, mohon ditunggu');
                }else{
                    return redirect('/listSupplier')->with('gagal', 'Pengajuan gagal, Mohon Hubungi Admin');
                }
            }else{
                return redirect('/listSupplier')->with('gagal', 'Pengajuan sudah pernah dilakukan');
            }
        }else{
            return redirect ('/masukSupplier')->with('gagal', 'Anda gagal logout, silahkan login kembali');
        }

    }

    public function terimaPengajuan($id){
        $key = env('APP_KEY');
        $token = Session::get('token');
        $tokenDb = M_Admin::where('token',$token)->count();

        //status 1 - belum diterima masih menunggu
        //status 2 - sudah selesai
        //status 0 - ditolak 

        if($tokenDb > 0){
            if(M_Pengajuan::where('id_pengajuan',$id)->update([
                'status' => 2
            ])){
                return redirect('/pengajuan')->with('berhasil','Status pengajuan berhasil diubah');
            }else{
                return redirect('/pengajuan')->with('gagal','Status pengajuan gagal diubah');
            }
        }else{
            return redirect('/masukAdmin')->with('gagal','Silahkan login terlebih dahulu');
        }
    }

     public function tolakPengajuan($id){
        $key = env('APP_KEY');
        $token = Session::get('token');
        $tokenDb = M_Admin::where('token',$token)->count();

        //status 1 - belum diterima masih menunggu
        //status 2 - sudah selesai
        //status 0 - ditolak 

        if($tokenDb > 0){
            if(M_Pengajuan::where('id_pengajuan',$id)->update([
                'status' => 0
            ])){
                return redirect('/pengajuan')->with('berhasil','Status pengajuan berhasil diubah');
            }else{
                return redirect('/pengajuan')->with('gagal','Status pengajuan gagal diubah');
            }
        }else{
            return redirect('/masukAdmin')->with('gagal','Silahkan login terlebih dahulu');
        }
    }

    public function riwayatKu(){
        $key = env('APP_KEY');
        $token = Session::get('token');
        $tokenDb = M_Supplier::where('token', $token)->count();
        $decode = JWT::decode($token,$key, array('HS256'));
        $decode_array = (array) $decode;

        if($tokenDb > 0){
            $pengajuan = M_Pengajuan::where('id_supplier',$decode_array['id_supplier'])->get();
            $dataArr = array();
            foreach($pengajuan as $p){
                $pengadaan = M_Pengadaan::where('id_pengadaan', $p->id_pengadaan)->first();
                $supplier = M_Supplier::where('id_supplier', $decode_array['id_supplier'])->first();
                $laporanCount = M_Laporan::where('id_pengajuan', $p->id_pengajuan)->count();
                $laporanFirst = M_Laporan::where('id_pengajuan', $p->id_pengajuan)->first();

                if($laporanCount > 0){
                    $lapLink = $laporanFirst->laporan;
                }else{
                    $lapLink = "-";
                }

                $dataArr[] = array(
                    "id_pengajuan" => $p->id_pengajuan,
                    "nama_pengadaan" => $pengadaan->nama_pengadaan,
                    "gambar" => $pengadaan->gambar,
                    "anggaran_pengadaan" => $pengadaan->anggaran,
                    "proposal" => $p->proposal,
                    "anggaran_pengajuan" => $p->anggaran,
                    "status_pengajuan" => $p->status,
                    "nama_supplier" => $supplier->nama_usaha,
                    "email_supplier" => $supplier->email,
                    "alamat_supplier" => $supplier->alamat,
                    "laporan" =>$lapLink,
                );
            }
            $data['pengajuan'] = $dataArr;
            $sup = M_Supplier::where('id_supplier',$decode_array['id_supplier'])->first();
            $data['nama_usaha'] = $sup->nama_usaha;
            return view('supplier.riwayat_pengajuan',$data);
        }else{
            return redirect('/listSupplier')->with('gagal', 'Pengajuan sudah pernah dilakukan');
        }
    }

    public function tambahLaporan(Request $request){
        $key = env('APP_KEY');
        $token = Session::get('token');
        $tokenDb = M_Supplier::where('token', $token)->count();
        $decode = JWT::decode($token,$key, array('HS256'));
        $decode_array = (array) $decode;

        if($tokenDb>0){
            $this->validate($request,
                [
                    'id_pengajuan' => 'required',
                    'laporan' => 'required|mimes:pdf|max:10240',
                ]
            );

            $cekLaporan = M_Laporan::where('id_supplier',$decode_array['id_supplier'])->where('id_pengajuan',$request->id_pengajuan)->count();

            if($cekLaporan == 0){
                $path = $request->file('laporan')->store('public/laporan');
                if(M_Laporan::create([
                    'id_pengajuan' => $request->id_pengajuan,
                    'id_supplier' => $decode_array['id_supplier'],
                    'laporan' => $path                
                ])){
                    return redirect('/riwayatKu')->with('berhasil', 'Laporan berhasil diupload');
                }else{
                    return redirect('/riwayatKu')->with('gagal', 'Laporan gagal di upload Mohon Hubungi Admin');
                }
            }else{
                return redirect('/riwayatKu')->with('gagal', 'Laporan sudah pernah diupload');
            }
        }else{
            return redirect ('/masukSupplier')->with('gagal', 'Anda gagal logout, silahkan login kembali');
        }
    }

    public function laporan(){
        $key = env('APP_KEY');
        $token = Session::get('token');
        $tokenDb = M_Admin::where('token',$token)->count();

        //status 1 - belum diterima masih menunggu
        //status 2 - sudah selesai
        //status 0 - ditolak 

        if($tokenDb > 0){
            $pengajuan = M_Pengajuan::where('status','2')->paginate(15);
            $dataP = array();
            foreach($pengajuan as $p){
                $pengadaan = M_Pengadaan::where('id_pengadaan', $p->id_pengadaan)->first();
                $supplier = M_Supplier::where('id_supplier', $p->id_supplier)->first();
                $c_laporan = M_Laporan::where('id_pengajuan', $p->id_pengajuan)->count();
                $laporan = M_Laporan::where('id_pengajuan', $p->id_pengajuan)->first();
                if($c_laporan>0){
                    $dataP[] = array(
                        "id_pengajuan" => $p->id_pengajuan,
                        "nama_pengadaan" => $pengadaan->nama_pengadaan,
                        "gambar" => $pengadaan->gambar,
                        "anggaran_pengadaan" => $pengadaan->anggaran,
                        "proposal" => $p->proposal,
                        "anggaran_pengajuan" => $p->anggaran,
                        "status_pengajuan" => $p->status,
                        "nama_supplier" => $supplier->nama_usaha,
                        "email_supplier" => $supplier->email,
                        "alamat_supplier" => $supplier->alamat,
                        "id_laporan" => $laporan->id_laporan,
                        "laporan" => $laporan->laporan
                    );
                }else{

                }
                
            }

            $data['pengajuan'] = $dataP;
            $adm = M_Admin::where('token',$token)->first();
            $data['nama'] = $adm->nama;
            return view('admin.laporan',$data);
        }else{
            return redirect('/masukAdmin')->with('gagal','Silahkan login terlebih dahulu');
        }
    }

    public function selesaiPengajuan($id){
        $key = env('APP_KEY');
        $token = Session::get('token');
        $tokenDb = M_Admin::where('token',$token)->count();

        //status 1 - belum diterima masih menunggu
        //status 2 - sudah selesai
        //status 0 - ditolak 

        if($tokenDb > 0){
            if(M_Pengajuan::where('id_pengajuan',$id)->update([
                'status' => 3
            ])){
                return redirect('/laporan')->with('berhasil','Status pengajuan berhasil diubah');
            }else{
                return redirect('/laporan')->with('gagal','Status pengajuan gagal diubah');
            }
        }else{
            return redirect('/masukAdmin')->with('gagal','Silahkan login terlebih dahulu');
        }
    }

    public function tolakLaporan($id){
        $key = env('APP_KEY');
        $token = Session::get('token');
        $tokenDb = M_Admin::where('token',$token)->count();

        //status 1 - belum diterima masih menunggu
        //status 2 - sudah selesai
        //status 0 - ditolak 

        if($tokenDb>0){
            $laporan = M_Laporan::where("id_laporan",$id)->count();
            if($laporan>0){
                $dataLaporan = M_Laporan::where('id_laporan',$id)->first();
                if(Storage::delete($dataLaporan->laporan)){
                    if(M_Laporan::where("id_laporan",$id)->delete()){
                        return redirect('/laporan')->with('berhasil', 'Laporan berhasil ditolak');
                    }else{
                        return redirect('/laporan')->with('gagal', 'Laporan gagal ditolak');
                    }
                    
                }else{
                    return redirect('/laporan')->with('gagal', 'Laporan gagal ditolak');
                }
                return redirect('/laporan')->with('berhasil', 'Laporan gagal diterima');
            }else{
                return redirect('/laporan')->with('gagal', 'Data tidak ditemukan');
            }
        }else{
            return redirect ('/masukAdmin')->with('gagal', 'Anda gagal logout, silahkan login kembali');
        }
    }


    public function pengajuanselesai(){
        $key = env('APP_KEY');
        $token = Session::get('token');
        $tokenDb = M_Supplier::where('token', $token)->count();
        $decode = JWT::decode($token,$key, array('HS256'));
        $decode_array = (array) $decode;

        if($tokenDb > 0){
            $pengajuan = M_Pengajuan::where('id_supplier',$decode_array['id_supplier'])->where('status',3)->get();
            $dataArr = array();
            foreach($pengajuan as $p){
                $pengadaan = M_Pengadaan::where('id_pengadaan', $p->id_pengadaan)->first();
                $supplier = M_Supplier::where('id_supplier', $decode_array['id_supplier'])->first();
                $laporanCount = M_Laporan::where('id_pengajuan', $p->id_pengajuan)->count();
                $laporanFirst = M_Laporan::where('id_pengajuan', $p->id_pengajuan)->first();

                if($laporanCount > 0){
                    $lapLink = $laporanFirst->laporan;
                }else{
                    $lapLink = "-";
                }

                $dataArr[] = array(
                    "id_pengajuan" => $p->id_pengajuan,
                    "nama_pengadaan" => $pengadaan->nama_pengadaan,
                    "gambar" => $pengadaan->gambar,
                    "anggaran_pengadaan" => $pengadaan->anggaran,
                    "proposal" => $p->proposal,
                    "anggaran_pengajuan" => $p->anggaran,
                    "status_pengajuan" => $p->status,
                    "nama_supplier" => $supplier->nama_usaha,
                    "email_supplier" => $supplier->email,
                    "alamat_supplier" => $supplier->alamat,
                    "laporan" =>$lapLink
                );
            }
            $data['pengajuan'] = $dataArr;
            $sup = M_Supplier::where('id_supplier',$decode_array['id_supplier'])->first();
            $data['nama_usaha'] = $sup->nama_usaha;
            return view('pengajuan.pengajuanselesai',$data);
        }else{
            return redirect('/listSupplier')->with('gagal', 'Pengajuan sudah pernah dilakukan');
        }
    }
}
