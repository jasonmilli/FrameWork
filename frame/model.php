<?php namespace Frame;
class Model {
    private $schema;
    private $table;
    private $pdo;
    private $primary = null;
    private $foreigns = array();
    protected function __construct($schema, $table) {
        $this->schema = $schema;
        $this->table = $table;
        $this->pdo = new \PDO("mysql:dbname={$this->schema};host:localhost", 'root', '');
        if (is_null($this->primary)) $this->primary = substr($this->table, 0, strlen($this->table - 1))."_id";
    }
    public function create($record) {
        if (!is_array($record)) throw new \Exception('Create function takes in argument of array');
        $columns = implode('`, `', array_keys($record));
        $values = $this->placeHolders($record, "'");
        $sql = <<<SQL
INSERT INTO `{$this->schema}`.`{$this->table}` (`$columns`, `created_at`, `updated_at`) VALUES($values, NOW(), NOW());
SQL;
        return $this->query($sql, array_values($record));
    }
    protected function placeHolders($columns, $mark = "'") {
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
                $placeholders[] = $column;
                $placeholders[] = $value;
                if ($first) {
                    $first = false;
                    $where .= ' WHERE ';
                } else $where .= ' AND ';
                $where .= "`?` = '?'";
            }
        }
        return array('wheres' => $where, 'placeholders' => $placeholders);
    }
    public function read($wheres = null) {
        $wheres = $this->where($wheres);
        $sql = <<<SQL
SELECT * FROM `?`.`?`{$wheres['wheres']};
SQL;
        return $this->query($sql, array_merge(array($this->schema, $this->table), $wheres['placeholders']));
    }
    protected function query($sql, $params) {
        print_r(array($sql, $params));
        $prep = $this->pdo->prepare($sql);
        $prep->execute($params);
        return $prep->fetchAll();
    }
    public function update($key, $values) {
        $set = '';
        $sets = array();
        foreach ($values as $column => $value) {
            $set .= "`?` = '?', r";
            $sets[] = $column;
            $sets[] = $value;
        }
        $sql = <<<SQL
UPDATE `?`.`?` SET $set `updated_at` = NOW() WHERE `?` = '?';
SQL;
        return $this->query($sql, array_merge(array($this->schema, $this->table), $sets, array($this->primary, $key)));
    }
    public function delete($key) {
        $sql = <<<SQL
DELETE FROM `?`.`?` WHERE `?` = '?';
SQL;
        return $this->query($sql, array($this->schema, $this->table, $this->primary, $key));
    }
    public function join($models, $wheres) {
        $using = '';
        $using_params = array();
        $joins = array($this);
        foreach ($models as $model) {
            $use = null;
            foreach ($joins as $join) {
                foreach ($model->foreigns as $foreign) {
                    if ($foreign == $join->primary) {
                        $use = $foreign;
                        break 2;
                    }
                }
                foreach ($join->foreigns as $foreign) {
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
            $using .= " JOIN `?`.`?` USING(`?`)";
            $using_params = array_merge($using_params, array($model->schema, $model->table, $use));
        }
        $wheres = $this->where($wheres);
        $sql = <<<SQL
SELECT * FROM `?`.`?`$join {$wheres['wheres']};
SQL;
        return $this->query($sql, array_merge(array($this->schema, $this->table), $using_params, $wheres['placeholders']));
    }
}
