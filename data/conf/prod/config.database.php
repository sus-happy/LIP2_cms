<?php

$conf['driver'] = 'sqlite';
$conf['user']   = '';
$conf['pass']   = '';
$conf['host']   = '';
$conf['dbname'] = Config::get_param( 'global', 'site', 'data' ).'/db/db.db';
