#!/usr/bin/perl
################################################################################
# 高機能アクセス解析CGI Professional版 （管理者用）
# Ver 4.11.2
# Copyright(C) futomi 2001 - 2008
# http://www.futomi.com/
###############################################################################
use strict;
BEGIN {
	use FindBin;
	if($FindBin::Bin && $FindBin::Bin ne "/") {
		push(@INC, "$FindBin::Bin/lib");
		chdir $FindBin::Bin;
	} else {
		push(@INC, "./lib");
	}
}
use CGI;
use CGI::Carp qw(fatalsToBrowser);
my $q = new CGI;
$| = 1;
#バージョン情報
my $CGI_VERSION = '4.11.2';
#著作権表示の定義
my $COPYRIGHT = "futomi's CGI Cafe - 高機能\アクセス解析CGI Professional $CGI_VERSION";
my $COPYRIGHT2 = "<a href=\"http://www.futomi.com\" target=\"_blank\">$COPYRIGHT</a>";
#このCGIのURL
my $CGI_URL = 'setup.cgi';
my $CONF_DATA = './data/config.cgi';
#フリーサーバのドメインリスト（正規表現）
my $FREE_SERVER_NAME = '\.tok2\.com|\.infoseek\.co\.jp|\.xrea\.com';

my @CONF_KEYS = (
	'ADMINPASS',		#管理者用パスワード
	'IMAGE_URL',		#イメージディレクトリの URL
	'AUTHFLAG',		#アクセス制限機能
	'PASSWORD',		#パスワード
	'URL2PATH_FLAG',	#URLマッピング機能
	'URL2PATH_URL',		#URLマッピング（URL）
	'URL2PATH_PATH',	#URLマッピング（パス）
	'TIMEDIFF',		#時差の調整
	'GRAPHMAXLENGTH',	#棒グラフの長さ
	'ROW',			#表示ランキング数
	'LOTATION',		#ローテーション設定
	'LOTATION_SIZE',	#ローテーションサイズ
	'LOTATION_SAVE',	#過去ログ保存機能
	'LOTATION_SAVE_NUM',	#過去ログ保存数
	'MY_SITE_URLs',		#リンク元除外URL
	'REJECT_HOSTS',		#ロギング除外ホスト
	'DIRECTORYINDEX',	#ディレクトリインデックス
	'URLHANDLE',		#アクセスページ URL の扱い
	'USECOOKIE',		#Cookieの利用設定
	'EXPIREDAYS',		#Cookieの有効期限
	'INTERVAL',		#セッションインターバル
	'LOCK_FLAG',		#ログファイルのロック処理
	'CIRCLE_GLAPH',		#円グラフの方式
	'IMAGE_TYPE',		#解析タグの表示画像形式
	'REQ_URL_CONV_1',	#アクセスページURL置換
	'REQ_URL_CONV_2'	##アクセスページURL置換
);

#処理モード
my $m = $q->param('m');
if($m =~ /[^a-zA-Z0-9]/) {
	&ErrorPrint('不正なパラメータが送信されました。');
}
#設定値を取得
my %CONF = &GetConf('./data/config.cgi');
#管理者用パスワードがセットされていれば、acc.cgiへリダイレクト
if($CONF{ADMINPASS} && $m ne 'p5') {
	&redirect('acc.cgi');
	exit;
}
#処理文岐
if($m eq 'p1') {
	&p1;
} elsif($m eq 'p2') {
	&p2;
} elsif($m eq 'p3') {
	&p3;
} elsif($m eq 'p4') {
	&p4;
} elsif($m eq 'p5') {
	&p5;
} else {
	my $error = &WriteConfData;
	if($error) {
		&print_startup_error($error);
	}
	&p1;
}
exit;


#####################################################################

