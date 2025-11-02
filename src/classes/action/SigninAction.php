<?php
namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;

class SigninAction extends Action
{
    public function __invoke(): string
    {
        if ($this->http_method === 'GET') {
            return "<form method='POST' action='http://" . $this->hostname . $this->script_name . "?action=signin'>
                <label for='username'>Nom d'utilisateur :</label>
                <input type='text' id='username' name='username' required>
                <label for='email'>Email :</label>
                <input type='email' id='email' name='email' required>
                <label for='password'>Mot de passe :</label>
                <input type='password' id='password' name='password' required>
                <input type='submit' value='Connexion'>
            </form>";
        } elseif ($this->http_method === 'POST') {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
                try {
                    AuthnProvider::signin($email, $password);
                    $_SESSION['user'] = $email;
                    return "<div>Authentification réussie. Bienvenue, $email !</div>";
                } catch (AuthnException $e) {
                    return "<div>Erreur d'authentification : " . $e->getMessage() . "</div>";
                }
            } else {
                return "<div>Adresse email invalide.</div>";
            }
        } else {
            return "<div>Méthode HTTP non supportée.</div>";
        }
    }
}
