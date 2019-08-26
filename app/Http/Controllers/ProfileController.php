<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Image;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        return view('auth.profile');
    }

    

    public function updateProfile(Request $request){
        //Form validation
        $request->validate([
            'name' => 'required',
            'gambar' => 'required|image|mimes:jpeg,png,jpgF,gif|max:2048'
        ]);
        //get current user
        $user = User::findOrFail(auth()->user()->id);
        //set username
        $user->name= $request->input('name');

        //check if a profile image has been uploaded
        if($request->has('gambar')){
            //get image file
            $imagess = $request->file('gambar');

            //ambil foto untuk digabung
            $foto = Image::make($imagess)->resize(1000,1000);
            // create a new Image instance for inserting
            $twibbon = 'storage/image/Twibbon_GERIGI_ITS_2019.png';   
            //return 'hehehehehe';
            
            // Insert the logo onto the background
            $foto->insert($twibbon, 'center')->encode('jpg');

            //make an image name based on user name and current timestamp
            $name = str_slug($request->input('name')).'_'.time();
            //define folder path
            $folder = '/image/';
            //make a file path where will be stored [folder path + file name + file extention]
            $filePath = $folder.$name.'.'.$imagess->getClientOriginalExtension();

            //set user profile image path in database to filepath
            $user->gambar = $filePath;
            Storage::put($filePath, $foto->__toString());
        }
        //persist user record to database
        $user->save();

        //return user back and show a flash message
        return redirect()->back()->with(['status' => 'Berhasil mengupdate profil']);
    }

    public function deleteProfile(Request $request){
        $rekues = $request->input('id-user');
        $user = User::find($rekues);

        $user->delete();
        return redirect('/')->with('status', 'Berhasil Menghapus akun');
    }

}