sub print_html {
	my($html) = @_;
	my $content_length = length $html;
	if($ENV{'SERVER_NAME'} !~ /($FREE_SERVER_NAME)/) {
		print "Content-Length: ${content_length}\n";
	}
	print "Content-Type: text/html; charset=utf-8\n";
	print "\n";
	print $html;
	exit;
}

sub print_startup_error {
	my($error) = @_;
	my $html = &ReadTemplate('./template/setup.p0.html');
	$html =~ s/\%error\%/$error/g;
	&print_html($html);
}

sub p5 {
	my $html = &ReadTemplate('./template/setup.p5.html');
	&print_html($html);
}

sub p4 {
	my $f = $q->param('f');
	my $error;
	if($f) {
		$error = &set_admin_pass;
		unless($error) {
			&redirect("${CGI_URL}?m=p5");
			exit;
		}
	}
	my $html = &ReadTemplate('./template/setup.p4.html');
	$html =~ s/\%error\%/$error/g;
	&print_html($html);
}
sub p3 {
	my $f = $q->param('f');
	my $error;
	if($f) {
		$error = &set_logon_pass;
		unless($error) {
			&redirect("${CGI_URL}?m=p4");
			exit;
		}
	}
	my $html = &ReadTemplate('./template/setup.p3.html');
	$html =~ s/\%error\%/$error/g;
	&print_html($html);
}

sub p2 {
	my $result;
	my $f = $q->param('f');
	if($f) {
		my @errs = &shinan;
		if(@errs) {
			my @tmp;
			for my $e (@errs) {
				$e = "<div class=\"serror\">${e}</div>";
				push(@tmp, $e);
			}
			$result = join("\n", @tmp);
			$result = "<div class=\"serrorbox\">${result}</div>";
		} else {
			$result .= "<div>診断結果は良好です。「次へ」ボタンを押してセットアップを進めてください。</div>";
			$result .= "<form action=\"${CGI_URL}\" method=\"post\">\n";
			$result .= "<input type=\"hidden\" name=\"m\" value=\"p3\" />\n";
			$result .= "<input type=\"submit\" name=\"b1\" value=\"　次　へ　\" />\n";
			$result .= "</form>";
		}
	}
	my $html = &ReadTemplate('./template/setup.p2.html');
	$html =~ s/\%result\%/$result/g;
	&print_html($html);
}

sub p1 {
	my $IMAGE_URL = $q->param('IMAGE_URL');
	if($IMAGE_URL eq '') {
		$IMAGE_URL = $CONF{IMAGE_URL};
	}
	my $error;
	if($IMAGE_URL =~ /[^\w\d\-\_\.\&\;\?\~\/\:]/) {
		$error = 'イメージディレクトリURLに不適切な文字が含まれています。';
	} else {
		$IMAGE_URL =~ s/\/$//;
		$CONF{IMAGE_URL} = $IMAGE_URL;
		$error = &WriteConfData;
	}
	my $html = &ReadTemplate('./template/setup.p1.html');
	$html =~ s/\%error\%/$error/g;
	my $IMAGE_URL_ENC = &SecureHtml($IMAGE_URL);
	$html =~ s/\%IMAGE_URL\%/$IMAGE_URL_ENC/g;
	&print_html($html);
}

sub set_admin_pass {
	my $pass1 = $q->param('PASSWORD1');
	my $pass2 = $q->param('PASSWORD2');
	my $error;
	if($pass1 && $pass2) {
		if($pass1 eq $pass2) {
			if($pass1 =~ /[^a-zA-Z0-9\-\_]/) {
				$error = "パスワードに不正な文字が含まれています。指定できる文字は、半角の英数、ハイフン、アンダースコアです。";
			}
		} else {
			$error = "管理者パスワードの再入力が間違っています。再度注意して入力してください。";
		}
	} else {
		$error = "管理者パスワードを指定してください。";
	}
	if($error) {
		return $error;
	}
	my $enc_pass = &EncryptPasswd($pass1);

	$CONF{'ADMINPASS'} = $enc_pass;
	&WriteConfData;
	return '';
}

