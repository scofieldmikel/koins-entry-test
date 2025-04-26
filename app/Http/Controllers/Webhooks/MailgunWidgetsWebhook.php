<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessMailgunFile;
use App\Jobs\ProcessMandateNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MailgunWidgetsWebhook extends Controller
{
    public function handle(Request $request)
    {
        \Log::info(print_r($request->all(), true));

        if (Str::contains($request->subject, 'Direct Debit Update For')) {
            ProcessMandateNotification::dispatch($request->all());

            return response()->json(['status' => 'ok'], 200);
        }

        //        $files = collect(json_decode($request->input('attachments'), true))
        //            ->filter(function ($file) {
        //                if ($file['content-type'] == 'image/jpeg') {
        //                    return true;
        //                }
        //
        //                if ($file['content-type'] == 'image/png') {
        //                    return true;
        //                }
        //
        //                if ($file['content-type'] == 'image/jpg') {
        //                    return true;
        //                }
        //            });
        //        if ($files->count() === 0) {
        //            return response()->json([
        //                'status' => 'error',
        //                'message' => 'Missing expected Image attachment',
        //            ], 406);
        //        }
        //
        //        if ($request->sender === 'flickwheelmandate@gmail.com') {
        //            $terms = explode('~', $request->subject);
        //            $request->merge([
        //                'subject' => $terms[0],
        //                'sender' => $terms[1],
        //            ]);
        //        }
        //
        //        dispatch(new ProcessMailgunFile($files, $request->sender, $request->subject));

        return response()->json(['status' => 'ok'], 200);
    }
}
