<?php namespace Frame;
abstract class Model {
    private $schema = null;
    private $table = null;
    private $pdo;
    private $primary = null;
    private $foreigns;
    public function __construct($schema = null, $table = null) {
        if (is_null($schema) || is_null($table)) {//throw new \Exception('Schema and Table need to be passed in with the constructor');
            $ref = new \ReflectionClass(get_class($this));
            //echo $ref->getNamespaceName();
            $parts = explode('\\', $ref->getName());
            if (count($parts) == 4 && $parts[0] == 'Work' && $parts[1] == 'Models') {
                print_r($parts);
                if (is_null($schema) && is_null($this->schema)) $schema = strtolower($parts[2]);
                if (is_null($table) && is_null($this->table)) $table = strtolower($parts[3]);
            }
        }
        if (!isset($this->foreigns)) $this->foreigns = array;
        $this->schema = $schema;
        $this->table = $table;
        $this->pdo = new \PDO("mysql:dbname={$this->schema};host:localhost", 'root', '');
        if (is_null($this->primary)) $this->primary = substr($this->table, 0, strlen($this->table) - 1)."_id";
    }
    public function create($record) {
        if (!is_array($record)) throw new \Exception('Create function takes in argument of array');
        $columns = implode('`, `', array_keys($record));
        $values = $this->placeHolders($record);
        $sql = <<<SQL
INSERT INTO `{$this->schema}`.`{$this->table}` (`$columns`, `created_at`, `updated_at`) VALUES($values, NOW(), NOW());
SQL;
        return $this->query($sql, array_values($record));
    }
    protected function placeHolders($columns, $mark = "") {
        $first = true;
        for ($i = 0; $i < count($columns); $i++) {
            if ($first) {
                $return = $mark.'?'.$mark;
                $first = false;
            } else $return .= ','.$mark.'?'.$mark;
        }
        return $return;
    }
    protected function where($wheres) {
        $where = '';
        if (is_array($wheres)) {
            $first = true;
            $placeholders = array();
            foreach ($wheres as $column => $value) {
                $placeholders[] = $value;
                if ($first) {
                    $first = false;
                    $where .= ' WHERE ';
                } else $where .= ' AND ';
                $where .= "`$column` = ?";
            }
        }
        return array('wheres' => $where, 'placeholders' => $placeholders);
    }
    public function read($wheres = null) {
        $wheres = $this->where($wheres);
        $sql = <<<SQL
SELECT * FROM `{$this->schema}`.`{$this->table}`{$wheres['wheres']};
SQL;
        return $this->query($sql, $wheres['placeholders']);
    }
    protected function query($sql, $params) {
        print_r(array($sql, $params));
        $prep = $this->pdo->prepare($sql);
        if (!$prep->execute($params)) return $prep->errorInfo();
        return $prep->fetchAll();
    }
    public function update($key, $values) {
        $set = '';
        $sets = array();
        foreach ($values as $column => $value) {
            $set .= "`$column` = ?, ";
            $sets[] = $value;
        }
        $sql = <<<SQL
UPDATE `{$this->schema}`.`{$this->table}` SET $set `updated_at` = NOW() WHERE `{$this->primary}` = ?;
SQL;
        return $this->query($sql, array_merge($sets, array($key)));
    }
    public function delete($key) {
        $sql = <<<SQL
DELETE FROM `{$this->schema}`.`{$this->table}` WHERE `{$this->primary}` = ?;
SQL;
        return $this->query($sql, array($key));
    }
    public function join($models, $wheres = array()) {
        $using = '';
        $joins = array($this);
        if (!is_array($models)) $models = array($models);
        print_r($models);
        foreach ($models as $model) {
            echo "{$model->primary}\n";
            $use = null;
            foreach ($joins as $join) {
                echo " {$join->primary}\n";
                foreach ($model->foreigns as $foreign) {
                    echo "  $foreign\n";
                    if ($foreign == $join->primary) {
                        $use = $foreign;
                        break 2;
                    }
                }
                print_r($model);
                print_r($model->foreigns);
                foreach ($join->foreigns as $foreign) {
                    echo "   $foreign\n";
                    if ($foreign == $model->primary) {
                        $use = $foreign;
                        break 2;
                    }
                }
            }
            if (is_null($use)) {
                throw new \Exception("No Foreign key link found for `{$model->schema}`.`{$model->table}`");
            }
            $joins[] = $model;
            $using .= " JOIN `{$model->schema}`.`{$model->table}` USING(`$use`)";
        }
        $wheres = $this->where($wheres);
        $sql = <<<SQL
SELECT * FROM `{$this->schema}`.`{$this->table}`$using {$wheres['wheres']};
SQL;
        return $this->query($sql, $wheres['placeholders']);
    }
}
