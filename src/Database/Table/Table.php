<?php namespace App\Database\Table;

use App\Database\Connexion;
use PDO;

class Table {

    protected $pdo;
    protected $class = null;

    public function __construct()
    {
        $this->pdo = Connexion::getPDO();
    }

    public function query(string $query)
    {
        return $this->pdo->query($query);
    }

        
    /** @param  string $fetchMode ASSOC|NUM|CLASS */
    public function queryFetch(string $query, string $fetchMode = 'ASSOC'): array
    {
        $query = $this->pdo->query($query);
        if($fetchMode == 'CLASS') {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->class);
        } elseif($fetchMode == 'NUM') {
            $query->setFetchMode(PDO::FETCH_NUM);
        } else {
            $query->setFetchMode(PDO::FETCH_ASSOC);
        }
        return $query->fetch();
    }  

    protected function queryfetchAllClass(string $query)
    {
        return $this->pdo
            ->query($query)
            ->fetchAll(PDO::FETCH_CLASS, $this->class);
    }

    protected function prepared(string $query, array $params): void
    {
        $query = $this->pdo->prepare($query);
        $query->execute($params);
    }

    protected function prepareFetchClass(string $query, array $params)
    {
        $query = $this->pdo->prepare($query);
        $query->execute($params);
        $query->setFetchMode(PDO::FETCH_CLASS, $this->class);
        return $query->fetch();
    }

    protected function prepareFetchAllClass(string $query, array $params)
    {
        $query = $this->pdo->prepare($query);
        $query->execute($params);
        return $query->fetchAll(PDO::FETCH_CLASS, $this->class);
    }

    /**
     * Run SQL 'CREATE TABLE' action in database
     * @todo Beware to set PDO::ATTR_ERRMODE on PDO::ERRMODE_SILENT (default) for query() to return FALSE on failure
     * @return string
     */
    function createTable(string $tableName, string$columnsPartOfQuery): string
    {
        Connexion::setErrModeToNormalIfNotYet();
        $query = $this->pdo->query(
            "CREATE TABLE ".DB_NAME.".$tableName ($columnsPartOfQuery) 
            ENGINE = InnoDB"
        );
        if(!$query) return "<br><span style='color:red'>La table <b>".DB_NAME.".$tableName</b> n'a pas été créée car une erreur est survenue.</span>";
        return "<br>La table <b>".DB_NAME.".$tableName</b> a été créée.";
    }

}