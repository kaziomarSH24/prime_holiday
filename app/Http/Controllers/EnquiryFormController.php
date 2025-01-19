<?php

namespace App\Http\Controllers;

use App\Mail\EnquiryFormMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class EnquiryFormController extends Controller
{
    public function sendEnquiry(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|email',
                'telephone' => 'required|numeric',
                'enquiry' => 'required|string',
                ]);

                if ($validator->fails()) {
                    return response()->json(['error' => $validator->errors()], 400);
                }

                $formData = $validator->validated();

                // Send email
                Mail::to($formData['email'])->send(new EnquiryFormMail($formData));

                //send email to admin
                $adminEmail = env('ADMIN_EMAIL');
                Mail::to($adminEmail)->send(new EnquiryFormMail($formData, true));

                return response()->json([
                    'success' => true,
                    'message' => 'Enquiry sent successfully'
                ], 200);

        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending the enquiry',
                // 'error' => $e->getMessage()
            ], 500);
        }

    }

}
