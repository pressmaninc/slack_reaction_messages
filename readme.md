# Slack Reaction Messages
## 概要
Slackの全公開チャンネルから指定リアクションが付与されているメッセージ、スレッドを取得してCSVファイルに出力する。  
CSVの内容は「投稿者」「メッセージ」「リアクション数」「リンク」である。

## 使用方法
### 前提条件
Slackで事前にアプリを作成し、[スコープの設定とOAuth認証](https://api.slack.com/legacy/oauth)までを完了させる必要がある。

本アプリに必要な権限は以下である。

- User Token Scopes
	- channels:history
	- channels:read
	- users:read

### 設定
1. 本リポジトリをローカルにダウンロードする。
2. 以下コマンドで.envファイルを作成する
`cp .env.sample .env`
3. .envファイル内の各変数を適切な値に変更する
4. `composer install` にて必要な外部ライブラリをインストールする

### 実行方法
リポジトリのディレクトリ直下に移動して
`php execute.php` にて実行する。

csvディレクトリに「現在日時.csv」というファイル名で結果がcsvファイルで出力される。

## 自動テスト
phpunitによる自動テストを導入している。  
Slack APIのレスポンスとしてmockを用意している。  
mockはtests/mockディレクトリに格納されている。  

### 実行方法
リポジトリのディレクトリ直下に移動して、以下コマンドでユニットテストが実行される。  
`./vendor/phpunit/phpunit/phpunit`
