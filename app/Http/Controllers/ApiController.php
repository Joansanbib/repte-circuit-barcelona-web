<?php

namespace App\Http\Controllers;

use App\Models\Incidencia;
use App\Models\UsuariResol;
use App\Models\Usuari;
use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash; 


class ApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function getAllIncidents()
    {
        $incidents = Incidencia::all();
        foreach ($incidents as $incident) {
            $incident->Data = $incident->Data->toIso8601String();
        }
        return response()->json($incidents);
    }
    public function getAllZones()
    {
        $zones = Zona::all();
        return response()->json($zones);
    }
    public function createIncident(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'Nom' => 'required|max:150',
            'Descripcio' => 'required|max:500',
            'Data' => 'required|date',
            'Estat' => 'required',
            'Prioritat' => 'required',
            'Zona' => 'required',
            'Rol_assignat' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $incidencia = new Incidencia();
        $incidencia->Nom = $request->input('Nom');
        $incidencia->Descripcio = $request->input('Descripcio');
        $incidencia->Data = $request->input('Data');
        $incidencia->Estat = $request->input('Estat');
        $incidencia->Prioritat = $request->input('Prioritat');
        $incidencia->Zona = $request->input('Zona');
        $incidencia->Ruta_img = $request->input('Ruta_img');
        $incidencia->Rol_assignat = $request->input('Rol_assignat');

        $incidencia->save();
        return response()->json($incidencia, 201);
    }

    public function login(Request $request){
        $email = $request->input('email');
        $password = $request->input('password');
        $user = Usuari::where('email', $email)->first();
        if ($user && Hash::check($password, $user->password)) {
            return response()->json(['user' => $user], 200);
        }
        else {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }
    }
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:usuaris',
            'password' => 'required|min:8',
            'name' => 'required',
            'NIF' => 'required|regex:/^[0-9]{8}[A-Za-z]$/|unique:usuaris',
        ]);
        if ($validator->fails()) {
            return response()->noContent(400);
        }
        $user = new Usuari();
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->name = $request->input('name');
        $user->NIF = $request->input('NIF');
        $user->Rol = "Operari";
        $user->save();
        return response()->noContent(201);
    }











    public function getUserIncidents()
    {
        $incidents = Incidencia::where('user_id', Auth::id())->get();
        return response()->json($incidents);
    }

    
//
//pr

    public function updateIncident(Request $request, $id, $user)
    {
        $incident = Incidencia::find($id);
        $this->authorize('isOwnerOrAdmin', $incident);

        $incident->update($request->all());
        return response()->json($incident);
    }

    public function deleteIncident($id)
    {
        $this->authorize('isAdmin', Usuari::class);
        Incidencia::destroy($id);
        return response()->json(null, 204);
    }

    public function searchIncidents(Request $request)
    {
        $query = Incidencia::query();

        if ($request->filled('description')) {
            $query->where('Descripcio', 'like', '%' . $request->description . '%');
        }

        if ($request->filled('zone')) {
            $query->where('Zona', $request->zone);
        }

        if ($request->filled('state')) {
            $query->where('Estat', $request->state);
        }

        if ($request->filled('priority')) {
            $query->where('Prioritat', $request->priority);
        }

        return response()->json($query->get());
    }

    // UsuariResols Endpoints
    public function indexUsuariResol()
    {
        $resolutions = UsuariResol::where('Usuari', Auth::id())->get();
        return response()->json($resolutions);
    }

    public function storeUsuariResol(Request $request)
    {
        $request->validate([
            'Incidencia' => 'required|exists:incidencias,id',
            'Comentaris' => 'required|string',
            'Estat' => 'required|in:Solucionada,En manteniment',
            'Inici' => 'required|date',
            'Final' => 'required|date|after_or_equal:Inici',
        ]);

        $resolution = new UsuariResol($request->all());
        $resolution->Usuari = Auth::id();
        $resolution->save();

        return response()->json($resolution, 201);
    }

    public function showUsuariResol($id)
    {
        $resolution = UsuariResol::where('id', $id)->firstOrFail();
        return response()->json($resolution);
    }

    public function updateUsuariResol(Request $request, $id)
    {
        $resolution = UsuariResol::find($id);

        $request->validate([
            'Comentaris' => 'sometimes|string',
            'Estat' => 'sometimes|in:Solucionada,En manteniment',
            'Inici' => 'sometimes|date',
            'Final' => 'sometimes|date|after_or_equal:Inici',
        ]);

        $resolution->update($request->all());
        return response()->json($resolution);
    }

    public function destroyUsuariResol($id)
    {
        UsuariResol::destroy($id);
        return response()->json(null, 204);
    }

    public function searchUsuariResol(Request $request)
    {
        $query = UsuariResol::query();

        if ($request->filled('state')) {
            $query->where('Estat', $request->state);
        }

        if ($request->filled('incidencia_id')) {
            $query->where('Incidencia', $request->incidencia_id);
        }

        if ($request->filled('comments')) {
            $query->where('Comentaris', 'like', '%' . $request->comments . '%');
        }

        if ($request->filled('start_time')) {
            $query->where('Inici', $request->start_time);
        }

        if ($request->filled('end_time')) {
            $query->where('Final', $request->end_time);
        }

        return response()->json($query->get());
    }

    // Usuaris Endpoints
    public function searchUsers(Request $request)
    {
        $query = Usuari::query();

        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        if ($request->filled('surname')) {
            $query->whereRaw("CONCAT(first_name, ' ', second_name) LIKE ?", ["%{$request->surname}%"]);
        }

        if ($request->filled('nif')) {
            $query->where('NIF', $request->nif);
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('role')) {
            $query->where('Rol', $request->role);
        }

        return response()->json($query->get());
    }

    // Zones Endpoints
    public function searchZones(Request $request)
    {
        $query = Zona::query();

        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        if ($request->filled('description')) {
            $query->where('Descripcio', 'like', '%' . $request->description . '%');
        }

        if ($request->filled('incidencia_id')) {
            $query->whereHas('incidencias', function ($q) use ($request) {
                $q->where('id', $request->incidencia_id);
            });
        }

        if ($request->filled('name')) {
            $query->where('Nom', 'like', '%' . $request->name . '%');
        }

        return response()->json($query->get());
    }
}
