<?php
/**
* CONTROLLER FOR FemitidasLineas
* @author Sergio Perez <sergio.perez@albatronic.com>
* @copyright INFORMATICA ALBATRONIC SL 
* @since 07.06.2011 00:45:15

* Extiende a la clase controller
*/

class FemitidasLineasController extends Controller {

	protected $entity = "FemitidasLineas";
        protected $parentEntity = "FemitidasCab";

    public function listAction($idFactura='') {

        if ($idFactura == '')
            $idFactura = $this->request[2];

        $lis = new $this->entity();
        $rows = $lis->cargaCondicion('IDLinea', "IDFactura = '" . $idFactura . "'", 'IDLinea ASC');
        foreach ($rows as $row) {
            $lineas[] = new $this->entity($row['IDLinea']);
        }

        $template = $this->entity . '/list.html.twig';

        $this->values['linkBy']['value'] = $idFactura;
        $this->values['listado']['data'] = $lineas;

        unset($lis);
        unset($lineas);

        return array('template' => $template, 'values' => $this->values);
    }
}
?>