<?php

namespace App\Http\Controllers;

use App\User;
use App\Transformer\UserTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends HandleController
{

    /**
     * @var UserTransformer
     */
    protected $userTransformer;

    /**
     * UserController constructor.
     * @param $userTransformer
     */
    public function __construct(UserTransformer $userTransformer)
    {
        $this->userTransformer = $userTransformer;
    }

    /**
     * @return JsonResponse
     */
    public function index (Request $request)
    {
        //check to see if there are any query
        if($request->query('name') || $request->query('email'))
        {

            //if query is name
            if ($request->query('name'))
            {
                $value = $request->query('name');
                $user = User::select('id','name','email')
                    ->where('name','like', $value.'%')
                    ->paginate(5);
            }

            //if query is email
            else if ($request->query('email'))
            {
                $value = $request->query('email');
                $user = User::select('id','name','email')
                    ->where('email', $value)
                    ->paginate(5);
            }

            //if not any of the queries stated
            else
            {
                return $this->responseNotFound();
            }

            //return the json user response
            return Response::json([
                'data' => $user
            ],200);
        }


        //Return all results
        return Response::json([
            'data' => User::paginate(5)
        ]);
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function show (User $user)
    {
        //if the user is not found
        if (!$user)
        {
            return $this->responseNotFound();
        }


        //return json response of user detail
        return Response::json([
            'data' => $this->userTransformer->transform($user)
        ]);
    }

    /**
     * @param Request $request
     * @throws \Illuminate\Validation\ValidationException
     */
    public function create (Request $request)
    {
        //validate input
        if($this->validateUserInput($request)) {

            //create new user and insert into database
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->save();
            $token = $user->createToken('ApiToken')->accessToken;


            //return success json response
            return Response::json([
                'message' => 'User successfully created',
                'token' => $token
            ]);
        }

        return $this->responseNotServed();

    }

    /**
     * @param Request $request
     * @param User $user
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update (Request $request, User $user)
    {
        //validate input

        if($this->validateUserInput($request,'update')) {

            $inputs = $request->all();
            foreach($inputs as $key => $input)
            {
                if ($key == "password")
                {
                    $user->password = Hash::make($request->password);
                }

                $user->$key = $input;
                $user->save();
            }

            //return success json response
            return Response::json([
                'message' => 'successfully updated'
            ]);
        }

        return $this->responseNotServed();

    }

    /**
     * @param User $user
     * @throws \Exception
     */
    public function delete (User $user)
    {
        //check if user has been deleted
        if(!$user->delete())
        {
            //return failed json response
            return $this->responseNotFound();
        }


        //return successfully deleted json response
        return Response::json([
            'message' => 'user successfully deleted'
        ]);
    }

}