sub set_logon_pass {
	my $pass1 = $q->param('PASSWORD1');
	my $pass2 = $q->param('PASSWORD2');
	my $error;
	if($pass1 && $pass2) {
		if($pass1 eq $pass2) {
			if($pass1 =~ /[^a-zA-Z0-9\-\_]/) {
				$error = "パスワードに不正な文字が含まれています。指定できる文字は、半角の英数、ハイフン、アンダースコアです。";
			}
		} else {
			$error = "ログオンパスワードの再入力が間違っています。再度注意して入力してください。";
		}
	} else {
		$error = "ログオンパスワードを指定してください。";
	}
	if($error) {
		return $error;
	}
	$CONF{'PASSWORD'} = $pass1;
	&WriteConfData;
	return '';
}

sub EncryptPasswd {
	my($pass)=@_;
	my(@salt_set)=('a'..'z','A'..'Z','0'..'9','.','/');
	srand;
	my($seed1) = int(rand(64));
	my($seed2) = int(rand(64));
	my($salt) = $salt_set[$seed1] . $salt_set[$seed2];
	return crypt($pass,$salt);
}

sub shinan {
	my $ex_uid = $>;
	my @stats = stat("./setup.cgi");
	my $owner_uid = $stats[4];
	my $executer;
	if($ex_uid eq $owner_uid) {
		$executer = 'owner';
	} else {
		$executer = 'other';
	}
	my $rc = &GetReturnCode("./setup.cgi");
	my $perl_path = &GetPerlPath("./setup.cgi");
	my $permission = sprintf("%o",(stat("./setup.cgi"))[2] & 0777);
	my @errs;
	if( my $e = &ExistCheck('./acclog.cgi') ) {
		push(@errs, $e);
	}
	if( my $e = &ReturnCodeCheck('./acclog.cgi', $rc) ) {
		push(@errs, $e);
	}
	if( my $e = &PerlPathCheck('./acclog.cgi', $perl_path) ) {
		push(@errs, $e);
	}
	if( my $e = &PermissionCheck('./acclog.cgi', $executer, $permission) ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./acc.cgi') ) {
		push(@errs, $e);
	}
	if( my $e = &ReturnCodeCheck('./acc.cgi', $rc) ) {
		push(@errs, $e);
	}
	if( my $e = &PerlPathCheck('./acc.cgi', $perl_path) ) {
		push(@errs, $e);
	}
	if( my $e = &PermissionCheck('./acc.cgi', $executer, $permission) ) {
		push(@errs, $e);
	}

	if( my $e = &ExistCheck('./admin.cgi') ) {
		push(@errs, $e);
	}
	if( my $e = &ReturnCodeCheck('./admin.cgi', $rc) ) {
		push(@errs, $e);
	}
	if( my $e = &PerlPathCheck('./admin.cgi', $perl_path) ) {
		push(@errs, $e);
	}
	if( my $e = &PermissionCheck('./admin.cgi', $executer, $permission) ) {
		push(@errs, $e);
	}

	if( my $e = &ExistCheck('./acclogo.gif') ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./acclogo.png') ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./acclogo.jpg') ) {
		push(@errs, $e);
	}
	if( my $e = &DirExistCheck('./logs') ) {
		push(@errs, $e);
	}
	if( my $e = &DirWriteCheck('./logs') ) {
		push(@errs, $e);
	}
	if( my $e = &DirExistCheck('./session') ) {
		push(@errs, $e);
	}
	if( my $e = &DirWriteCheck('./session') ) {
		push(@errs, $e);
	}
	if( my $e = &DirExistCheck('./data') ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./data/config.cgi') ) {
		push(@errs, $e);
	}
	if( my $e = &ReturnCodeCheck('./data/config.cgi', $rc) ) {
		push(@errs, $e);
	}
	if( my $e = &WriteCheck('./data/config.cgi') ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./data/country.dat') ) {
		push(@errs, $e);
	}
	if( my $e = &ReturnCodeCheck('./data/country.dat', $rc) ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./data/help.dat') ) {
		push(@errs, $e);
	}
	if( my $e = &ReturnCodeCheck('./data/help.dat', $rc) ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./data/language.dat') ) {
		push(@errs, $e);
	}
	if( my $e = &ReturnCodeCheck('./data/language.dat', $rc) ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./data/organization.dat') ) {
		push(@errs, $e);
	}
	if( my $e = &ReturnCodeCheck('./data/organization.dat', $rc) ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./data/pref.dat') ) {
		push(@errs, $e);
	}
	if( my $e = &ReturnCodeCheck('./data/pref.dat', $rc) ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./data/site.dat') ) {
		push(@errs, $e);
	}
	if( my $e = &ReturnCodeCheck('./data/site.dat', $rc) ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./data/ipaddr.dat') ) {
		push(@errs, $e);
	}
	if( my $e = &ReturnCodeCheck('./data/ipaddr.dat', $rc) ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./data/title.dat') ) {
		push(@errs, $e);
	}
	if( my $e = &ReturnCodeCheck('./data/title.dat', $rc) ) {
		push(@errs, $e);
	}
	if( my $e = &DirExistCheck('./lib') ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./lib/Jcode.pm') ) {
		push(@errs, $e);
	}
	if( my $e = &ReturnCodeCheck('./lib/Jcode.pm', $rc) ) {
		push(@errs, $e);
	}
	if( my $e = &DirExistCheck('./lib/Jcode') ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./lib/CGI.pm') ) {
		push(@errs, $e);
	}
	if( my $e = &ReturnCodeCheck('./lib/CGI.pm', $rc) ) {
		push(@errs, $e);
	}
	if( my $e = &DirExistCheck('./lib/CGI') ) {
		push(@errs, $e);
	}
	if( my $e = &DirExistCheck('./lib/File') ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./lib/File/Spec.pm') ) {
		push(@errs, $e);
	}
	if( my $e = &ReturnCodeCheck('./lib/File/Spec.pm', $rc) ) {
		push(@errs, $e);
	}
	if( my $e = &DirExistCheck('./lib/File/Spec') ) {
		push(@errs, $e);
	}
	if( my $e = &DirExistCheck('./lib/FCC/Apache') ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./lib/FCC/Apache/Session.pm') ) {
		push(@errs, $e);
	}
	if( my $e = &ReturnCodeCheck('./lib/FCC/Apache/Session.pm', $rc) ) {
		push(@errs, $e);
	}
	if( my $e = &DirExistCheck('./lib/Digest/Perl') ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./lib/Digest/Perl/MD5.pm') ) {
		push(@errs, $e);
	}
	if( my $e = &ReturnCodeCheck('./lib/Digest/Perl/MD5.pm', $rc) ) {
		push(@errs, $e);
	}

	if( my $e = &DirExistCheck('./template') ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./template/admin.auth.html') ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./template/admin.changepass.html') ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./template/admin.changepass_complete.html') ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./template/admin.complete.html') ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./template/admin.conf.html') ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./template/admin.help.html') ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./template/admin.loginfo.html') ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./template/admin.restrict1.html') ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./template/admin.restrict2.html') ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./template/admin.restrict_complete.html') ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./template/admin.setpass.html') ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./template/auth_error.html') ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./template/loginfo.html') ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./template/logoff.html') ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./template/logon.html') ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./template/mainframe.html') ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./template/menuframe.html') ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./template/result.html') ) {
		push(@errs, $e);
	}
	if( my $e = &ExistCheck('./template/search.html') ) {
		push(@errs, $e);
	}
	return @errs;
}

