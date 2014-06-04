<?php

/**
 * CONTROLLER FOR Existencias
 * @author Sergio Perez <sergio.perez@albatronic.com>
 * @copyright INFORMATICA ALBATRONIC SL 
 * @since 07.06.2011 00:45:15

 * Extiende a la clase controller
 */
class ExistenciasController extends Controller {

    protected $entity = "Existencias";
    protected $parentEntity = "";

    /**
     * Genera una listado por pantalla en base al filtro.
     * Puede recibir un filtro adicional
     *
     * @param string $aditionalFilter
     * @return array con el template y valores a renderizar
     */
    public function listAction($aditionalFilter='') {

        $this->listado->setQuery($this->makeQuery());

        return parent::listAction($aditionalFilter);
    }

    /**
     * Genera un listado en formato PDF en base a los parametros obtenidos
     * del fichero listados.yml de cada controlador y los datos filtrados
     * segun el request
     * @return array Template y valores
     */
    public function listadoAction($aditionalFilter='') {

        $this->listado->setQuery($this->makeQuery());

        return parent::listadoAction($aditionalFilter);
    }

    public function exportarAction($aditionalFilter = '') {

        $this->listado->setQuery($this->makeQuery());

        return parent::exportarAction($aditionalFilter);
    }

    /**
     * Crea la sentencia SQL en base a lo que venga en el filtro
     * @return string Sentencia SQL
     */
    private function makeQuery() {

        $filtro = "";

        foreach ($this->request['filter']['columnsSelected'] as $key => $columna) {
            if ($this->request['filter']['valuesSelected'][$key] != '') {
                if ($filtro != '')
                    $filtro .= ' AND ';
                $filtro .= "({$this->form->getDataBaseName()}.existencias.{$columna} like '{$this->request['filter']['valuesSelected'][$key]}')";
            }
        }

        if ($this->request['filter']['flags']['soloDisponible']) {
            if ($filtro != '')
                $filtro .= " AND ";
            $filtro .= "(Reales-Reservadas>0)";
        }

        if ($this->request['filter']['flags']['agrupado']) {
            if ($this->request['filter']['valuesSelected'][0] == '')
                $query = "select IDArticulo,sum(Reales) as Reales,sum(Reservadas) as Reservadas,sum(Entrando) as Entrando from {$this->form->getDataBaseName()}.existencias where ({$filtro}) GROUP BY IDArticulo order by {$this->request['filter']['orderBy']}";
            else
                $query = "select IDAlmacen, IDArticulo,sum(Reales) as Reales,sum(Reservadas) as Reservadas,sum(Entrando) as Entrando from {$this->form->getDataBaseName()}.existencias where ({$filtro}) GROUP BY IDAlmacen,IDArticulo order by {$this->request['filter']['orderBy']}";
        } else {
            $query = "select {$this->form->getDataBaseName()}.existencias.* from {$this->form->getDataBaseName()}.existencias where ({$filtro}) {$groupBy} order by {$this->request['filter']['orderBy']}";
        }

        return $query;
    }

}

?>