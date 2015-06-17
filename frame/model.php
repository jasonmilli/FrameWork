<?php namespace Frame;
class Model {
    private $schema;
    private $table;
    private $pdo;
    protected function __construct($schema, $table) {
        $this->pdo = new \PDO("mysql:dbname={$this->schema};host:localhost", 'root', '');
    }
    public function create($record) {
        if (!is_array($record)) throw new \Exception('Create function takes in argument of array');
        $columns = $this->placeHolders($record, "`");
        $values = $this->placeHolders($record, "'");
        $sql = <<<SQL
INSERT INTO `?`.`?` ($columns, `created_at`, `updated_at`) VALUES($values, NOW(), NOW());
SQL;
        $this->query($sql, array_merge(array($this->schema, $this->table), array_keys($record), array_values($record)));
    }
    private function placeHolders($columns, $mark = "'") {
        $first = true;
        for ($i = 0; $i < count($columns); $i++) {
            if ($first) {
                $return = '?';
                $first = false;
            } else $return .= ',?';
        }
        return $return;
    }
    public function read($wheres = null) {
        $where = '';
        if (is_array($wheres)) {
            $first = true;
            $placeholders = array();
            foreach ($wheres as $column => $value) {
                $placeholders[] = $column;
                $placeholders[] = $value;
                if ($first) {
                    $first = false;
                    $where .= ' WHERE ';
                } else $where .= ' AND ';
                $where .= "`?` = '?'";
            }
        }
        $sql = <<<SQL
SELECT * FROM `?`.`?`$where;
SQL;
        $result = $this->query($sql, array_merge(array($this->schema, $this->table), $placeholders));
        print_r($result);
    }
    private function query($sql, $params) {
        $prep = $this->pdo->prepare($sql);
        $prep->execute($params);
        return $prep->fetchAll();
    }
    public function update($key, $values) {
        $set = '';
        $sets = array();
        foreach ($values as $column => $value) {
            $set .= '? = ?, ';
            $sets[] = $column;
            $sets[] = $value;
        }
        $sql = <<<SQL
UPDATE `?`.`?` SET $set `updated_at` = NOW() WHERE `?` = '?';
SQL;
        $primary = substr($this->table, 0, strlen($this->table - 1))."_id";
        $this->query($sql, array_merge(array($this->schema, $this->table), $sets, array($primary, $key)));
    }
    public function delete($key) {
        $primary = substr($this->table, 0, strlen($this->table - 1))."_id";
        $sql = <<<SQL
DELETE FROM `?`.`?` WHERE `?` = '?';
SQL;
        $this->query($sql, array($this->schema, $this->table, $primary, $key));
    }
    public function join($models, $wheres) {
        $using = null;
        $models[] = $this;
        foreach ($models as $model)
        foreach ($this->foreign as $foreign) {
            
}
