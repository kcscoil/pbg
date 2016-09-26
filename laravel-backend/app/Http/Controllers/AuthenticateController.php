<?php

namespace App\Http\Controllers;

use App\role_user;
use Illuminate\Http\Request;
use App\Role;
use App\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticateController extends Controller
{
     /**
     * @ApiDescription(section="Authenticate", description="Get a Token")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/authenticate")
     * @ApiParams(name="email", type="string", nullable=false, description="Email")
     * @ApiParams(name="password", type="string", nullable=false, description="Password")
     * @ApiReturnHeaders(sample="HTTP 200 OK")
     * @ApiReturn(type="object", sample="{\"token\":\"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6XC9cL3d3dy5ncm93LmNvLmlsXC9EZXZlbG9wZXJcL2xhcmF2ZWwtYmFja2VuZFwvcHVibGljXC9hcGlcL2F1dGhlbnRpY2F0ZSIsImlhdCI6MTQ3NDg4ODY5NywiZXhwIjoxNDc0ODkyMjk3LCJuYmYiOjE0NzQ4ODg2OTcsImp0aSI6IjcxMjAwZmU4MDhkYzYyMjNlNzg2YTQ2OGFjZTA2ZGRmIn0.R4l7VzQClOZmAayHZbYjvgYZrRq5WXO36A_LJct5xH4\",\"user\":{\"id\":1,\"name\":\"kcs\",\"email\":\"admin@example.com\",\"avatar_url\":\"\",\"created_at\":\"2016-09-06 06:35:07\",\"updated_at\":\"-0001-11-30 00:00:00\",\"username\":null,\"location\":null,\"country\":null,\"biography\":null,\"occupation\":null,\"website\":null,\"image\":null,\"status\":0,\"birthday\":null,\"gender\":null,\"deleted_at\":null,\"permissions\":[\"view_dashboard\",\"view_admin\",\"add_request\",\"edit_request\",\"delete_request\",\"view_request\",\"add_customer\",\"edit_customer\",\"delete_customer\",\"view_customer\",\"add_category\",\"edit_category\",\"delete_category\",\"view_category\",\"view_user\",\"add_user\",\"edit_user\",\"delete_user\",\"view_role\",\"add_role\",\"edit_role\",\"delete_role\",\"view_permission\",\"add_permission\",\"edit_permission\",\"delete_permission\",\"export_csv\",\"export_xls\",\"view_news_category\",\"add_news_category\",\"edit_news_category\",\"delete_news_category\",\"add_gallery\",\"edit_gallery\",\"view_gallery\",\"delete_gallery\",\"view_task\",\"add_task\",\"delete_task\",\"edit_task\",\"view_comment\",\"add_comment\",\"edit_comment\",\"delete_comment\",\"export_pdf\",\"import_user\"]}}")
     */
    public function authenticate(Request $request)
    {
	    $permissions=array();
        $credentials = $request->only('email', 'password');

        try {
            // verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid Credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        $role = DB::table('role_user')->where('user_id',Auth::user()->id)->first();
        $role = Role::find($role->role_id);
        $user=Auth::user();
        $temp=$role->perms()->get()->lists('name');
        foreach($temp as $value){
            $permissions[]=$value;
         }
        $user->permissions=$permissions;
        return response()->json(compact('token','user'));
 
    }

}
