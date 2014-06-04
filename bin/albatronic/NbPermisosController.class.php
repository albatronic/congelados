<?php

/**
 * Description of NbPermisosController
 *
 * Este es un controlador comun a todas las apps.
 * Se utiliza para gestionar los permisos de acceso a las
 * opciones y subopciones de menu de cada perfil de usuario
 *
 * Cada app debe tener un modulo 'Permisos' y un controlador 'PermisosController' que
 * extienda a este.
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 20-ene-2012
 *
 */
class NbPermisosController extends Controller {

    protected $entity = "Permisos";
    protected $parentEntity = "";

    /**
     * Devuelve todas los permisos del perfil de usuario
     * indicado en la posicion 2 del request.
     * @return array
     */
    public function listopcionesAction() {
        $this->listado->filter['columnSelected'] = $this->form->getLinkBy();

        switch ($this->request['METHOD']) {
            case 'GET':
                $this->listado->filter['value'] = $this->request[2];
                $this->values['linkBy']['value'] = $this->request[2];
                break;

            case 'POST':
                $this->listado->filter['value'] = $this->request['IDPerfil'];
                $this->values['linkBy']['value'] = $this->request['IDPerfil'];
                $this->values['IDOpcion'] = $this->request['IDOpcion'];

                $menu = new Menu($this->values['linkBy']['value']);
                $subopciones = $menu->getSubopciones($this->request['IDOpcion'],$this->values['linkBy']['value']);
                unset($menu);

                //El campo Permisos lo transformo en un array con los
                //permisos independientes para poder tratar cada uno por separado
                foreach ($subopciones as $key => $value) {
                    $permisos['C'] = substr($subopciones[$key]['Permisos'], 0, 1);
                    $permisos['I'] = substr($subopciones[$key]['Permisos'], 1, 1);
                    $permisos['B'] = substr($subopciones[$key]['Permisos'], 2, 1);
                    $permisos['A'] = substr($subopciones[$key]['Permisos'], 3, 1);
                    $permisos['L'] = substr($subopciones[$key]['Permisos'], 4, 1);
                    $permisos['E'] = substr($subopciones[$key]['Permisos'], 5, 1);

                    $subopciones[$key]['Permisos'] = $permisos;
                }
                break;
        }

        $template = $this->entity . '/list.html.twig';

        $menu = new Menu($this->values['linkBy']['value']);
        $this->values['listado']['opciones'] = $menu->getOpciones($this->values['linkBy']['value']);
        $this->values['listado']['subopciones'] = $subopciones;
        $this->values['opciones'] = $menu->getOpciones();
        $this->values['subopciones'] = $menu->getSubopciones($this->request['IDOpcion']);

        unset($menu);
        return array('template' => $template, 'values' => $this->values);
    }

    /**
     * Añade una opcion de menu al perfil indicado
     * Recibe por post: IDPerfil y IDOpcion
     */
    public function anadiropcionAction() {
        $permiso = new Permisos();
        $permiso->setIDPerfil($this->request['IDPerfil']);
        $permiso->setIDOpcion($this->request['IDOpcion']);
        $permiso->create();

        $this->request[2] = $this->request['IDPerfil'];
        return $this->listopcionesAction();
    }

    /**
     * Borra una opcion de menu al perfil indicado
     * Recibe por post: IDPerfil y IDOpcion
     */
    public function borraropcionAction() {
        $em = new EntityManager("empresas");
        if (is_resource($em->getDbLink())) {
            $query = "delete from permisos where IDPerfil='" . $this->request['IDPerfil'] . "' and IDOpcion='" . $this->request['IDOpcion'] . "'";
            $em->query($query);
            $em->desConecta();
        }

        $this->request[2] = $this->request['IDPerfil'];
        return $this->listopcionesAction();
    }

    /**
     * Añade una subopcion de menu.
     * Recibe por post: IDPerfil, IDOpcion y IDSubopcion
     */
    public function anadirsubopcionAction() {
        $permiso = new Permisos();
        $permiso->setIDPerfil($this->request['IDPerfil']);
        $permiso->setIDOpcion($this->request['IDOpcion']);
        $permiso->setIDSubopcion($this->request['IDSubopcion']);
        $permiso->create();

        return $this->listopcionesAction();
    }

    /**
     * Borra una subopcion de menu al perfil indicado
     * Recibe por post: IDPerfil, IDOpcion y IDSubopcion
     */
    public function borrarsubopcionAction() {
        $em = new EntityManager("empresas");
        if (is_resource($em->getDbLink())) {
            $query = "delete from permisos where IDPerfil='" . $this->request['IDPerfil'] . "' and IDOpcion='" . $this->request['IDOpcion'] . "' and IDSubopcion='" . $this->request['IDSubopcion'] . "';";
            $em->query($query);
            $em->desConecta();
        }

        return $this->listopcionesAction();
    }

