<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Api\QA;
use App\Models\Api\Solutions;
use App\Models\Api\UserSolutions;
use Illuminate\Support\Facades\DB;




class ApiController extends BaseController
{


    public function getUsers()
    {
        $users = User::all();
        return response()->json(['success' => true, 'data' => $users], 200);
    }



    public function register(Request $request)
    {
        $email = $request->email;
        $password = $request->password;
        $name = $request->name;
        $number = $request->number;
        $type = $request->type;
        $id_token = $request->id_token;
    
        $request->validate([
            'email' => 'required|email',
            'password' => 'nullable|string',
            'name' => 'required|string',
            'number' => 'required|integer',
            'type' => 'nullable|string',
            'id_token' => 'nullable|string',
        ]);
    
        $user = User::where('email', $email)->first();
    
        if ($password == null && $id_token != null) {
            if ($user) {
                return response()->json(['success' => false, 'message' => "{$user->email} already exists"], 200);
            }
    
            $user = User::create([
                'email' => $email,
                'password' => $password,
                'name' => $name,
                'number' => $number,
                'type' => $type,
                'id_token' => $id_token,
            ]);
            return response()->json(['success' => true, 'message' => "Welcome to join, {$user->name}", 'data' => $user], 200);
        }
    
        if ($email == null || $password == null || $name == null || $number == null) {
            return response()->json(['success' => false, 'errors' => 'All fields are required'], 200);
        }
    
        if ($user && $user->email == 'ajay@gmail.com') {
            return response()->json(['success' => true, 'message' => "Welcome to join, {$user->name}", 'data' => $user], 200);
        } else {
            if (!$user) {
                $user = User::create([
                    'email' => $email,
                    'password' => $password,
                    'name' => $name,
                    'number' => $number,
                    'type' => $type,
                    'id_token' => $id_token,
                ]);
    
                return response()->json(['success' => true, 'message' => "Welcome to join, {$user->name}", 'data' => $user], 200);
            } else {
                return response()->json(['success' => false, 'message' => "{$user->email} already exists"], 200);
            }
        }
    }
    



    public function login(Request $request)
    {
        $email = $request->email;
        $password = $request->password;
        $id_token = $request->id_token;
    
        $user = User::where('email', $email)->first();
    
        if ($user != null) {
           
        if ($id_token != null && $id_token == $user->id_token) {
                return response()->json(['success' => true, 'data' => $user], 200);
        } else if($password == $user->password) {
            return response()->json(['success' => true, 'data' => $user], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Password is incorrect'], 401);
        }
    }

        return response()->json(['success' => false, 'message' => 'User not registered'], 404);
    }
    




    public function logout()
    {
        if (Auth::check()) {
            Auth::logout();
            return response()->json(['success' => true, 'message' => 'User logged out'], 200);
        }

        return response()->json(['success' => false, 'message' => 'No user to log out'], 400);
    }




    // This funcation return user data using id

    public function getUser(Request $request)
    {
        $id = $request->id;
        
        $user = User::where('id', $id)->first();
        
        return response()->json(['success' => true, 'data' => $user], 200);
    }



    // This funcation return single user data from user_solutions table useing uid
    public function userSolution(Request $request)
    {
        $id = $request->id;

        if(empty($id)){
            return response()->json(['success' => false, 'message' => 'User not found'], 200);
        }

        $userData = DB::table('user_solutions')->where('uid', $id)->first();
    
        return response()->json(['success' => true, 'data' => $userData], 200);
    }
    


    // get solution by id witch is store in user_solutions table in solution_id
    public function getSolutionBYID(Request $request)
    {
        $id = $request->id;

    
        $filterArray = explode(',', $id);
    
        $solutions = Solutions::all()->filter(function ($solution) use ($filterArray) {
            $solutionFilters = explode(',', $solution->id);
    
            return !empty(array_intersect($solutionFilters, $filterArray));
        });
    
        $formattedSolutions = $solutions->values()->toArray();
    
        return response()->json(['success' => true, 'data' => $formattedSolutions], 200);
    }
    

    // This funcation return all questions witch is ask when user enter first time in app or when user signup in the app
    public function getQuestion()
    {
        $questions = QA::all();
        return response()->json(['success' => true, 'data' => $questions], 200);
    }



    // This funcation store images in db and in ftp server
    public function qImg(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            
            $image->storeAs('img', $imageName, 'public');
    
            $user = QA::create([
                'question' => 'testing question',
                'img' => $imageName,
                'icon' => 'NULL ICON',
                'color_code' => '75E6A4',
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'data' => [
                    'image_name' => $imageName,
                ],
            ], 201);
        }
    
        return response()->json([
            'success' => false,
            'message' => 'Image upload failed',
        ], 400);
    }
    


    

