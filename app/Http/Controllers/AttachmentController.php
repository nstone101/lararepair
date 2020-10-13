<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AttachmentRequest;
use App\Repositories\RepairRepository;
use App\Repositories\ActivityLogRepository;
use App\Repair;
use App\Repositories\TaxRepository;

class AttachmentController extends Controller
{
    protected $repo;
    protected $request;
    protected $activity;
    protected $tax;

    protected $module = 'repair';

    public function __construct(Request $request, RepairRepository $repo, ActivityLogRepository $activity,  TaxRepository $tax)
    {
        $this->repo = $repo;
        $this->request = $request;
        $this->activity = $activity;
        $this->tax = $tax;
    }
    


    public function getUploadedFiles($id) {
        $attachments = \App\Attachment::where('repair_id', $id)->get();
        return response()->json($attachments);
    }

    public function deleteAttachment(AttachmentRequest $request) {
        $id = ($request->get('id'));
        $attachment = \App\Attachment::findOrFail($id);
        unlink(public_path('uploads/repairs/').$attachment->filename);
        $attachment->delete();
        return response()->json(['success'=>true]);
    }

    public function upload(AttachmentRequest $request) {
        $imageName = md5($request->file->getClientOriginalName().time()).'.'.$request->file->getClientOriginalExtension();
        $request->file->move(public_path('uploads/repairs'), $imageName);
        $uploadRow = \App\Attachment::create([
            'repair_id'=>$request->get('id'),
            'filename'=>$imageName,
            'label'=>$request->file->getClientOriginalName(),
        ]);
        return response()->json(['success'=>true, 'msg'=>'You have successfully upload file.', 'attachment_id'=>$uploadRow->id]);
    }  

}