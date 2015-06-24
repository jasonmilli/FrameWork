<?php namespace Frame;
class View {
    public static function __callStatic($method, $arguments) {
        return "<$method>".implode('',$arguments)."</$method>";
    }
    public static function layout($in) {
        $out = '<table>';
        foreach ($in as $row) {
            $out .= '<tr>';
            foreach ($row as $column) $out .= "<td>$column</td>";
            $out .= '</tr>';
        }
        $out .= '</table>';
        return $out;
    }
    public static function layoutColumn($in) {
        $out = array();
        foreach ($in as $row) $out[] = array($row);
        return self::layout($out);
    }
    public static function layoutRow($in) {
        return self::layout(array($in));
    }
    public static function table($in) {
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
