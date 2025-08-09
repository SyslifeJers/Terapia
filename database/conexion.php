<?php

class Database
{
    private $servername = 'localhost';
    private $dbname = 'u529445062_cenere';
    private $username = 'u529445062_jers';
    private $password = 'Rtx2080_';
    private $conn;


    public function __construct()
    {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        if ($this->conn->connect_error) {
            die("Conexión fallida: " . $this->conn->connect_error);
        }
        $this->conn->set_charset("utf8");
    }


    public function getConnection()
    {
        return $this->conn;
    }


    public function closeConnection()
    {
        if ($this->conn) {

            $this->conn->close();
        }
    }
    public function getOpcionesPagoCurso() {
    $stmt = $this->conn->prepare("SELECT `id_opcion`, `numero_pagos`, a.`id_frecuencia`,f.tipo, `activo`, `costo_adicional`, `nota` FROM `opciones_pago` a INNER join frecuencia_pago f on f.id_frecuencia = a.id_frecuencia WHERE `activo` = 1;");
    $stmt->execute();
    $result = $stmt->get_result();
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}