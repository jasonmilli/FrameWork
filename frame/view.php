<?php namespace Frame;
class View {
    public static function __callStatic($method, $arguments) {
        return "<$method>".implode('',$arguments)."</$method>";
    }
    public static function layout($in) {
        //print_r($in);
        $rows = array();
        foreach ($in as $row) {
            $columns = array();
            foreach ($row as $column) $columns[] = self::td($column);
            print_r($columns);
            $rows[] = self::tr($columns);
        }
        return self::table($rows);
    }
    public static function layoutColumn($in) {
        //print_r($in);
        $out = array();
        foreach ($in as $row) $out[] = array($row);
        return self::layout($out);
    }
    public static function layoutRow($in) {
        //print_r($in);
        return self::layout(array($in));
    }
    public static function tableWithHead($in) {
        $out = '<table><tr>';
        if (count($in) < 1) return '';
        foreach ($in[0] as $head => $throw) $out .= "<th>$head</th>";
        foreach ($in as $row) {
            $out .= '</tr><tr>';
            foreach ($row as $column) $out .= "<td>$column</td>";
        }
        $out .= '</tr></table>';
        return $out;
    }
}
