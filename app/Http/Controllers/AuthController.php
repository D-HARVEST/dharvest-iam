namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
public function login(Request $request)
{
$fields = $request->validate([
'email' => 'required|string|email',
'password' => 'required|string'
]);

// Vérifier l'email
$user = User::where('email', $fields['email'])->first();

// Vérifier le mot de passe
if (!$user || !Hash::check($fields['password'], $user->password)) {
return response([
'message' => 'Identifiants incorrects'
], 401);
}

// Générer le Token Passport
// On donne un nom au token, par exemple "D-Harvest-Token"
$token = $user->createToken('djallonke_auth_token')->accessToken;

return response([
'user' => $user,
'access_token' => $token,
'token_type' => 'Bearer',
], 200);
}
}