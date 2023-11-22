<?php
namespace App\Controllers;
use App\Controllers\Controller;
use App\Services\Authenticator;

class LoginController extends Controller{

    public function index(){
       $error="";
       $style="";
        if(isset($_POST['login'])){
            $email = $_POST['email'];
            $password = $_POST['password'];
            $user = new Authenticator();
            if($user->login($email,$password)){
                header('Location:?page=home');
            }
            $error= "Utilisateur non trouvé";
            $style="color: red; padding:15px; border:1px solid red;";
        }
        
        
 
        $this->render('./views/template_login.phtml',[
            'error'=> $error,
            'style'=> $style
        ]);
     
    }

}