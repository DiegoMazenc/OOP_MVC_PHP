<?php
namespace App\Controllers;
use App\Controllers\Controller;
use App\Models\User;
use App\Models\UserManager;

class UserController extends Controller{

    public function __construct()
{
    var_dump($_SESSION['user']['roles']); // Ajout de cette ligne pour déboguer

    if (!isset($_SESSION['user']) || !in_array("ROLE_ADMIN", json_decode($_SESSION['user']['roles']))) {
        header('Location:?page=login');
        exit;
    }
}

    public function index(){
        
            $user = new UserManager();
        $users = $user->getAll();
        $this->render('./views/template_user.phtml',[
            'users' => $users
        ]);
        
        
    }

    public function new(){
        // On anticipe d'éventuelles erreurs en créant un tableau
        $errors = [];
        $error = "";
        if (isset($_POST['submit'])){

            $verif = new UserManager();
            if($verif->getUserByEmail($_POST['email'])>0){
                $error = "vous êtes déjà inscrit";
            } else {

            
            // On instancie la class User pour créer un nouvel utilisateur
            $user = new User();
            // Si le formulaire est validé on "hydrate" notre objet
            // avec les informations du formulaire
            $user
            ->setName($_POST['name'])
            ->setEmail($_POST['email'])
            ->setPassword(password_hash($_POST['password'],PASSWORD_DEFAULT))
            ->setRoles("[]");
            // Si la méthode validate ne retourne pas d'erreurs on fait l'insert dans la table
            

            $errors = $user->validate();
            if (empty($errors)){
                // On transforme l'objet User courant en tableau
                // Avec uniquement les valeurs des propriétés
                // Voir la methode toArray() dans User.php
                $userArray = $user->toArray();
                // On instancie un UserManager
                $userManager = new UserManager();
                // On effectue l'insert dans la table
                $userManager->insert( $userArray );
                // On est très content !
                // ON redirige !
                header('Location:?page=user');

            }
        }}
        
        $this->render('./views/template_user_new.phtml',[
            'errors' => $errors,
            'error' => $error
        ]);
    }

    public function delete(){
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $user = new UserManager();
            if ($user->delete($id)) {
                header("Location:?page=user");
            }
        }
    }

    public function edit()
    {
        // on récupère l'id depuis l'url
        // on la convertit en entier pour être plus prudent...
        $id = intval($_GET['id']);
        $userManager = new UserManager();
        // On fait une petite requète pour récupérer la picture à modifier
        $user = $userManager->getOneById($id);
        // Si le formulaire est validé on update dans la table
        // Sans oublier de préciser l'id
        if (isset($_POST['edit'])) {
            $newUser = new User();
            $newUser->setName($_POST['name'])
            ->setEmail($_POST['email'])
            ->setRoles($_POST['roles']);

            $UserArray = $newUser->toArrayUpdate();
            $UserArray[] = $id;
            
            $userManager = new UserManager();
            $userManager->updateUser($UserArray);
            // Et on redirige sur l'adminlist
            header("Location:?page=user");
        }
        $this->render('./views/template_user_edit.phtml', [
            "user" => $user
        ]);
    }
}