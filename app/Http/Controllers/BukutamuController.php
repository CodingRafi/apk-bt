<?php

namespace App\Http\Controllers;

use App\Datatamu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BukutamuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datatamu = DB::table('datatamus')->paginate(5);

        return view('bukutamu',[
            'datatamu' => $datatamu
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('bread_bukutamu.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // foto

        $img =  $request->get('image');
        // dd($img);
        $folderPath = "storage/";
        $image_parts = explode(";base64,", $img);
        foreach ($image_parts as $key => $image){
            $image_base64 = base64_decode($image);
        }

        $fileName = uniqid() . '.png';
        $file = $folderPath . $fileName;
        file_put_contents($file, $image_base64);
        $folderPath2 = "tandaTangan/";
        $img_parts =  explode(";base64,", $request->signed);
        $img_type_aux = explode("image/", $img_parts[0]);
        $img_type = $img_type_aux[1];
        $img_base64 = base64_decode($img_parts[1]);
        $namaTandaTangan =   uniqid() . '.'.$img_type;
        $file = $folderPath2 . $namaTandaTangan;
        file_put_contents($file, $img_base64);

        DB::table('datatamus')->insert([
            [
                'nama' => $request->namatamu,
                'instansi' => $request->instansi,
                'alamat' => $request->alamat,
                'image' => $fileName,
                'tandatangan' => $namaTandaTangan
            ]
        ]);

        return redirect("/bukutamu")->with('success', 'Data submitted Successfully');
        // dump($request->all());
        // ELEQUENT
        // Datatamu::create([
        //     'nama' => $request->namatamu,
        //     'instansi' => $request->instansi,
        //     'nohp' => $request->nohp,
        //     'alamat' => $request->alamat,
        //     'keperluan' => $request->keperluan,
        //     'pegawaitujuan' => $request->pegawaitujuan,
        //     'suhutubuh' => $request->suhutubuh
        // ]);

        // Query Builder

    // panggil validasi form
            // ddd($request);

        // $this->_validation($request);

        // DB::table('datatamus')->insert([
        //     [
        //         'nama' => $request->namatamu,
        //         'instansi' => $request->instansi,
        //         'nohp' => $request->nohp,
        //         'alamat' => $request->alamat,
        //         'keperluan' => $request->keperluan,
        //         'pegawaitujuan' => $request->pegawaitujuan,
        //         'suhutubuh'=>$request->suhutubuh
            
        //     ]
        // ]);

        // return redirect()->route('showdata')->with('notif','Data Berhasil di Simpan!');
    }
    private function _validation(Request $request)
    {
        $request->validate([
            'namatamu' => 'required',
            'instansi' => 'required',
            'alamat'=>'required'
        ],
        // [
        //     'namatamu.required'=>'Nama Tamu harus diisi!'
        // ]
            
    );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $datatamu = DB::table('datatamus')->where('id',$id)->first();
        return view('bread_bukutamu.edit',['datatamu'=>$datatamu]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->_validation($request);
        // return dd($request->all());
        DB::table('datatamus')->where('id',$id)->update([
            'nama'=>$request->namatamu,
            'instansi'=>$request->instansi,
            'alamat'=>$request->alamat
        ]);
        return redirect()->route('showdata')->with('notif','Data Berhasil Di Update!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // dd($id);
        // $gambar = Gambar::where('id',$id)->first();
        $pengunjung = DB::table('datatamus')->where('id',$id);
        Storage::delete($pengunjung->first()->image);
        $pengunjung->delete();

        return redirect()->route('showdata')->with('notifdelete','Data Berhasil DiHapus!');
    }

    
}
