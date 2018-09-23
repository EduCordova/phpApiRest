<?php

namespace App\Http\Controllers\User;

//IMPORTAR EL MODEL USER EL DE ARRIBA NO ES -__-
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Listar todos los recursos Disponibles
        $usuarios = User::all();
        return response()->json(['data' => $usuarios], 200);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Creando un usuario
        $campos = $request->all();
        //Reglas de validacion
        $reglas = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ];
        $this->validate($request,$reglas);
        //Encriptando la contraseña
        $campos['password'] = bcrypt($request->password);
        $campos['verified'] = User::USUARIO_NO_VERIFICADO;
        $campos['verification_token'] = User::generarVerificationToken();
        $campos['admin'] = User::USUARIO_REGULAR;

        $usuario = User::create($campos);
        // 201 creado!!
        return response()->json(['data' => $usuario], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $usuario = User::find($id);
        $usuario = User::findOrFail($id);
        
        return response()->json(['data'=>$usuario], 200);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id); //Buscamos al usuario
        $reglas = [
            'email' => 'email|unique:users,email,'.$user->id,
            'password' => 'min:6|confirmed',
            'admin' => 'in:'.User::USUARIO_ADMINISTRADOR.','.User::USUARIO_REGULAR,
        ];
        $this->validate($request,$reglas);
        if ($request->has('name'))
        {
            $user->name = $request->name;
        }
        //Email se actualiza solo si es diferente dah!
        if ($request->has('email') && $user->email != $request->email)
        {
            $user->verified = User::USUARIO_NO_VERIFICADO;
            $user->verification_token = User::generarVerificationToken();
            $user->email = $request->email;
        }

        if ($request->has('password'))
        {
            $user->password = bcrypt($request->password);
        }

        if ($request->has('admin'))
        {
            if(!$user->esVerificado())
            {
                return response()->json(
                    [
                        'error'=> 'Unicamente los usuarios verificados pueden cambiar su valor  de administraor',
                        'code' => 409
                    ], 409);
            }
            $user ->admin = $request->admin;
        }

        if (!$user->isDirty())
        {
            return response()->json(
                [
                    'error'=> 'Se debe especificar un valor diferente almenos para actualizar',
                     'code' => 409
                ], 409);
        }

        $user->save();
        return response()->json(['data' => $user], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
