<?php
// Redirige l'utilisateur s'il est déjà identifié
session_start();
if(isset($_COOKIE["ID_UTILISATEUR"])){
      header("Location: ../menu.php");
}else{
	include_once("../header.inc.php");
	include_once("../include/functions.php");	
	include_once("../include/textes.php");
	require_once('../include/phpmailer/class.phpmailer.php');
     // Formulaire visible par défaut
     $masquer_formulaire = false;
     
     // Une fois le formulaire envoyé
     if(isset($_POST["valid"])){
          
          // Vérification de la validité des champs
          if (!filter_var($_POST["TB_Adresse_Email"], FILTER_VALIDATE_EMAIL)){
               echo "Votre adresse e-mail n'est pas valide";              
          }else{               
               // Connexion à la base de données              
          	   connectSQL();  
          	   include_once("../include/protect_var.php");
          	   $reqTest="SELECT id,actif FROM user WHERE mail1='$_POST[TB_Adresse_Email]'";
          	   $recTest=mysql_query($reqTest);
          	   if (mysql_num_rows($recTest)>0){
          	   		$resTest=mysql_fetch_array($recTest);
          	   		if ($resTest['actif']==1){
                         // Génération de la clef
                        
                         $caracteres = array("a", "b", "c", "d", "e", "f", 0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
                         $caracteres_aleatoires = array_rand($caracteres, 8);
                         $clef_activation = "";
                         
                         foreach($caracteres_aleatoires as $i){
                              $clef_activation .= $caracteres[$i];
                         }     
                         //Generation du mot de passe                         
                         $password=genererMDP();                   
                         // Création du compte utilisateur
                         $req="
                              UPDATE user SET
                              ask_change_password='1',
                              password_change='".md5($password)."',
                              clef_activation_password='$clef_activation' WHERE id='$resTest[id]'                              
                         ";                       
                         $result = mysql_query($req);
                         
                         // Si une erreur survient
                         if(!$result){
                              echo "Erreur d'accès à la base de données ";                                                         
                         }else{                              
                              // Envoi du mail d'activation
                              $sujet = "Réinitialisation de votre mot de passe";
                              $message ="Vous avez demandé la réinitisalisation de votre mot de passe\n".
                              $message ="Votre nouveau mot de passe est : $password\n\n";
                              $message .= "Pour terminer la réinitialisation de votre mot de passe, merci de cliquer sur le lien suivant :\n";
                              $message .= $GLOBALS['params']['appli']['proto']."://" . $_SERVER["SERVER_NAME"];
                              $message .= $GLOBALS['params']['appli']['root_folder']."/resources/activate_password.php?id=" .$resTest[id];
                              $message .= "&activation=" . $clef_activation;
                              $mail = new PHPMailer();
                              $mail->IsSMTP();
                              #$mail->Host = $GLOBALS['params']['appli']['exp_name']['smtp_host'];
                              $mail->From = $GLOBALS['params']['appli']['exp_mail'];
                              $mail->FromName = $GLOBALS['params']['appli']['exp_name'];
                              $mail->AddAddress($_POST["TB_Adresse_Email"]);
                              $mail->Subject = $sujet;
                              $mail->Body = utf8_decode($message.$GLOBALS['textes']['mail']['PS'].$GLOBALS['textes']['mail']['sign']);
                             // $mail->Send();
                              // Si une erreur survient
                              //if(!@mail($_POST["TB_Adresse_Email"], $sujet, $message)){
                              if ( !$mail->Send() ) {
                                  echo "Une erreur est survenue lors de l'envoi du mail d'activation";                                                                   
                              }else{
                                   
                                   // Message de confirmation
                                   echo "Un email vient de vous être envoyé afin de terminer la procédure de réinitialisation";                                                                     
                                   // On masque le formulaire
                                   $masquer_formulaire = true;
                                   
                              }                              
                         } 
          	   		}else{
          	   			echo "Ce compte n'est pas actif, vous ne pouvez pas réinitialiser le mot de passe !";
          	   		}                 
          	   }else{
          	   	echo "Cette adresse mail n'existe pas !";
          	   }      
               // Fermeture de la connexion à la base de données
               mysql_close();
	               
          }                             
     }     
}