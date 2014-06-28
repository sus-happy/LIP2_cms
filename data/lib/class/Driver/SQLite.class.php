<?php

/**
 * モデルクラス：SQLiteドライバー
 *
 * @package Database
 * @author SUSH
 * @version 0.0.1
 */

class Driver_SQLite extends Object {

    public static function init( $db ) {
        if (version_compare($db->getAttribute(PDO::ATTR_SERVER_VERSION), '3.5.4')<0) {
            $step_func = create_function('&$context, $rownumber, $string, $delimiter=\',\'', 'if (isset($string)) { if (isset($context)) $context .= $delimiter; $context .= $string; } return $context;');
            $finalize_func = create_function('&$context, $rownumber', 'return $context;');
            $db->sqliteCreateAggregate('group_concat', $step_func, $finalize_func);
        }
    }

}
