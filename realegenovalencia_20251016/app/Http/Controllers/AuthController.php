<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function crearUsuario(Request $request)
    {
        $satisfactorio = false;
        $estado = 0;
        $mensaje = "";
        $errores = [];
        $valores = [];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users',
            'password' => 'required|string|min:8|max:20'
        ], [
            'name.required' => 'El nombre obligatorio',
            'name.max' => 'El nombre no puede exceder el limite de 100 caracteres',
            'email.required' => 'El email es un campo obligatorio',
            'email.email' => 'El formato del email no es válido',
            'email.max' => 'El email no puede exceder el limite de 100 caracteres',
            'email.unique' => 'El email ya se encuentra registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.max' => 'La contraseña no puede exceder el limite de 20 caracteres'
        ]);

        if ($validator->fails()) {
            $satisfactorio = false;
            $estado = 400;
            $mensaje = "Error en validación de datos";
            $errores = [
                "code" => 400,
                "msg" => $validator->errors()->first()
            ];
            
            $respuesta = [
                "success" => $satisfactorio,
                "status" => $estado,
                "msg" => $mensaje,
                "data" => $valores,
                "errors" => $errores,
                "total" => sizeof($valores)
            ];
            
            return response()->json($respuesta, $estado);
        }

        try {
            //crear usuario
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            //crear token
            $token = $user->createToken('auth_token')->plainTextToken;

            $satisfactorio = true;
            $estado = 201;
            $mensaje = "Usuario creado exitosamente";
            $errores = [
                "code" => 201,
                "msg" => ""
            ];
            $valores = [
                "access_token" => $token,
                "token_type" => "bearer"
            ];

        } catch (\Exception $e) {
            $satisfactorio = false;
            $estado = 500;
            $mensaje = "Error al crear el usuario";
            $errores = [
                "code" => 500,
                "msg" => $e->getMessage()
            ];
        }

        $respuesta = [
            "success" => $satisfactorio,
            "status" => $estado,
            "msg" => $mensaje,
            "data" => $valores,
            "errors" => $errores,
            "total" => sizeof($valores)
        ];

        return response()->json($respuesta, $estado);
    }

    public function login(Request $request)
    {
        $satisfactorio = false;
        $estado = 0;
        $mensaje = "";
        $errores = [];
        $valores = [];

        //validar entrada de datos
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            $satisfactorio = false;
            $estado = 400;
            $mensaje = "Error en la validación de datos";
            $errores = [
                "code" => 400,
                "msg" => $validator->errors()->first()
            ];
            
            $respuesta = [
                "success" => $satisfactorio,
                "status" => $estado,
                "msg" => $mensaje,
                "data" => $valores,
                "errors" => $errores,
                "total" => sizeof($valores)
            ];
            
            return response()->json($respuesta, $estado);
        }

        //verificacion de credenciales
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = User::where('email', $request->email)->first();
            $token = $user->createToken('auth_token')->plainTextToken;

            $satisfactorio = true;
            $estado = 200;
            $mensaje = "Login exitoso";
            $errores = [
                "code" => 200,
                "msg" => ""
            ];
            $valores = [
                "access_token" => $token,
                "token_type" => "Bearer"
            ];
        } else {
            $satisfactorio = false;
            $estado = 401;
            $mensaje = "Credenciales incorrectas";
            $errores = [
                "code" => 401,
                "msg" => "No se reconocen las credenciales"
            ];
        }

        $respuesta = [
            "success" => $satisfactorio,
            "status" => $estado,
            "msg" => $mensaje,
            "data" => $valores,
            "errors" => $errores,
            "total" => sizeof($valores)
        ];

        return response()->json($respuesta, $estado);
    }

    public function obtenerUsuario(Request $request)
    {
        $satisfactorio = false;
        $estado = 0;
        $mensaje = "";
        $errores = [];
        $valores = [];

        //verificar usuario
        if ($request->user()) {
            $user = $request->user();
            
            $satisfactorio = true;
            $estado = 200;
            $mensaje = "Datos de usuario obtenidos de forma exitosa";
            $errores = [
                "code" => 200,
                "msg" => ""
            ];
            $valores = [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "email_verified_at" => $user->email_verified_at,
                "created_at" => $user->created_at,
                "updated_at" => $user->updated_at
            ];
        } else {
            $satisfactorio = false;
            $estado = 401;
            $mensaje = "No autorizado";
            $errores = [
                "code" => 401,
                "msg" => "Token no válido o han expirado"
            ];
        }

        $respuesta = [
            "success" => $satisfactorio,
            "status" => $estado,
            "msg" => $mensaje,
            "data" => $valores,
            "errors" => $errores,
            "total" => sizeof($valores)
        ];

        return response()->json($respuesta, $estado);
    }
}