<?php

/**
 * CONTROLLER FOR Lotes
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL 
 * @since 07.06.2011 00:45:16

 * Extiende a la clase controller
 */
class LotesController extends Controller {

    protected $entity = "Lotes";
    protected $parentEntity = "";

    /**
     * Descatalogar los lotes (poner no vigentes) que no tengan existencias
     * entre todos los almacenes de la empresa
     */
    public function DescatalogarAction() {
        if ($this->values['permisos']['A']) {

            $lote = new Lotes();
            $dbLotes = $lote->getDataBaseName();
            $existencias = new Existencias();
            $dbExistencias = $existencias->getDataBaseName();
            unset($lote);
            unset($existencias);

            $em = new EntityManager("datos" . $_SESSION['emp']);
            if ($em->getDbLink()) {
                $query = "
                UPDATE {$dbLotes}.lotes SET Vigente='0'
                WHERE IDLote not in (
                    SELECT IDLote
                    FROM {$dbExistencias}.existencias e
                    WHERE (
                        IDLote>0 AND
                        (
                            Reales>0 OR
                            Reservadas>0 OR
                            Entrando>0
                        )
                    )
                    GROUP BY IDLote
                    ORDER BY IDLote
                );";
                $em->query($query);
                $em->desConecta();
            }

            return $this->indexAction();
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

    /**
     * Catalogar (poner vigentes) todos los lotes
     */
    public function CatalogarAction() {

        if ($this->values['permisos']['A']) {

            $lote = new Lotes();
            $dbLotes = $lote->getDataBaseName();
            unset($lote);

            $em = new EntityManager("datos" . $_SESSION['emp']);
            if ($em->getDbLink()) {
                $query = "UPDATE {$dbLotes}.lotes SET Vigente='1';";
                $em->query($query);
                $em->desConecta();
            }

            return $this->indexAction();
        } else {
            return array('template' => '_global/forbiden.html.twig');
        }
    }

}

?>