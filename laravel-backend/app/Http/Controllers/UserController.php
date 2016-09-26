<?php

namespace App\Http\Controllers;

use App\Role;
use App\role_user;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use File;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function __construct()
    {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
    }


    /*
     * Export Excel Method
     */
    public function exportFile(Request $request){
        ### $request['export_type'] is export mode  "EXCEL or CSV"
        ### Check export CSV permission
        if($request['export_type']=='csv'&& !Auth::user()->can('export_csv') )
            return 'You not have this permission';

        ### Check export EXCEL permission
        if($request['export_type']=='xls'&& !Auth::user()->can('export_xls') )
            return 'You not have this permission';



        ### record_type 1 equal whole records and 2 equals selected records
        if ($request['record_type']==1) {
            $users = User::all();
        } else if ($request['record_type']==2){
//            return $request['selection'];
//            $temp = explode(",", $request['selection']);
    //        foreach($temp as $val) {
  //             $users = User::find($val);
//            }
            $users = User::findMany($request['selection']);
        }

        ###
        if($request['export_type']=='pdf'){ //export PDF
            $html='<h1 style="text-align: center">YEP ngLaravel PDF </h1>';
            $html .= '<style> table, th, td {text-align: center;} th, td {padding: 5px;} th {color: #43A047;border-color: black;background-color: #C5E1A5} </style> <table border="2" style="width:100%;"> <tr> <th>Name</th> <th>Email</th> </tr>';
            foreach ($users as $user ){
                $html .="<tr> <td>$user->name</td> <td>$user->email</td> </tr>";
            }
            $html .= '</table>';
            $pdf = App::make('dompdf.wrapper');
            $headers = array(
                'Content-Type: application/pdf',
            );
            $pdf->loadHTML($html);
            return $pdf->download('permission.pdf',$headers);
        }else {
            Excel::create('user', function ($excel) use ($users) {
                $excel->sheet('Sheet 1', function ($sheet) use ($users) {
                    $sheet->fromArray($users);
                });
            })->download($request['export_type']);
        }
    }






    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    /**
     * @ApiDescription(section="User", description="Get information about user")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/user/")
     * @ApiParams(name="token", type="string", nullable=false, description="Token")
     * @ApiReturnHeaders(sample="HTTP 200 OK")
     * @ApiReturn(type="object", sample="{\"total\":4,\"per_page\":10,\"current_page\":1,\"last_page\":1,\"next_page_url\":null,\"prev_page_url\":null,\"from\":1,\"to\":4,\"data\":[{\"id\":1,\"name\":\"kcs\",\"email\":\"example1@domain.com\",\"avatar_url\":\"\",\"created_at\":\"2016-09-06 06:35:07\",\"updated_at\":\"-0001-11-30 00:00:00\",\"username\":null,\"location\":null,\"country\":null,\"biography\":null,\"occupation\":null,\"website\":null,\"image\":null,\"status\":0,\"birthday\":null,\"gender\":null,\"deleted_at\":null},{\"id\":2,\"name\":\"testt\",\"email\":\"example2@domain.com\",\"avatar_url\":\"\",\"created_at\":\"2016-09-22 08:27:18\",\"updated_at\":\"2016-09-22 08:27:18\",\"username\":null,\"location\":\"g34\",\"country\":\"gfg\",\"biography\":null,\"occupation\":null,\"website\":null,\"image\":null,\"status\":0,\"birthday\":null,\"gender\":0,\"deleted_at\":null},{\"id\":3,\"name\":\"tomer2\",\"email\":\"example3@domain.com\",\"avatar_url\":\"\",\"created_at\":\"2016-09-22 09:26:51\",\"updated_at\":\"2016-09-22 09:26:51\",\"username\":null,\"location\":null,\"country\":null,\"biography\":null,\"occupation\":null,\"website\":null,\"image\":null,\"status\":0,\"birthday\":null,\"gender\":null,\"deleted_at\":null},{\"id\":4,\"name\":\"tomer2\",\"email\":\"example4@domain.com\",\"avatar_url\":\"\",\"created_at\":\"2016-09-22 09:30:07\",\"updated_at\":\"2016-09-22 09:30:07\",\"username\":null,\"location\":null,\"country\":null,\"biography\":null,\"occupation\":null,\"website\":null,\"image\":null,\"status\":0,\"birthday\":null,\"gender\":null,\"deleted_at\":null}]}")
     */
    
    public function index()
    {
        $per_page = \Request::get('per_page') ?: 10;

        if(Auth::user()->can('view_user')){
            return User::paginate($per_page);
        }
        else
            return response()->json(['error' =>'You not have User'], 403);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    /**
     * @ApiDescription(section="User", description="Get information about user")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/user/{id}")
     * @ApiParams(name="token", type="string", nullable=false, description="Token")
     * @ApiReturnHeaders(sample="HTTP 200 OK")
     * @ApiReturn(type="object", sample="{\"id\":2,\"name\":\"john\",\"email\":\"example@grow.com\",\"avatar_url\":\"\",\"created_at\":\"2016-09-22 08:27:18\",\"updated_at\":\"2016-09-22 08:27:18\",\"username\":null,\"location\":\"g34\",\"country\":\"gfg\",\"biography\":null,\"occupation\":null,\"website\":null,\"image\":null,\"status\":0,\"birthday\":null,\"gender\":0,\"deleted_at\":null,\"role_id\":1}")
     */
    public function show($id)
    {
        $User = User::find($id);
        $role_id=DB::table('role_user')->select('role_id')->where('user_id',$id)->lists('role_id');
        $User->role_id=$role_id[0];
        if($User)
            return $User;
        else
            return response()->json(['error' => 'not found item'], 404);
    }
    
    

    /*
     *  Search Method
     */
    
     /**
     * @ApiDescription(section="User", description="Search a user")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/user/search")
     * @ApiParams(name="token", type="string", nullable=false, description="Token")
     * @ApiParams(name="query", type="string", nullable=false, description="String to find")
    * @ApiReturn(type="object", sample="{\"total\":1,\"per_page\":10,\"current_page\":1,\"last_page\":1,\"next_page_url\":null,\"prev_page_url\":null,\"from\":1,\"to\":1,\"data\":[{\"id\":2,\"name\":\"testuser\",\"email\":\"example@domain.com\",\"avatar_url\":\"\",\"created_at\":\"2016-09-22 08:27:18\",\"updated_at\":\"2016-09-22 08:27:18\",\"username\":null,\"location\":\"g34\",\"country\":\"gfg\",\"biography\":null,\"occupation\":null,\"website\":null,\"image\":null,\"status\":0,\"birthday\":null,\"gender\":0,\"deleted_at\":null,\"relevance\":1206}]}")
    */
    public function search(Request $request)
    {
        $per_page = \Request::get('per_page') ?: 10;
        ### search
        if ($request['query']) {
            $User = User::search($request['query'], null, false)->get();
            $page = $request->has('page') ? $request->page - 1 : 0;
            $total = $User->count();
            $User = $User->slice($page * $per_page, $per_page);
            $User = new \Illuminate\Pagination\LengthAwarePaginator($User, $total, $per_page);
            return  $User;
        }
        return 'not found';
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    /**
     * @ApiDescription(section="User", description="Create's a new user")
     * @ApiMethod(type="post", route="/user/create")
     * @ApiRoute(name="/user/create")
     * @ApiParams(name="token", type="string", nullable=false, description="Token")
     * @ApiParams(name="title", type="string", nullable=false, description="Title")
     * @ApiParams(name="user_id", type="integer", nullable=false, description="User ID")
     * @ApiParams(name="category_id", type="integer", nullable=false, description="Category ID")
     * @ApiReturnHeaders(sample="HTTP 200 OK")
     * @ApiReturn(type="object", sample="{'success': {'title': 'some-task','updated_at': '2016-09-25 07:49:03','created_at':'2016-09-25 07:49:03','id': 1,'user_id': 1,'category_id': 1}}")
     */
    public function store(Request $request)
    {

       if(Auth::user()->can('add_user')) {
            if ($request) {
             $validator = Validator::make($request->all(), [
                    'name' => 'required|min:5',
                    'email' => 'unique:users,email|required|email',
                    'password' => 'required|min:6',
                    'role_id' => 'required',

                ]);
                if ($validator->fails()) {
                    return response()->json(['error' => $validator->errors()], 406);
                }

                ###  upload avatar
                if (file_exists("temp/" . $request['avatar_url']) && $request['avatar_url'] != ''){
                    File::move("temp/".$request['avatar_url'], "uploads/". $request['avatar_url']);
                }
                ####

                $request['password'] = bcrypt($request['password']);
                $user=User::create($request->all());
                DB::insert('insert into role_user (user_id, role_id) values (?, ?)', [$user->id, $request['role_id']]);
                return response()->json(['success'], 200);
            } else {
                return response()->json(['error' => 'can not save product'], 401);
            }
      }else
            return response()->json(['error' =>'You not have User'], 403);

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $editUser = User::find($id);




        if($editUser)
            return response()->json(['success'=>$editUser], 200);
        else
            return response()->json(['error' => 'not found item'], 404);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     /**
     * @ApiDescription(section="User", description="Edit a user")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/user/update")
     * @ApiParams(name="id", type="integer", nullable=false, description="User id")
     */
    public function update(Request $request, $id)
    {
        /**
         * in demo version you can't delete default task manager user permission.
         * in production you should remove it
         */
        if($id==1)
            return response()->json(['error' =>'You not have permission to edit this item in demo mode'], 403);


        if(Auth::user()->can('edit_user')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:5',
                'email' => 'required|email',
                'password' => 'required|min:6',
                'role_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 406);
            }
            ###  upload avatar
            if (file_exists("temp/" . $request['avatar_url']) && $request['avatar_url'] != ''){
                File::move("temp/".$request['avatar_url'], "uploads/". $request['avatar_url']);
            }
            ####
            $User = User::find($id);
            DB::table('role_user')->where('user_id',$User->id)->delete();
            DB::insert('insert into role_user (user_id, role_id) values (?, ?)', [$User->id, $request['role_id']]);
            if ($request['password'] != '********')
                $request['password'] = bcrypt($request['password']);
            else
                $request['password'] = $User->password;
            if ($User) {
                $User->update($request->all());
                return response()->json(['success'], 200);
            } else
                return response()->json(['error' => 'not found item'], 404);
        } else
            return response()->json(['error' =>'You not have User'], 403);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     /**
     * @ApiDescription(section="User", description="Delete a user")
     * @ApiMethod(type="delete")
     * @ApiRoute(name="/user/{id}")
     * @ApiParams(name="token", type="string", nullable=false, description="Token")
     * @ApiReturnHeaders(sample="HTTP 200 OK")
     */
    public function destroy($id)
    {
        if(Auth::user()->can('delete_user')) {
            $temp = explode(",", $id);
            foreach($temp as $val){
                /**
                 * in demo version you can't delete default task manager user permission.
                 * in production you should remove it
                 */
                if($val==1)
                    return response()->json(['error' =>'You not have permission to delete this item in demo mode'], 403);

                $User = User::find($val);
                $User->delete();
            }
            return response()->json(['success'], 200);
        } else
            return response()->json(['error' =>'You not have User'], 403);
    }
}