sub PermissionCheck {
	my($file, , $executer, $permission) = @_;
	unless(-e $file) { return ''; }
	my $permission2 = sprintf("%o",(stat("$file"))[2] & 0777);
	my $err_str;
	my $err_flag = 0;
	if($executer eq 'owner') {
		unless($permission2 =~ /^(7|5)/) {
			unless($^O =~ /MSWin32/i) {
				my $p = $permission;
				if( chmod(oct($permission), $file) ) { return ''; }
			}
			return "$file に実行権限がありません。パーミッションを $permission に変更してください。";
		}
	} elsif($executer eq 'other') {
		unless($permission2 =~ /(7|5)$/) {
			return "$fileに実行権限がありません。パーミッションを $permission に変更してください。";
		}
	}
	return '';
}

sub PerlPathCheck {
	my($file, $perl_path) = @_;
	unless(-e $file) { return ''; }
	my $perl_path2 = &GetPerlPath("$file");
	if($perl_path2 ne $perl_path) {
		if( my $err = &RewritePerlPath($file, $perl_path) ) {
			return "$file の Perl パスが正しく設定されていません。$file の 1 行目を「$perl_path」に書き換えてください。${err}";
		} else {
			return '';
		}
	}
	return '';
}

sub RewritePerlPath {
	my($file, $perl_path) = @_;
	unless(-e $file) { return $!; }
	my $data = &ReadFile($file);
	my @lines = split(/\n/, $data);
	if($lines[0] =~ /^\#\!/) {
		$lines[0] = $perl_path;
	}
	$data = join("\n", @lines);
	open(FILE, ">${file}") || return "${file} のPerlパス書き換えに失敗しました。: $!";
	binmode(FILE);
	print FILE $data;
	close(FILE);
	return '';
}

