<?php

namespace App\Http\Controllers;

use App\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use File;
use Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class PermissionController extends Controller
{
    public function __construct()
    {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
    }
    
    
/**
     * Display the all resources.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * @ApiDescription(section="Permission", description="Get all permissions")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/permission")
     * @ApiParams(name="token", type="string", nullable=false, description="Token")
     * @ApiReturnHeaders(sample="HTTP 200 OK")
     * @ApiReturn(type="object", sample="{\"total\":46,\"per_page\":10,\"current_page\":1,\"last_page\":5,\"next_page_url\":\"http:\/\/www.grow.co.il\/Developer\/laravel-backend\/public\/api\/permission?page=2\",\"prev_page_url\":null,\"from\":1,\"to\":10,\"data\":[{\"id\":1,\"name\":\"view_dashboard\",\"display_name\":\"view_dashboard\",\"description\":null,\"created_at\":\"-0001-11-30 00:00:00\",\"updated_at\":\"-0001-11-30 00:00:00\"},{\"id\":2,\"name\":\"view_admin\",\"display_name\":\"view_admin\",\"description\":null,\"created_at\":\"-0001-11-30 00:00:00\",\"updated_at\":\"-0001-11-30 00:00:00\"},{\"id\":3,\"name\":\"add_request\",\"display_name\":\"add_request\",\"description\":null,\"created_at\":\"-0001-11-30 00:00:00\",\"updated_at\":\"-0001-11-30 00:00:00\"},{\"id\":4,\"name\":\"edit_request\",\"display_name\":\"edit_request\",\"description\":null,\"created_at\":\"-0001-11-30 00:00:00\",\"updated_at\":\"-0001-11-30 00:00:00\"},{\"id\":5,\"name\":\"delete_request\",\"display_name\":\"delete_request\",\"description\":null,\"created_at\":\"-0001-11-30 00:00:00\",\"updated_at\":\"-0001-11-30 00:00:00\"},{\"id\":6,\"name\":\"view_request\",\"display_name\":\"view_request\",\"description\":null,\"created_at\":\"-0001-11-30 00:00:00\",\"updated_at\":\"-0001-11-30 00:00:00\"},{\"id\":7,\"name\":\"add_customer\",\"display_name\":\"add_customer\",\"description\":null,\"created_at\":\"-0001-11-30 00:00:00\",\"updated_at\":\"-0001-11-30 00:00:00\"},{\"id\":8,\"name\":\"edit_customer\",\"display_name\":\"edit_customer\",\"description\":null,\"created_at\":\"-0001-11-30 00:00:00\",\"updated_at\":\"-0001-11-30 00:00:00\"},{\"id\":9,\"name\":\"delete_customer\",\"display_name\":\"delete_customer\",\"description\":null,\"created_at\":\"-0001-11-30 00:00:00\",\"updated_at\":\"-0001-11-30 00:00:00\"},{\"id\":10,\"name\":\"view_customer\",\"display_name\":\"view_customer\",\"description\":null,\"created_at\":\"-0001-11-30 00:00:00\",\"updated_at\":\"-0001-11-30 00:00:00\"}]}")
     */


    public function index()
    {
        $per_page = \Request::get('per_page') ?: 10;

        if(Auth::user()->can('view_permission'))
            return Permission::paginate($per_page);
        else
            return response()->json(['error' =>'You not have permission'], 403);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * @ApiDescription(section="Permission", description="Get a permission")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/permission/{id}")
     * @ApiParams(name="token", type="string", nullable=false, description="Token")
     * @ApiReturnHeaders(sample="HTTP 200 OK")
     * @ApiReturn(type="object", sample="{\"id\":3,\"name\":\"add_request\",\"display_name\":\"add_request\",\"description\":null,\"created_at\":\"-0001-11-30 00:00:00\",\"updated_at\":\"-0001-11-30 00:00:00\"}")
     */
    public function show($id)
    {
        $Permission = Permission::find($id);
        return $Permission;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
/**
     * @ApiDescription(section="Permission", description="Search a permission")
     * @ApiMethod(type="get")
     * @ApiRoute(name="/permission/search")
     * @ApiParams(name="token", type="string", nullable=false, description="Token")
     * @ApiParams(name="query", type="string", nullable=false, description="String to find")
     * @ApiReturnHeaders(sample="HTTP 200 OK")
     * @ApiReturn(type="object", sample="{\"total\":0,\"per_page\":10,\"current_page\":1,\"last_page\":0,\"next_page_url\":null,\"prev_page_url\":null,\"from\":1,\"to\":0,\"data\":[]}")
     */
    public function search(Request $request)
    {
        $per_page = \Request::get('per_page') ?: 10;
        ### search
        if ($request['query']) {
            $Permission = Permission::search($request['query'], null, false)->get();
            $page = $request->has('page') ? $request->page - 1 : 0;
            $total = $Permission->count();
            $Permission = $Permission->slice($page * $per_page, $per_page);
            $Permission = new \Illuminate\Pagination\LengthAwarePaginator($Permission, $total, $per_page);
            return  $Permission;
        }
        return 'not found';
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /**
     * @ApiDescription(section="Permission", description="Create a permission")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/permission/store")
     * @ApiParams(name="token", type="string", nullable=false, description="Token")
     * @ApiParams(name="name", type="string", nullable=false, description="Permission name")
     * @ApiParams(name="display_name", type="string", nullable=false, description="Display Name")
     * @ApiReturnHeaders(sample="HTTP 200 OK")
     * @ApiReturn(type="object", sample="[\"success\"]")
     */
    public function store(Request $request)
    {
        if(Auth::user()->can('add_permission')) {
            if ($request) {

                $validator = Validator::make($request->all(), [
                    'name' => 'required|min:6',
                    'display_name' => 'required|min:6',
                ]);
                if ($validator->fails()) {
                    return response()->json(['error' => $validator->errors()], 406);
                }
                Permission::create($request->all());
                return response()->json(['success'], 200);
            } else {
                return response()->json(['error' => 'can not save product'], 401);
            }
        }else
            return response()->json(['error' =>'You not have permission'], 403);

    }

    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     
    public function edit($id)
    {
        $editpermission = Permission::find($id);
        if($editpermission)
            return response()->json(['success'=>$editpermission], 200);
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
     * @ApiDescription(section="Permission", description="Edit a permission")
     * @ApiMethod(type="put")
     * @ApiRoute(name="/permission/{id}")
     * @ApiParams(name="token", type="string", nullable=false, description="Token")
     * @ApiParams(name="name", type="string", nullable=false, description="Permission name")
     * @ApiParams(name="display_name", type="string", nullable=false, description="Display Name")
     * @ApiReturnHeaders(sample="HTTP 200 OK")
     * @ApiReturn(type="object", sample="")
     */
    public function update(Request $request, $id)
    {
        /**
         * in demo version you can't update default task manager user permission.
         * in production you should remove it
         */
        if($id<45)
            return response()->json(['error' =>'You not have permission to edit this item in demo mode'], 403);
        /** end demo restriction
        */

        if(Auth::user()->can('edit_permission')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:6',
                'display_name' => 'required|min:6',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 406);
            }
        $Permission = Permission::find($id);
            if ($Permission) {
                $Permission->update($request->all());
                return response()->json(['success'], 200);
            } else
                return response()->json(['error' => 'not found item'], 404);
        } else
            return response()->json(['error' =>'You not have permission'], 403);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
        /**
     * @ApiDescription(section="Permission", description="Delete a permission")
     * @ApiMethod(type="delete")
     * @ApiRoute(name="/permission/{id}")
     * @ApiParams(name="token", type="string", nullable=false, description="Token")
     * @ApiReturnHeaders(sample="HTTP 200 OK")
     * @ApiReturn(type="object", sample="")
     */
    public function destroy($id)
    {
        if(Auth::user()->can('delete_permission')) {
            $temp = explode(",", $id);
            foreach($temp as $val){
                /**
                 * in demo version you can't delete default task manager user permission.
                 * in production you should remove it
                 */
                if($id<45)
                    return response()->json(['error' =>'You not have permission to delete this item in demo mode'], 403);
                /** end demo restriction
                 */
                $Permission = Permission::find($val);
                $Permission->delete();
            }
            return response()->json(['success'], 200);
        } else
            return response()->json(['error' =>'You not have permission'], 403);
    }
}