    //  This funcation return all filters
    public function getFilteredData()
    {
        $filters = DB::table('filters')->get();
    
        $allFilter = (object) [
            'id' => 0,
            'filter_name' => 'All',
            'created_at' => "2024-09-23 12:37:24",
            'updated_at' => "2024-09-23 12:37:24"
        ];

        $filters->prepend($allFilter);
    
        return response()->json($filters);
    }
    


// This funcation return all solutions with filter id and if null return all solutions

public function solutions(Request $request)
{
    $id = $request->id;

    if (empty($id)) {
        $solutions = Solutions::all();
        return response()->json(['success' => true, 'data' => $solutions], 200);
    }

    $filterArray = explode(',', $id);

    $solutions = Solutions::all()->filter(function ($solution) use ($filterArray) {
        $solutionFilters = explode(',', $solution->filter);

        return !empty(array_intersect($solutionFilters, $filterArray));
    });

    $formattedSolutions = $solutions->values()->toArray();

    return response()->json(['success' => true, 'data' => $formattedSolutions], 200);
}


// this funcation add solutions id in user_solutions table
public function add_solutions(Request $request)
{
    $uid = $request->uid;
    $sid = $request->sid;

    $userSolution = UserSolutions::where('uid', $uid)->first();

    if (!$userSolution) {
        return response()->json(['success' => false, 'message' => 'User not found'], 200);
    }

    if (empty($userSolution->solution_id)) {
        $userSolution->solution_id = '';
    }

    $existingsid = $userSolution->solution_id ? explode(',', $userSolution->solution_id) : [];

    if (in_array($sid, $existingsid)) {
        return response()->json(['success' => false, 'message' => 'Solution ID already exists'], 200);
    }

    $existingsid[] = $sid;

    $userSolution->solution_id = implode(',', array_unique($existingsid));

    $userSolution->save();

    return response()->json(['success' => true, 'message' => 'Solution added successfully', 'data' => $userSolution], 200);
}



// this funcation remove solutions id in user_solutions table
public function remove_solutions(Request $request)
{
    $uid = $request->uid;
    $sid = $request->sid;

    $userSolution = UserSolutions::where('uid', $uid)->first();

    if (!$userSolution) {
        return response()->json(['success' => false, 'message' => 'User not found'], 200);
    }

    if (empty($userSolution->solution_id)) {
        return response()->json(['success' => false, 'message' => 'No solutions to remove'], 200);
    }

    $existingsid = $userSolution->solution_id ? explode(',', $userSolution->solution_id) : [];

    $updatedsid = array_diff($existingsid, (array)$sid);

    $userSolution->solution_id = implode(',', $updatedsid);

    $userSolution->save();

    return response()->json(['success' => true, 'message' => 'Solution removed successfully', 'data' => $userSolution], 200);
}



// this funcation add filter id in user_solutions table

public function add_filter(Request $request)
{
    $uid = $request->uid;
    $filters = $request->filter;

    $userSolution = UserSolutions::where('uid', $uid)->first();

    if (!$userSolution) {
        return response()->json(['success' => false, 'message' => 'User not found'], 200);
    }

    if (empty($userSolution->filter_id)) {
        $userSolution->filter_id = '';
    }

    $existingFilters = $userSolution->filter_id ? explode(',', $userSolution->filter_id) : [];

    $updatedFilters = array_unique(array_merge($existingFilters, (array)$filters));

    $userSolution->filter_id = implode(',', $updatedFilters);

    $userSolution->save();

    return response()->json(['success' => true, 'message' => 'Filters added successfully', 'data' => $userSolution], 200);
}


// this funcation add question id in user_solutions table
public function add_question(Request $request)
{
    $uid = $request->uid;
    $q = $request->qId;

    $userSolution = UserSolutions::where('uid', $uid)->first();

    if (!$userSolution) {
        // 'uid' => $uid,

        $userSolution = UserSolutions::create([
            'uid' => $uid,
            'select_questions' => $q,
            'solution_status' => '0',
        ]);

        if (empty($userSolution->select_questions)) {
            $userSolution->select_questions = '';
        }

        $existingFilters = $userSolution->select_questions ? explode(',', $userSolution->select_questions) : [];

        $updatedFilters = array_unique(array_merge($existingFilters, (array)$q));

        $userSolution->select_questions = implode(',', $updatedFilters);

        $userSolution->save();

        return response()->json(['success' => true, 'message' => "Question no. {$q} added successfully", 'data' => $userSolution], 200);

    }

    if (empty($userSolution->select_questions)) {
        $userSolution->select_questions = '';
    }

    $existingFilters = $userSolution->select_questions ? explode(',', $userSolution->select_questions) : [];

    $updatedFilters = array_unique(array_merge($existingFilters, (array)$q));

    $userSolution->select_questions = implode(',', $updatedFilters);

    $userSolution->save();

    return response()->json(['success' => true, 'message' => "Question no. {$q} added successfully", 'data' => $userSolution], 200);
}



// This funcation return welcome message when user signup
public function welcomeMessage()
{
    // return response()->json(['success' => true, 'message' => 'A user problem-solving app helps identify, analyze, and resolve user issues efficiently, improving satisfaction and experience.', 'title' => 'WELCOME TO THE APP', 'image' => 'https://twilsms.com/habit_change/storage/app/public/img/yoga.png'], 200);
    return response()->json(['success' => true, 'message' => 'A user problem-solving app helps identify, analyze, and resolve user issues efficiently, improving satisfaction and experience.', 'title' => 'WELCOME TO THE APP'], 200);
}



//*********************************************************** TEST FUNCATION **************************************************************


public function test()
{
    return response()->json(['message' => 'API working'], 200);
}


public function getNextQuestion($id) {
    $question = Question::where('parent_question_id', $id)->first();
    return response()->json($question);
}


public function store(Request $request) {
    $answer = new Answer();
    $answer->user_id = $request->user_id;
    $answer->question_id = $request->question_id;
    $answer->answer_text = $request->answer_text;
    $answer->save();

    return response()->json(['success' => true]);
}


public function getSolution($userId) {
    $answers = Answer::where('user_id', $userId)->get();
    $solutions = [];

    foreach ($answers as $answer) {
        $solution = Solution::where('question_id', $answer->question_id)->first();
        if ($solution) {
            $solutions[] = $solution;
        }
    }

    return response()->json($solutions);
}



}