sub ReadFile {
	my($file) = @_;
	my $size = -s $file;
	if(!open(IN, "$file")) {
		&ErrorPrint("$file をオープンできませんでした。 : $!");
	}
	binmode(IN);
	my $filestr;
	sysread(IN, $filestr, $size);
	close(IN);
	return $filestr;
}

sub WriteCheck {
	my($file) = @_;
	if(open(FILE, ">>$file")) {
		close(FILE);
	} else {
		return "$file のパーミッションが正しくありません。606 もしくは 666 に変更してください。";
	}
	return '';
}

sub DirWriteCheck {
	my($dir) = @_;
	my $test_file = "${dir}/check.txt";
	if(-e "$dir") {
		if(open(TEST, ">$test_file")) {
			close(TEST);
			unlink("$test_file");
		} else {
			return "ディレクトリ $dir のパーミッションが正しくありません。707 もしくは 777 に変更してください。";
		}
	} else {
		return "ディレクトリ $dir がありません。。";
	}
	return '';
}

sub DirExistCheck {
	my($dir) = @_;
	if(opendir(DIR, "$dir")) {
		closedir(DIR);
	} else {
		return "ディレクトリ $dir がありません。サーバ上に $dir を作成してください。";
	}
	return '';
}

sub ExistCheck {
	my($file) = @_;
	unless(-e "$file") {
		return "$file がありません。サーバに $file を アップロードしてください。";
	}
	return '';
}

sub ReturnCodeCheck {
	my($file, $rc) = @_;
	unless(-e $file) { return ''; }
	my $rc2 = &GetReturnCode($file);
	if($rc2 ne $rc) {
		return "$file の改行コードが正しくありません。${file} の改行コードを${rc}に変換した上で、上書きアップロードしてください。";
	}
	return '';
}

sub GetReturnCode {
	my($file) = @_;
	my $size = -s "$file";
	my $str;
	if(open(FILE, "$file")) {
		sysread(FILE, $str, $size);
		close(FILE);
	} else {
		return '';
	}
	my $return_code;
	if($str =~ /\x0D\x0A/) {
		$return_code = 'CRLF';
	} elsif($str =~ /\x0D/) {
		$return_code = 'CR';
	} elsif($str =~ /\x0A/) {
		$return_code = 'LF';
	}
	return $return_code;
}

