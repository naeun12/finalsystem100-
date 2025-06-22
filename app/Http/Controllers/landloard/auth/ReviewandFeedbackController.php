<?php

namespace App\Http\Controllers\landloard\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReviewandFeedbackController extends Controller
{
    public function ReviewandFeedback()
    {
        return view ('landlord.auth.ReviewandFeedback', ['title' => 'Landlord - Review and Feedback',
        'headerName' => 'Review and Feedback'
    ]);
    }
}
