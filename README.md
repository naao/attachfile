- Attachfile-1.03

- DATE : 2012-7-15
- author : naao
-- http://www.naaon.com/

Attachfileモジュールの、d3forumコメント統合時の拡張性を上げたハック版です。
　　将来的にAttachfiloeのブロックができた時、複数のモジュールのブロックを同一ページ
　　に配置することも、今回の方法の延長線上で可能になると思います。

xoops_trust_path/modules/attachfile/plugins/　以下に、当該モジュールの
　　trust_path/permission.php　を置きます。

このファイルの書き方が、若干変わっています。_sample/フォルダ内のpermission.phpで
　　説明すると、

      class bulletinAttachfilePlugin extends AttachfilePluginAbstract{

      function bulletinAttachfilePlugin( $parentObj )

　　この「bulletin」部分は、全て当該モジュールのtrust側のディレクトリ名になります。
　　その他は、従来の書き方と（多分）変更なしで行けると思います。
　　（違いは、関数に渡す引数を１つだけで良いように変更してます。）

本パックの同梱したd3forum用のプラグインには、コメント統合した時のコメント元モジュール
　　の記事の閲覧権限チェックを追加しています。
　　実際の制御は、当該コメント元モジュールのコメント統合クラス内の「validate_id」メソッド
　　の実装内容に依存します。
　　なお、d3forum本体のコメント元記事権限連動は。以下のページを参考に願います。
　　http://www.naaon.com/modules/xpwiki/233.html

コメント統合元記事ページのコメント一覧「(d3forum)_comment_listposts_flat.html 」
　　テンプレートに、

　　<{attachfile_attach_download dirname=attachfile target_dirname=d3forum target_id=$post.id}>

　　と書くことで、コメント元ページのコメント一覧でも、添付ファイルの存在を知り、
　　リンクからダウンロードウインドウを開くことができます。
　　target_dirname=d3forum　の「d3forum」部分は、d3forumのroot_path側インストール
　　ディレクトリ名に変更してください。
　　（通常のメインページに使用時は、いあっまで通りの表記に省略可能です。）

まだ駆け出しですので、不具合等がありましたら、naaoまでご報告ください。

改定履歴

-- 1.02_diff_003（2010-11-09）
　　d3forumプラグインで、コメント統合していないフォーラムで添付参照権限が取得できなかった不具合の修正。

-- 1.02_diff_002（2010-11-04）
　　d3forumプラグイン内の、d3commentのObj取得をtarget_dirnameにつき１回で済むように改善。

以上