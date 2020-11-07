<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class FileController extends HandleController
{
    public function index(Request $request)
    {
        $file = User::select('name','file_upload')
            ->where('file_upload', '<>', '', 'and')//not null
            ->paginate(5);

        return Response::json([
            'data' => $file
        ]);
    }


    public function show (User $user)
    {
        return Response::json([
            'message' => [
                'id' => $user->id,
                'name' => $user->name,
                'data' => $user->file_upload
            ]
        ]);
    }


    public function create(Request $request, User $user)
    {
        if (!$request->hasFile('file')) {
            return Response::json([
                'Message' => 'No File Found'
            ]);
        }

        $validate = $this->validate($request, [
            'file' => 'required|mimes:csv,txt|max:1999'
        ]);

        if($user->file_upload)
        {
            $fileName = substr($user->file_upload['file'],22);
            Storage::delete('public/file_uploads/'.$fileName);
        }


        if ($validate) {
            $file = $request->file('file');
            $fileNameWithExt = $file->getClientOriginalName();
            $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $fileNameStore = $fileName . "_" . time() . '.' . $extension;

            if (Storage::disk('local')->exists($fileNameStore)) {
                Storage::delete($fileNameStore);
            }

            $path = $file->storeAs('public/file_uploads', $fileNameStore);
            $data = [
                'file' => '/storage/file_uploads'.'/'.$fileNameStore
            ];

            $user->file_upload = $data;
            $user->save();

            return Response::json([
                'Status' => 'Succesfully Uploaded',
                'Data' => $data
            ]);
        }

        return $this->internalError();
    }


    public function delete(User $user)
    {
        $fileName = substr($user->file_upload['file'],22);
        $user->file_upload = null;
        $user->save();

        Storage::delete('public/file_uploads/'.$fileName);

        return Response::json([
            'data' => 'Successfully deleted'
        ]);
    }
}
