# LIP2FW cms

以前作ったLIP_Frameworkを一から書き直してCMSを作りやすいように改造したもの。

## 使い方概要

### 例：ニュース

用意するもの

* /data/action/News.php
* /data/model/News.php
* /data/view/news/***
* /data/yaml/form/News.yml

#### /data/action/News.php
コントローラ  
コントローラはほぼ記載するモノはありません。

`$_preffix`にview, modelのファイル・ディレクトリ名を指定。

#### /data/model/News.php
モデル  
Model_postで用意していないものを追加

Model_postに最初から入っているもの

* 投稿名
* 削除フラグ
* 表示/非表示フラグ
* 投稿日時
* 更新日時

#### /data/view/news/***
テンプレート  
一覧ページや入力ページをモリモリ記述

#### /data/yaml/form/News.yml
バリデーションルール