<?php
/**
 * LipFW2 Loader
 *
 * @author SUSH
 * @version 0.0.1
 */

/*
ini_set( 'error_reporting', E_ALL ^E_NOTICE );
ini_set( 'display_errors', TRUE );
*/

/** LipFW2のデータのディレクトリ指定 */
define( 'LIP_DATA_DIR', dirname(__FILE__).'/../data' );
/** 実行モード:読み込むconfigファイルの振り分け */
define( 'LIP_MODE', 'local' );

/**
 * 以降は変更不要（のはず）
 */

/**
 * 設定ファイルの読込
 */
require_once( LIP_DATA_DIR.'/lib/class/Config.class.php' );

Config::set_version( LIP_MODE );
Config::import( array(
    'global',
    'session',
    'database',
    'auth',
) );

/**
 * ローダークラスの初期設定
 */
require_once( Config::get_param( 'global', 'site', 'data' ).'/lib/class/Loader.class.php' );

/**
 * セッションクラスの初期設定
 */
Session::start( Config::get_param( 'session', 'key' ) );

/**
 * データベース接続の初期設定
 */
Database::connect();

/**
 * ビュークラスの初期設定
 */
View::reset_var();

/**
 * 関数群
 */
Loader::get_func( 'echo' );

/**
 * YAMLファイル用ライブラリ
 */
require_once( Config::get_param( 'global', 'site', 'data' ).'/inc/spicy.php' );

