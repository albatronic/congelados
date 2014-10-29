<?php

/**
 * Description of ClientesDentrega
 *
 * @author Sergio Pérez <sergio.perez@albatronic.com>
 * @copyright Informática ALBATRONIC, SL
 * @since 04-nov-2011
 *
 */
class ClientesDentrega extends ClientesDentregaEntity {

    public function __toString() {
        return $this->getDireccion();
    }

    /**
     * Devuelve un array con todas las direcciones de entrega 
     * vigentes de un cliente
     *
     * Si se omite el segundo parametro, como valor a mostar se genera un string
     * concatenando la dirección, el codigo postal y la poblacion
     *
     * Cada elemento tiene la primarykey y el valor de $column
     *
     * @param integer $idCliente El id del cliente
     * @param string $column Nombre de la columna a mostrar (opcional)
     * @return array Array con las direcciones de entrega
     */
    public function fetchAll($idCliente, $column='') {
        $this->conecta();

        if (is_resource($this->_dbLink)) {
            if ($column == '') {
                $column = "CONCAT(Direccion,' ',CodPostal,' ',Poblacion)";
            }
            $filtro = "WHERE (IDCliente='" . $idCliente . "') and Vigente='1'";
            $query = "SELECT IDDirec as Id,$column as Value FROM clientes_dentrega $filtro ORDER BY $column ASC;";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $this->_em->desConecta();
            unset($this->_em);
        }
        $rows[] = array('Id' => '', Value => ':: Dirección de Entrega');
        return $rows;
    }

    /**
     * Devuelve array con los id's de las direcciones
     * de entrega vigentes de la zona $idZona y cuyos clientes 
     * están vigentes y son de la sucursal en curso.
     * 
     * @param int $idZona
     * @return array
     */
    public function getDireccionesVigentesZona($idZona) {
        
        $rows = array();
        
        $this->conecta();

        if (is_resource($this->_dbLink)) {
            $query = "SELECT t1.IDDirec FROM clientes_dentrega t1, clientes t2 WHERE t1.IDZona='{$idZona}' AND t1.Vigente='1' AND t1.IDCliente=t2.IDCliente AND t2.Vigente='1' and t2.IDSucursal='{$_SESSION['suc']}'";
            $this->_em->query($query);
            $rows = $this->_em->fetchResult();
            $this->_em->desConecta();
            unset($this->_em);
        }  
        
        return $rows;
    }
}