    /**
     * Guarda una subopcion
     * Recibe por post el Id del permiso
     */
    public function guardarAction() {
        $v = $this->request;

        $p  = ($v['C'] == 'on') ? 1 : 0;
        $p .= ( $v['I'] == 'on') ? 1 : 0;
        $p .= ( $v['B'] == 'on') ? 1 : 0;
        $p .= ( $v['A'] == 'on') ? 1 : 0;
        $p .= ( $v['L'] == 'on') ? 1 : 0;
        $p .= ( $v['E'] == 'on') ? 1 : 0;

        $permiso = new Permisos($this->request['IdPermiso']);
        $permiso->setPermisos($p);
        $permiso->save();

        $this->request[2] = $this->request['IDPerfil'];
        return $this->listopcionesAction();
    }

    /**
     * BOTONES DE MODIFICACION GLOBAL
     * ------------------------------
     */
    public function bconsultarAction() {
        if ($this->request['bconsultar'] == 'on')
            $p = '1'; else
            $p='0';

        $em = new EntityManager("empresas");
        if (is_resource($em->getDbLink())) {
            $query = "update permisos set Permisos=CONCAT('$p',SUBSTRING(Permisos,2,5)) where IDPerfil='" . $this->request['IDPerfil'] . "' and IDOpcion='" . $this->request['IDOpcion'] . "'";
            $em->query($query);
            $em->desConecta();
        }

        $this->request[2] = $this->request['IDPerfil'];
        $this->values['bconsultar'] = $this->request['bconsultar'];
        return $this->listopcionesAction();
    }

    public function binsertarAction() {
        if ($this->request['binsertar'] == 'on')
            $p = '1'; else
            $p='0';

        $em = new EntityManager("empresas");
        if (is_resource($em->getDbLink())) {
            $query = "update permisos set Permisos=CONCAT(SUBSTRING(Permisos,1,1),'$p',SUBSTRING(Permisos,3,4)) where IDPerfil='" . $this->request['IDPerfil'] . "' and IDOpcion='" . $this->request['IDOpcion'] . "'";
            $em->query($query);
            $em->desConecta();
        }

        $this->request[2] = $this->request['IDPerfil'];
        $this->values['binsertar'] = $this->request['binsertar'];
        return $this->listopcionesAction();
    }

    public function bborrarAction() {
        if ($this->request['bborrar'] == 'on')
            $p = '1'; else
            $p='0';

        $em = new EntityManager("empresas");
        if (is_resource($em->getDbLink())) {
            $query = "update permisos set Permisos=CONCAT(SUBSTRING(Permisos,1,2),'$p',SUBSTRING(Permisos,4,3)) where IDPerfil='" . $this->request['IDPerfil'] . "' and IDOpcion='" . $this->request['IDOpcion'] . "'";
            $em->query($query);
            $em->desConecta();
        }

        $this->request[2] = $this->request['IDPerfil'];
        $this->values['bborrar'] = $this->request['bborrar'];
        return $this->listopcionesAction();
    }

    public function bmodificarAction() {
        if ($this->request['bmodificar'] == 'on')
            $p = '1'; else
            $p='0';

        $em = new EntityManager("empresas");
        if (is_resource($em->getDbLink())) {
            $query = "update permisos set Permisos=CONCAT(SUBSTRING(Permisos,1,3),'$p',SUBSTRING(Permisos,5,2)) where IDPerfil='" . $this->request['IDPerfil'] . "' and IDOpcion='" . $this->request['IDOpcion'] . "'";
            $em->query($query);
            $em->desConecta();
        }

        $this->request[2] = $this->request['IDPerfil'];
        $this->values['bmodificar'] = $this->request['bmodificar'];
        return $this->listopcionesAction();
    }

    public function blistadoAction() {
        if ($this->request['blistado'] == 'on')
            $p = '1'; else
            $p='0';

        $em = new EntityManager("empresas");
        if (is_resource($em->getDbLink())) {
            $query = "update permisos set Permisos=CONCAT(SUBSTRING(Permisos,1,4),'$p',SUBSTRING(Permisos,6,1)) where IDPerfil='" . $this->request['IDPerfil'] . "' and IDOpcion='" . $this->request['IDOpcion'] . "'";
            $em->query($query);
            $em->desConecta();
        }

        $this->request[2] = $this->request['IDPerfil'];
        $this->values['blistado'] = $this->request['blistado'];
        return $this->listopcionesAction();
    }

    public function bexcelAction() {
        if ($this->request['bexcel'] == 'on')
            $p = '1'; else
            $p='0';

        $em = new EntityManager("empresas");
        if (is_resource($em->getDbLink())) {
            $query = "update permisos set Permisos=CONCAT(SUBSTRING(Permisos,1,5),'$p') where IDPerfil='" . $this->request['IDPerfil'] . "' and IDOpcion='" . $this->request['IDOpcion'] . "'";
            $em->query($query);
            $em->desConecta();
        }

        $this->request[2] = $this->request['IDPerfil'];
        $this->values['bexcel'] = $this->request['bexcel'];
        return $this->listopcionesAction();
    }

}

?>
