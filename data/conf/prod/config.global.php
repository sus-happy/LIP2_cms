<?php

ini_set( 'display_errors', 0 );
ini_set( 'error_reporting', E_ALL & ~E_NOTICE );

// 名称
$conf['site']['name'] = 'サイト名';
// 公開画面URL
$conf['site']['pub']  = 'http://www.example.com/';
// 管理画面URL
$conf['site']['url']  = 'http://www.example.com/admin/';
// PATH_INFO使用時のベースファイル名
$conf['site']['file'] = 'index.php';
// LIP2FWの格納してあるディレクトリパス
$conf['site']['data'] = '/path/to/data';

// ベースクラス
$conf['index']['path'] = 'top';
// ベース関数
$conf['index']['func'] = 'init';
// 404クラス
$conf['404']['path'] = 'notfound';
// 404関数
$conf['404']['func'] = 'error';
