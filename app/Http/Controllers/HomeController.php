<?php

namespace App\Http\Controllers;
use Auth;
use DB;
use Illuminate\Http\Request;
use App\User;
use App\Challenge;
use App\Submission;
use Carbon\Carbon;
use App\mcq;
class HomeController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     **/
    public function __construct()
    {
       $this->middleware('auth');
    }

    /*
      Show the application dashboard.

      @return \Illuminate\Http\Response
     */

    public function index()
    {
        $challenges = DB::select('SELECT 
        challenges.tags,
        challenges.desc,
        challenges.created_at,
        challenges.id,
        COUNT(distinct(submissions.id))  AS counts,
        challenges.cname,
        AVG(challenges_rating.rating) as rating,
        U.name AS createdByName
    FROM
        challenges
            LEFT JOIN
        submissions ON challenge_id = challenges.id
            LEFT JOIN   
        users U ON U.id = challenges.user_id
          LEFT JOIN 
          challenges_rating on challenges_rating.challenge_id=challenges.id
    WHERE
        challenges.active = 1
    GROUP BY challenges.tags , challenges.id , challenges.desc , challenges.cname , U.name , challenges.created_at
    ORDER BY challenges.id DESC;');
        $user=user::find(Auth::user()->id);
        foreach($challenges as $challenge)
         {
            $parsed=Carbon::parse($challenge->created_at)->diffForHumans();
            $challenge->parsedTime=$parsed;

         }
        return view('home',['challs'=>$challenges,'user'=>$user]);
    }


    /**
     * New challenge page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function newchallenge()
    {
        return view('newchallengepage');
    }


    /**
     * Update page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function update()
    {
        return view ('Update');
    }

    public function feedback()
    {
       
        DB::table('userfeedback')->insert(
            ['user_id' => Auth::user()->id,
             'rating' => $_POST['smiley'],
             'suggestion'=> $_POST['suggestion']]
        );
        return redirect()->route('home');

    }
    
public function mcq()
{    
    $mcqs=mcq::all();
    return view ('mcq',['mcqs'=>$mcqs]);
} 
public function solve()
    {
        return view('solve');
    }
    public function mcq_submissions()
    {
        return view(' mcq_submissions');
    }

}
