<?php

/**
 * Description of LoginController
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @date 28-may-2011
 *
 */

class LoginController {

    /**
     * Variables enviadas en el request por POST o por GET
     * @var array
     */
    protected $request;

    public function __construct($request) {
        $this->request = $request;
    }

    public function LoginAction() {
        unset($_SESSION['USER']);

        $user = new Agentes();
        $rows = $user->cargaCondicion('*', "Login='" . $this->request['user'] . "' and Activo='1'");

        if ($user->getStatus()) {
            $user = new Agentes($rows[0]['IDAgente']);
            if ($user->getPassword() == md5($this->request['password'])) {
                $menu = new Menu($user->getIDPerfil()->getIDPerfil());
                $empresas = $user->getEmpresas();

                $_SESSION['USER'] = array(
                    'user' => array(
                        'id' => $user->getIDAgente(),
                        'Nombre' => $user->getNombre(),
                        'IDPerfil' => $user->getIDPerfil()->getIDPerfil(),
                    ),
                    'empresas' => $empresas,
                    'sucursales' => $user->getSucursales($empresas[0]['Id']),
                    'menu' => $menu->getArrayMenu(),
                );


                //Activar la empresa y sucursal por defecto
                if (count($_SESSION['USER']['empresas'])) {
                    $_SESSION['emp'] = $_SESSION['USER']['empresas'][0]['Id'];
                    $empresa = new Empresas($_SESSION['emp']);
                    $_SESSION['ver'] = $empresa->getIDVersion()->getIDTipo();
                }
                if (count($_SESSION['USER']['sucursales']))
                    $_SESSION['suc'] = $_SESSION['USER']['sucursales'][0]['Id'];

                //Desactivar el TPV
                unset($_SESSION['tpv']);

                //Actualizar el registro de entradas
                $user->setIDEmpresa($user->getIDEmpresa()->getIDEmpresa());
                $user->setIDSucursal($user->getIDSucursal()->getIDSucursal());
                //$user->setPassword($this->request['password']);
                $user->setIDPerfil($user->getIDPerfil()->getIDPerfil());
                $user->setNLogin($user->getNLogin() + 1);
                $user->setUltimoLogin(date('Y-m-d H:i:s'));
                $user->save();

                // Cargo las aplicaciones disponibles
                $config = sfYaml::load('config/config.yml');
                $values['apps'] = $config['config']['apps'];
                $template = $this->entity . "Login/Apps.html.twig";
            } else {
                $values = array('loginMensaje' => 'Password Incorrecta');
                $template = "Index/Index.html.twig";
            }
        } else {
            $values = array('loginMensaje' => 'Usuario no registrado');
            $template = "Index/Index.html.twig";
        }

        return array('template' => $template, 'values' => $values,);
    }

    /**
     * Olvidó la contraseña
     */
    public function ForgotAction() {
        switch ($this->request['METHOD']) {
            case 'GET':
                $template = $this->entity . "/Login/forgot.html.twig";
                break;

            case 'POST':
                $usu = new Agentes();
                $rows = $usu->cargaCondicion('*', "EMail='" . $this->request['EMail'] . "'");
                $usu = new Agentes($rows[0]['IDAgente']);
                if ($usu->getIDAgente() != '') {
                    $passw = new Password(6);
                    $nueva = $passw->genera();
                    unset($passw);
                    $usu->setPassword($nueva);
                    $usu->save();

                    $config = sfYaml::load('config/config.yml');

                    $to = $this->request['EMail'];
                    $subject = " Paswword regenerada";
                    $message = "<p>" . $config['config']['app']['name'] . "</p><p>Le ha sido generada una contrase&ntilde;a nueva de acceso al sistema.</p>" .
                            "<p>La contrase&ntilde;a nueva es: " . $nueva . "</p>";

                    $mail = new Mail();
                    $values['mensaje'] = $mail->send($to, '', 'Administrador', $subject, $message, array());
                    unset($mail);
                } else
                    $values['mensaje'] = "Ese EMail no consta en nuestra base de datos.";

                unset($usu);

                $template = $this->entity . "/Login/forgot.html.twig";
                break;
        }

        return array('template' => $template, 'values' => $values,);
    }

    /* ESTA FUNCION ES PARA CUANDO SE ENTRA DESDE EL PORTAL DE APLICACIONES
      public function LoginAction()
      {
      $em = new entityManager("portal");
      $em->query("select * from portal.users where EMail='".$this->request['user']."'");
      $row = $em->fetchResult();
      $usuario = $row[0];

      if($usuario){
      if($usuario['Password']==$this->request['password']){
      $query="select apps.* from apps,users_apps where users_apps.IDUser='".$usuario['IDUser']."' and users_apps.IDApp=apps.IDApp";
      $em->query($query);
      $apps = $em->fetchResult();

      $values = array('usuario'=>$usuario,'apps'=>$apps);
      $template = "_global/Apps.html.twig";
      } else {
      $values = array('loginMensaje'=>'Password Incorrecta');
      $template = "Index/Index.html.twig";
      }
      } else {
      $values = array('loginMensaje'=>'Usuario no registrado');
      $template = "Index/Index.html.twig";
      }

      return array('template'=>$template,'values'=>$values,);
      }
     */
}

?>