sub GetPerlPath {
	my($file) = @_;
	if(open(FILE, "$file")) {
		my @lines = <FILE>;
		my $perl_path = shift @lines;
		chop $perl_path;
		close(FILE);
		return $perl_path;
	} else {
		return '';
	}
}

sub WriteConfData {
	my $err;
	$err .= "<div>\n";
	$err .= "設定ファイル <tt>$CONF_DATA</tt> をオープンできませんでした。: $!<br />\n";
	$err .= "以下の点をご確認ください。<br />\n";
	$err .= "<ul>\n";
	$err .= "  <li>ディレクトリ <tt>data</tt> のパーミッションを 707 もしくは 777 に変更してみてください。</li>\n";
	$err .= "  <li>ディレクトリ <tt>data</tt> 内の <tt>config.cgi</tt> のパーミッションを 606 もしくは 666 に変更してみてください。</tt>\n";
	$err .= "</ul>\n";
	$err .= "</div>";
	open(CONF, ">$CONF_DATA") || return $err;
	my $key;
	for $key (@CONF_KEYS) {
		print CONF "$key=$CONF{$key}\n";
	}
	close(CONF);
	return '';
}

sub ReadTemplate {
	my($file) = @_;
	unless(-e $file) {
		&ErrorPrint("テンプレートファイル $file がありません。: $!");
	}
	my $size = -s $file;
	if(!open(FILE, "$file")) {
		&ErrorPrint("テンプレートファイル <tt>$file</tt> をオープンできませんでした。 : $!");
		exit;
	}
	binmode(FILE);
	my $html;
	sysread(FILE, $html, $size);
	close(FILE);
	$html =~ s/\%COPYRIGHT\%/$COPYRIGHT/g;
	$html =~ s/\%COPYRIGHT2\%/$COPYRIGHT2/g;
	$html =~ s/\%CGI_URL\%/$CGI_URL/g;
	$html =~ s/\%IMAGE_URL\%/$CONF{IMAGE_URL}/g;
	return $html;
}

sub GetConf {
	my($file) = @_;
	my %data = ();
	open(FILE, "$file") || &ErrorPrint("設定ファイル <tt>$file</tt> をオープンできませんでした。: $!");
	while(<FILE>) {
		chop;
		if(/^([a-zA-Z0-9\_\-]+)\=(.+)$/) {
			my $key = $1;
			my $value = $2;
			unless($key) {next;}
			$key =~ s/^[\s\t]*//;
			$key =~ s/[\s\t]*$//;
			$value =~ s/^[\s\t]*//;
			$value =~ s/[\s\t]*$//;
			$data{$key} = $value;
		}
	}
	close(FILE);
	return %data;
}

sub SecureHtml {
	my($html) = @_;
	$html =~ s/\&amp;/\&/g;
	$html =~ s/\&/&amp;/g;
	$html =~ s/\"/&quot;/g;
	$html =~ s/</&lt;/g;
	$html =~ s/>/&gt;/g;
	return $html;
}

sub ErrorPrint {
	my($message) = @_;
	my $html= <<"EOF";
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="ja" xml:lang="ja" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Language" content="ja" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>${COPYRIGHT}</title>
<link href="$CONF{IMAGE_URL}/default.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div>${message}</div>
</body></html>
EOF
	&print_html($html);
}

sub redirect {
	my($url) = @_;
	if($ENV{'SERVER_NAME'} !~ /($FREE_SERVER_NAME)/) {
		print "Location: ${url}\n\n";
		exit;
	} else {
		my $html= <<"EOF";
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="ja" xml:lang="ja" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Language" content="ja" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="refresh" content="0;URL=${url}" />
<title>${COPYRIGHT}</title>
<link href="$CONF{IMAGE_URL}/default.css" type="text/css" rel="stylesheet" />
</head>
<body>
<div>転送されない場合は、<a href="${url}">こちら</a>をクリックしてください。</div>
</body></html>
EOF
		&print_html($html);
	}
}
