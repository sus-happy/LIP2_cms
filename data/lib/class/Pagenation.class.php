<?php

/**
 * ページネーションクラス
 *
 * @package Global
 * @author SUSH
 * @version 0.0.1
 * @uses Url
 */

class Pagenation extends Object {
    private static
            $path = NULL,
            $total = 0,
            $per_page = 1,
            $max_page = -1,
            $page = 1,
            $span = 5,
            $param = array();

    // ページャーラッパー
    private static
        $wrap = array(
            'before'=> '<div class="text-center"><ul class="pagination text-center">',
            'after' => '</ul></div>',
        );
    // ページ番号ラッパー
    private static
        $num_wrap = array(
            'before'=> '<li><a href="%link%">&lt;</a></li>',
            'after' => '<li><a href="%link%">&gt;</a></li>',
            'first' => '<li><a href="%link%">&laquo;</a></li>',
            'last'  => '<li><a href="%link%">&raquo;</a></li>',
            'number'=> '<li><a href="%link%">%number%</a></li>',
            'active'=> '<li class="active"><span>%number%</span></li>',
        );

    /**
     * 設定情報を一気に設定
     *
     * @access public
     * @param array $param
     */
    public static function set_up( $param ) {
        // ベースURL
        if( isset( $param['path'] ) )
            self::set_base_path( $param['path'] );
        // 総数
        if( isset( $param['total'] ) )
            self::set_total_count( $param['total'] );
        // 表示数
        if( isset( $param['per_page'] ) )
            self::set_per_page( $param['per_page'] );
        // ページ番号
        if( isset( $param['current'] ) )
            self::set_current_page( $param['current'] );
        // 前後表示ページ数
        if( isset( $param['span'] ) )
            self::set_param( $param['span'] );
        // パラメータ
        if( isset( $param['param'] ) )
            self::set_param( $param['param'] );
    }

    /**
     * ページャーのベースURLの指定
     *
     * @access public
     * @param string $path
     */
    public static function set_base_path( $path ) {
        self::$path = $path;
    }

    /**
     * 記事総数の登録
     *
     * @access public
     * @param integer $total
     */
    public static function set_total_count( $total ) {
        self::$total = $total;
    }

    /**
     * 一ページの表示数の登録
     *
     * @access public
     * @param integer $per_page
     */
    public static function set_per_page( $per_page ) {
        self::$per_page = $per_page;
    }

    /**
     * 現在表示中のページ番号の登録
     *
     * @access public
     * @param integer $page
     */
    public static function set_current_page( $page ) {
        self::$page = $page;
    }

    /**
     * 追加パラメータ
     *
     * @access public
     * @param array $param
     */
    public static function set_param( $param ) {
        if( is_array( $param ) )
            self::$param = $param;
    }

    public static function check_max() {
        self::$max_page = ceil(self::$total/self::$per_page);
        if( self::$page > self::$max_page ) {
            self::$page = self::$max_page;
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 出力
     *
     * @access public
     */
    public static function view() {
        if( self::$max_page < 0 )
            self::check_max();

        if( self::$page > 1 || self::$max_page > 1 ) {
            echo self::$wrap["before"];

            if( count( self::$param ) ) {
                $query = '?'.http_build_query( self::$param );
            }

            $from = self::$page-self::$span;
            if( $from < 1 )
                $from = 1;
            $to   = $from + self::$span*2;
            if( $to > self::$max_page )
                $to = self::$max_page;

            /**
             * 最初のページ
             */
            if( $from > 1 )
                echo str_replace( "%link%", Url::site_url( sprintf( "%s/%s", self::$path, 1 ) ).$query, self::$num_wrap["first"] );

            /**
             * 前ページ
             */
            if( self::$page > 1 )
                echo str_replace( "%link%", Url::site_url( sprintf( "%s/%s", self::$path, self::$page-1) ).$query, self::$num_wrap["before"] );

            for( $i=$from; $i<=$to; $i++ ) {
                if (self::$page == $i) {
                    echo str_replace(
                        array( "%number%" ),
                        array( $i ),
                        self::$num_wrap["active"]
                    );
                } else {
                    echo str_replace(
                        array( "%link%", "%number%" ),
                        array( Url::site_url( sprintf( "%s/%s", self::$path, $i) ).$query, $i ),
                        self::$num_wrap["number"]
                    );
                }
            }

            /**
             * 次ページ
             */
            if( self::$page*self::$per_page < self::$total )
                echo str_replace( "%link%", Url::site_url( sprintf( "%s/%s", self::$path, self::$page+1) ).$query, self::$num_wrap["after"] );

            /**
             * 最後のページ
             */
            if( $to < self::$max_page )
                echo str_replace( "%link%", Url::site_url( sprintf( "%s/%s", self::$path, self::$max_page ) ).$query, self::$num_wrap["last"] );

            echo self::$wrap["after"];
        }
    }

    public static function get_page() {
        return self::$page;
    }
    public static function get_max_page() {
        return self::$max_page;
    }
    public static function get_total() {
        return self::$total;
    }
}