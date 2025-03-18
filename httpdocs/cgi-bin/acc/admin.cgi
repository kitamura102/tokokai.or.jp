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
	eval "use Net::Netmask; 1"
}
use CGI;
use CGI::Carp qw(fatalsToBrowser);
use Digest::Perl::MD5;
use FCC::Apache::Session;
my $q = new CGI;
$| = 1;
#CGIの設定
my $FREE_SERVER_NAME = '\.tok2\.com|\.infoseek\.co\.jp|\.xrea\.com';

my $COPYRIGHT = '<a href="http://www.futomi.com/" target="_blank">futomi\'s CGI Cafe</a>';
my $COPYRIGHT2 = "futomi's CGI Cafe - 高機能アクセス解析CGI Professional版";

my $RESTRICT_COOKIE_NAME = 'accrestrict';
my $CONF_DATA = './data/config.cgi';

my @CONF_KEYS = (
	'ADMINPASS',		#管理者用パスワード
	'IMAGE_URL',		#イメージディレクトリの URL
	'AUTHFLAG',			#アクセス制限機能
	'PASSWORD',			#パスワード
	'URL2PATH_FLAG',	#URLマッピング機能
	'URL2PATH_URL',		#URLマッピング（URL）
	'URL2PATH_PATH',	#URLマッピング（パス）
	'TIMEDIFF',			#時差の調整
	'GRAPHMAXLENGTH',	#棒グラフの長さ
	'ROW',				#表示ランキング数
	'LOTATION',			#ローテーション設定
	'LOTATION_SIZE',	#ローテーションサイズ
	'LOTATION_SAVE',	#過去ログ保存機能
	'LOTATION_SAVE_NUM',	#過去ログ保存数
	'MY_SITE_URLs',		#リンク元除外URL
	'REJECT_HOSTS',		#ロギング除外ホスト
	'DIRECTORYINDEX',	#ディレクトリインデックス
	'URLHANDLE',		#アクセスページ URL の扱い
	'USECOOKIE',		#Cookieの利用設定
	'EXPIREDAYS',		#Cookieの有効期限
	'INTERVAL',			#セッションインターバル
	'LOCK_FLAG',		#ログファイルのロック処理
	'CIRCLE_GLAPH',		#円グラフの方式
	'IMAGE_TYPE',		#解析タグの表示画像形式
	'REQ_URL_CONV_1',	#アクセスページURL置換
	'REQ_URL_CONV_2',	#アクセスページURL置換
	'STIMEOUT'			#ログオンセッションタイムアウト
);

my $err_status = '<font color="RED">NG</font>';
my $ok_status = '<font color="GREEN">OK</font>';

# このCGIのURL
my $CGI_URL = 'admin.cgi';

#Cookie名称
my $COOKIE_NAME = 'ope_sid';
my $COOKIE_NAME_ADM = 'adm_sid';

#ログ格納ディレクトリ
my $LOGDIR = './logs';

#設定データ取得
my %CONF = &GetConf($CONF_DATA);
if(!$CONF{STIMEOUT}) {
	$CONF{STIMEOUT} = 3600;
}
#管理者用パスワードがセットされていなければ、setup.cgiへリダイレクト
if(! $CONF{ADMINPASS}) {
	print "Location: setup.cgi\n\n";
	exit;
}

#処理内容の取得
my $action = $q->param('action');
my $setpass_flag = $q->param('setpass');
my $logon_flag = $q->param('logon');
if($action =~ /[^a-zA-Z0-9\_\-]/) {
	&ErrorPrint('不正なアクセスです。');
}
if($setpass_flag ne '' && $setpass_flag ne '1') {
	&ErrorPrint('不正なアクセスです。');
}
if($logon_flag ne '' && $logon_flag ne '1') {
	&ErrorPrint('不正なアクセスです。');
}
#パスワードセットフラグがあればパスワード設定画面を表示
if($setpass_flag) {
 	&SetPass;
 	%CONF = &GetConf($CONF_DATA);
}
#パスワードが設定されていなければ、パスワード設定画面を表示
unless($CONF{'ADMINPASS'}) {
	&PrintAdminPassSetForm;
}

#認証
my $SID;
if($logon_flag) {
	&LogOn;
} else {
	my %cookies = &GetCookie;
	$SID = $cookies{$COOKIE_NAME_ADM};
	if($SID =~ /[^a-zA-Z0-9]/) {
		&ErrorPrint('不正なアクセスです。');
	}
	my %session_data;
	my $session;
	if($SID) {
		&SessionAuth($SID, \%session_data, $CONF{STIMEOUT});
	} else {
		&PrintAuthForm();
	}
}

#処理分岐
if($action eq 'conf') {
	&PrintConf('./template/admin.conf.html');
} elsif($action eq 'setconf') {
	&SetConf;
	&PrintComplete('設定完了しました。', 'admin.cgi?action=conf');
} elsif($action eq 'help') {
	&PrintHelp;
} elsif($action eq 'log') {
	&PrintLogInfo;
} elsif($action eq 'logdel') {
	&DeleteLogFile;
	&PrintLogInfo;
} elsif($action eq 'download') {
	&DownloadFile;
} elsif($action eq 'restrict') {
	&PrintRestrictForm;
} elsif($action eq 'restrictset') {
	&SetRestrictCookie;
} elsif($action eq 'restrictclear') {
	&ClearRestrictCookie;
} elsif($action eq 'passform') {
	&PrintPassForm;
} elsif($action eq 'passchange') {
	&PassChange;
} else {
	&ErrorPrint('不正なアクセスです。');
}

exit;

###########################################################################

sub SessionAuth {
	my($sid, $session_data_ref, $timeout) = @_;
	my $session = new FCC::Apache::Session("./session", $timeout);
	%{$session_data_ref} = $session->sessoin_auth($sid);
	unless($session_data_ref->{_sid}) {
		my $error = "すでにログオフしたか、もしくはセッションタイムアウトしました。再度ログオンしなおしてください。";
		$error .= "<hr />［<a href=\"acc.cgi\" target=\"_top\">ログオン画面へ</a>］";
		my $html = &ReadTemplate('./template/auth_error.html');
		$html =~ s/\$error\$/${error}/g;
		my $content_length = length $html;
		print &ClearCookie($COOKIE_NAME_ADM), "\n";
		print &ClearCookie($COOKIE_NAME), "\n";
		print "Content-Type: text/html; charset=utf-8\n";
		if($ENV{'SERVER_NAME'} !~ /($FREE_SERVER_NAME)/) {
			print "Content-Length: ${content_length}\n";
		}
		print "\n";
		print $html;
		exit;
	}
	if($session_data_ref->{_userid} ne 'admin') {
		&ErrorPrint("不正なアクセスです。$session_data_ref->{_userid}");
	}
	return $session;
}

sub PrintAuthForm {
	my($err_flg) = @_;
	my $html = &ReadTemplate('./template/admin.auth.html');
	my $error;
	if($err_flg) {
		$error = 'パスワードが違います。';
	}
	$html =~ s/\%error\%/$error/g;
	my $content_length = length $html;
	if($ENV{'SERVER_NAME'} !~ /($FREE_SERVER_NAME)/) {
		print "Content-Length: ${content_length}\n";
	}
	print "Content-Type: text/html; charset=utf-8\n";
	print "\n";
	print $html;
	exit;
}

sub LogOn {
	my $in_pass = $q->param('pass');
	if($in_pass eq '') {
		&PrintAuthForm(1);
	}
	my $salt;
	if($CONF{'ADMINPASS'} =~ /^\$1\$([^\$]+)\$/) {
		$salt = $1;
	} else {
		$salt = substr($CONF{'ADMINPASS'}, 0, 2);
	}
	my $pass = crypt($in_pass, $salt);
	#パスワード照合
	if($pass ne $CONF{'ADMINPASS'}) {
		&PrintAuthForm(1);
	}
	my $session = new FCC::Apache::Session("./session");
	unless($session) {
		&ErrorPrint("システムエラー");
	}
	my %session_data = ${session}->session_create('admin');
	unless($session_data{_sid}) {
		my $err = '認証に失敗しました。:' . $session->error();
		&ErrorPrint($err);
	}
	my $target_url = $CGI_URL . "?t=" . time . '&amp;action=' . $action;
	print &SetCookie($COOKIE_NAME_ADM, $session_data{_sid}), "\n";
	print "Content-Type: text/html; charset=utf-8\n";
	print "\n";
	print "<html>\n";
	print "<head>\n";
	print "<meta http-equiv=\"content-type\" content=\"text/html;charset=utf-8\">\n";
	if($ENV{'SERVER_NAME'} =~ /($FREE_SERVER_NAME)/) {
		print "<meta http-equiv=\"Set-Cookie\" content=\"${COOKIE_NAME_ADM}=$session_data{_sid};\">\n";
	}
	print "<meta http-equiv=\"refresh\" content=\"0;URL=${target_url}\">\n";
	print "<title>ログオン中...</title>\n";
	print "</head>\n";
	print "<body>\n";
	print "<p style=\"font-size:small\">ログオン中 ...</p>\n";
	print "</body></html>\n";
	exit;
}

sub PassChange {
	my $pass1 = $q->param('PASSWORD1');
	my $pass2 = $q->param('PASSWORD2');
	unless($pass1 && $pass2) {
		&ErrorPrint("パスワードを指定してください。");
	}
	unless($pass1 eq $pass2) {
		&ErrorPrint("パスワード再入力が間違っています。再度注意して入力してください。");
	}
	if($pass1 =~ /[^a-zA-Z0-9\-\_]/) {
		&ErrorPrint("パスワードに不正な文字が含まれています。指定できる文字は、半角の英数、ハイフン、アンダースコアです。");
	}
	my $enc_pass = &EncryptPasswd($pass1);
	$CONF{'ADMINPASS'} = "$enc_pass";
	&WriteConfData;
	my $html = &ReadTemplate('./template/admin.changepass_complete.html');
	if($ENV{'SERVER_NAME'} =~ /($FREE_SERVER_NAME)/) {
		$html =~ s/\%COOKIE\%/<meta http-equiv='Set-Cookie' content='${COOKIE_NAME_ADM}=${SID};' \/>/;
	} else {
		$html =~ s/\%COOKIE\%//;
		my $CookieHeaderString = &SetCookie($COOKIE_NAME_ADM, $SID);
		print "P3P: CP=\"NOI TAIa\"\n";
		print "$CookieHeaderString\n";
	}
	my $content_length = length $html;
	if($ENV{'SERVER_NAME'} !~ /($FREE_SERVER_NAME)/) {
		print "Content-Length: ${content_length}\n";
	}
	print "Content-Type: text/html; charset=utf-8\n";
	print "\n";
	print $html;
	exit;
}

sub PrintPassForm {
	my $html = &ReadTemplate('./template/admin.changepass.html');
	if($ENV{'SERVER_NAME'} =~ /($FREE_SERVER_NAME)/) {
		$html =~ s/\%COOKIE\%/<meta http-equiv='Set-Cookie' content='${COOKIE_NAME_ADM}=${SID};' \/>/;
	} else {
		$html =~ s/\%COOKIE\%//;
		my $CookieHeaderString = &SetCookie($COOKIE_NAME_ADM, $SID);
		print "P3P: CP=\"NOI TAIa\"\n";
		print "$CookieHeaderString\n";
	}
	my $content_length = length $html;
	if($ENV{'SERVER_NAME'} !~ /($FREE_SERVER_NAME)/) {
		print "Content-Length: ${content_length}\n";
	}
	print "Content-Type: text/html; charset=utf-8\n";
	print "\n";
	print $html;
	exit;
}

sub ClearRestrictCookie {
	my $html = &ReadTemplate('./template/admin.restrict_complete.html');
	my $message = '設定を解除しました。';
	$html =~ s/\%message\%/$message/g;
	if($ENV{'SERVER_NAME'} =~ /($FREE_SERVER_NAME)/) {
		$html =~ s/\%COOKIE\%/<meta http-equiv='Set-Cookie' content='$RESTRICT_COOKIE_NAME=clear;expires=Thu, 01-Jan-1970 00:00:00 GMT;' \/>/;
	} else {
		$html =~ s/\%COOKIE\%//;
		my $CookieHeaderString = &ClearCookie($RESTRICT_COOKIE_NAME);
		print "P3P: CP=\"NOI TAIa\"\n";
		print "$CookieHeaderString\n";
	}
	my $content_length = length $html;
	if($ENV{'SERVER_NAME'} !~ /($FREE_SERVER_NAME)/) {
		print "Content-Length: ${content_length}\n";
	}
	print "Content-Type: text/html; charset=utf-8\n";
	print "\n";
	print $html;
	exit;
}

sub SetRestrictCookie {
	my $html = &ReadTemplate('./template/admin.restrict_complete.html');
	my $message = '設定完了しました。';
	$html =~ s/\%message\%/$message/g;
	my $expire = time + 315360000;	#10年後
	my $CookieHeaderString = &SetCookie($RESTRICT_COOKIE_NAME, '1', $expire);
	if($ENV{'SERVER_NAME'} =~ /($FREE_SERVER_NAME)/) {
		my $content_str = $CookieHeaderString;
		$content_str =~ s/^Set-Cookie: //;
		$html =~ s/\%COOKIE\%/<meta http-equiv='Set-Cookie' content='$content_str' \/>/;
	} else {
		$html =~ s/\%COOKIE\%//;
		print "P3P: CP=\"NOI TAIa\"\n";
		print "$CookieHeaderString\n";
	}
	my $content_length = length $html;
	if($ENV{'SERVER_NAME'} !~ /($FREE_SERVER_NAME)/) {
		print "Content-Length: ${content_length}\n";
	}
	print "Content-Type: text/html; charset=utf-8\n";
	print "\n";
	print $html;
	exit;
}

sub PrintRestrictForm {
	my %cookie = &GetCookie;
	my $template;
	if($cookie{$RESTRICT_COOKIE_NAME}) {
		$template = './template/admin.restrict2.html';
	} else {
		$template = './template/admin.restrict1.html';
	}
	my $html = &ReadTemplate($template);
	if($ENV{'SERVER_NAME'} =~ /($FREE_SERVER_NAME)/) {
		$html =~ s/\%COOKIE\%/<meta http-equiv='Set-Cookie' content='${COOKIE_NAME_ADM}=${SID};' \/>/;
	} else {
		$html =~ s/\%COOKIE\%//;
		my $CookieHeaderString = &SetCookie($COOKIE_NAME_ADM, $SID);
		print "P3P: CP=\"NOI TAIa\"\n";
		print "$CookieHeaderString\n";
	}
	my $content_length = length $html;
	print "Content-Type: text/html; charset=utf-8\n";
	if($ENV{'SERVER_NAME'} !~ /($FREE_SERVER_NAME)/) {
		print "Content-Length: ${content_length}\n";
	}
	print "\n";
	print $html;
	exit;
}

sub PrintLogInfo {
	opendir(DIR, "$LOGDIR") || &ErrorPrint("ログ格納ディレクトリ「$LOGDIR」をオープンできませんでした。");
	my @files = readdir(DIR);
	closedir(DIR);
	my @logs = ();
	for my $file (@files) {
		if($file =~ /^access_log/) {
			push(@logs, $file);
		}
	}
	my(%size_list, %mtime_list);
	for my $file (@logs) {
		my @stat = stat("$LOGDIR/$file");
		$size_list{$file} = $stat[7];
		$mtime_list{$file} = $stat[9];
	}
	my $list;
	foreach my $file (ValueSort(\%mtime_list)) {
		my $date = &ConvEpoc2Date($mtime_list{$file});
		my $dsp_size = &CommaFormat($size_list{$file});
		$list .= "  <tr>\n";
		$list .= "    <td>$file</td>\n";	#ファイル名
		$list .= "    <td class=\"right\">$dsp_size byte</td>\n";	#サイズ
		$list .= "    <td>$date</td>\n";	#最終更新日時
		$list .= "    <td class=\"center\"><a href=\"admin.cgi?action=download&amp;file=$file\">Download</a></td>\n";	#ダウンロード
		$list .= "    <td class=\"center\"><a href=\"javascript:delConfirm('$file')\">削除</a></td>\n";	#削除
		$list .= "  </tr>\n";
	}
	my $html = &ReadTemplate('./template/admin.loginfo.html');
	$html =~ s/\%LIST\%/$list/;
	if($ENV{'SERVER_NAME'} =~ /($FREE_SERVER_NAME)/) {
		$html =~ s/\%COOKIE\%/<meta http-equiv='Set-Cookie' CONTENT='${COOKIE_NAME_ADM}=${SID};' \/>/;
	} else {
		$html =~ s/\%COOKIE\%//;
		my $CookieHeaderString = &SetCookie($COOKIE_NAME_ADM, $SID);
		print "P3P: CP=\"NOI TAIa\"\n";
		print "$CookieHeaderString\n";
	}
	my $content_length = length $html;
	if($ENV{'SERVER_NAME'} !~ /($FREE_SERVER_NAME)/) {
		print "Content-Length: ${content_length}\n";
	}
	print "Content-Type: text/html; charset=utf-8\n";
	print "\n";
	print $html;
	exit;
}

sub PrintAdminPassSetForm {
	my $html = &ReadTemplate('./template/admin.setpass.html');
	$html =~ s/\%ACTION\%/$action/g;
	my $content_length = length $html;
	if($ENV{'SERVER_NAME'} !~ /($FREE_SERVER_NAME)/) {
		print "Content-Length: ${content_length}\n";
	}
	print "Content-Type: text/html; charset=utf-8\n";
	print "\n";
	print $html;
	exit;
}

sub DownloadFile {
	my $file = $q->param('file');
	if($file eq '') {
		&ErrorPrint('ログファイルが選択されていません。');
	} elsif($file =~ /[^a-zA-Z0-9\.\_\-]/) {
		&ErrorPrint('不正なパラメーターが送信されました。');
	}
	my $log = "$LOGDIR/$file";
	my $ua = $ENV{'HTTP_USER_AGENT'};
	my $rc = "\x0A";	#LF
	if($ua =~ /Windows|Win32|Win 9x/) {
		$rc = "\x0D\x0A";	#CRLF
	} elsif($ua =~ /Macintosh|Mac_/) {
		$rc = "\x0D";	#CR
	}
	my $size = -s $log;
	open(LOG, "$log") || &ErrorPrint("ログファイル <tt>$log</tt> をオープンできませんでした。 : $!");
	my $contents;
	sysread(LOG, $contents, $size);
	close(LOG);
	$contents =~ s/\n/$rc/g;
	print "Content-Type: application/octet-stream\n";
	print "Content-Disposition: attachment; filename=$file\n";
	if($ENV{'SERVER_NAME'} !~ /($FREE_SERVER_NAME)/) {
		my $clen = length $contents;
		print "Content-length: ${clen}\n";
	}
	print "\n";
	print "$contents";
	exit;
}

sub DeleteLogFile {
	my $file = $q->param('file');
	if($file eq '') {
		&ErrorPrint('ログファイルが選択されていません。');
	} elsif($file =~ /[^a-zA-Z0-9\.\_\-]/) {
		&ErrorPrint('不正なパラメーターが送信されました。');
	}
	my $log = "$LOGDIR/$file";
	unless(unlink($log)) {
		&ErrorPrint("ログファイル $log を削除できませんでした。 : $!");
	}
}

sub PrintHelp {
	my $item = $q->param('item');
	if($item =~ /[^a-zA-Z0-9\_\-]/) {
		&ErrorPrint('不正なパラメータが送信されました。');
	}
	$item =~ s/^help_//;
	my $f = './data/help.dat';
	my $size = -s $f;
	if(!open(IN, "$f")) {
		&ErrorPrint("ヘルプファイル <tt>${f}</tt> をオープンできませんでした。 : $!");
		exit;
	}
	binmode(IN);
	my $helpstr;
	sysread(IN, $helpstr, $size);
	close(IN);
	$helpstr = &UnifyReturnCode($helpstr);
	my @help_parts = split(/<-- delimiter -->/, $helpstr);
	my %help = ();
	my %itemname = ();
	my $part;
	for $part (@help_parts) {
		$part =~ s/^(\n|\s)+//;
		my @lines = split(/\n/, $part);
		my $key = shift(@lines);
		my $keyname = shift(@lines);
		my $help_str = join("\n", @lines);
		$help{$key} = $help_str;
		$itemname{$key} = $keyname;
	}
	my $html = &ReadTemplate('./template/admin.help.html');
	$html =~ s/\%HELP\%/$help{$item}/g;
	$html =~ s/\%ITEM\%/$itemname{$item}/g;
	my $content_length = length $html;
	if($ENV{'SERVER_NAME'} !~ /($FREE_SERVER_NAME)/) {
		print "Content-Length: ${content_length}\n";
	}
	print "Content-Type: text/html; charset=utf-8\n";
	print "\n";
	print $html;
	exit;
}


sub SetConf {
	my $IMAGE_URL = $q->param('IMAGE_URL');
	if($IMAGE_URL) {
		if($IMAGE_URL =~ /\/$/) {
			&ErrorPrint("イメージディレクトリの URL の最後にスラッシュは入れないで下さい。");
		}
		$CONF{'IMAGE_URL'} = $IMAGE_URL;
	} else {
		&ErrorPrint("イメージディレクトリの URL を指定して下さい。");
	}

	my $AUTHFLAG = $q->param('AUTHFLAG');
	my $PASSWORD = $q->param('PASSWORD');
	$CONF{'AUTHFLAG'} = $AUTHFLAG;
	if($AUTHFLAG) {
		if($PASSWORD eq "") {
			&ErrorPrint("パスワードを指定して下さい。");
		} else {
			if($PASSWORD =~ /[^a-zA-Z0-9\-\_]/) {
				&ErrorPrint("パスワードは、半角英数字、半角ハイフン、半角アンダースコアのみで指定して下さい。");
			}
			$CONF{'PASSWORD'} = $PASSWORD;
		}
	}

	my $URL2PATH_FLAG = $q->param('URL2PATH_FLAG');
	my $URL2PATH_URL = $q->param('URL2PATH_URL');
	my $URL2PATH_PATH = $q->param('URL2PATH_PATH');
	$CONF{'URL2PATH_FLAG'} = $URL2PATH_FLAG;
	if($URL2PATH_FLAG) {
		if($URL2PATH_URL eq "") {
			&ErrorPrint("URL マッピング機能\を使う場合には、マッピング元となる URL を指定して下さい。");
		}
		if($URL2PATH_PATH eq "") {
			&ErrorPrint("URL マッピング機能\を使う場合には、マッピング先となる パス を指定して下さい。");
		}
		unless($URL2PATH_URL =~ /\/$/) {
			&ErrorPrint("URL マッピングの指定では、最後にスラッシュを入れてください。");
		}
		unless($URL2PATH_PATH =~ /\/$/) {
			&ErrorPrint("URL マッピングの指定では、最後にスラッシュを入れてください。");
		}
		if(opendir(DIR, "$URL2PATH_PATH")) {
			closedir(DIR);
		} else {
			&ErrorPrint("URL マッピングの設定において、マッピング先のパスは存在しません。: $!");
		}
		$CONF{'URL2PATH_URL'} = $URL2PATH_URL;
		$CONF{'URL2PATH_PATH'} = $URL2PATH_PATH;
	}

	my $TIMEDIFF = $q->param('TIMEDIFF');
	unless($TIMEDIFF =~ /^\-*[0-9]+$/) {
		&ErrorPrint("時差の調整は、半角数字のみで指定して下さい。");
	}
	$TIMEDIFF =~ s/^0*//;
	if($TIMEDIFF eq "") {
		$TIMEDIFF = 0;
	}
	$CONF{'TIMEDIFF'} = $TIMEDIFF;

	my $GRAPHMAXLENGTH = $q->param('GRAPHMAXLENGTH');
	if($GRAPHMAXLENGTH =~ /[^0-9]/) {
		&ErrorPrint("棒グラフの長さは、半角数字のみで指定して下さい。");
	}
	$GRAPHMAXLENGTH =~ s/^0*//;
	if($GRAPHMAXLENGTH eq "") {
		$GRAPHMAXLENGTH = 0;
	}
	$CONF{'GRAPHMAXLENGTH'} = $GRAPHMAXLENGTH;

	my $ROW = $q->param('ROW');
	if($ROW =~ /[^0-9]/) {
		&ErrorPrint("ランキング表示数は、半角数字のみで指定して下さい。");
	}
	$ROW =~ s/^0*//;
	if($ROW eq "") {
		$ROW = 0;
	}
	$CONF{'ROW'} = $ROW;

	my $LOTATION = $q->param('LOTATION');
	my $LOTATION_SIZE = $q->param('LOTATION_SIZE');
	my $LOTATION_SAVE = $q->param('LOTATION_SAVE');
	my $LOTATION_SAVE_NUM = $q->param('LOTATION_SAVE_NUM');
	$CONF{'LOTATION'} = $LOTATION;
	if($LOTATION eq "1") {
		my $LOTATION_SIZE = $q->param('LOTATION_SIZE');
		if($LOTATION_SIZE eq "") {
			&ErrorPrint("ローテーションサイズを指定して下さい。");
		}
		if($LOTATION_SIZE =~ /[^0-9]/) {
			&ErrorPrint("ローテーションサイズは、半角数字のみで指定して下さい。");
		}
		$LOTATION_SIZE =~ s/^0*//;
		if($LOTATION_SIZE <= 0) {
			&ErrorPrint("ローテーションサイズに 0 以下は指定できません。");
		}
		$CONF{'LOTATION_SIZE'} = $LOTATION_SIZE;
	}
	if($LOTATION > 0) {
		if($LOTATION_SAVE !~ /^(0|1|2)$/) {
			&ErrorPrint('不正な値が送信されました。(LOTATION_SAVE)');
		}
		$CONF{'LOTATION_SAVE'} = $LOTATION_SAVE;
		if($LOTATION_SAVE == 2) {
			if($LOTATION_SAVE_NUM eq '') {
				&ErrorPrint('過去ログ保存で「指定個数分だけ保存する」を選択した場合は、保存過去ログファイル数を指定して下さい。');
			} elsif($LOTATION_SAVE_NUM =~ /[^\d]/) {
				&ErrorPrint('保存過去ログファイル数は半角数字で指定してください。');
			} elsif($LOTATION_SAVE_NUM == 0) {
				&ErrorPrint('保存過去ログファイル数に0を指定することはできません。');
			}
			$CONF{'LOTATION_SAVE_NUM'} = $LOTATION_SAVE_NUM;
		}
	}

	my $MY_SITE_URLs = $q->param('MY_SITE_URLs');
	$MY_SITE_URLs = &UnifyReturnCode($MY_SITE_URLs);
	my @sites = split(/\n/, $MY_SITE_URLs);
	my $site;
	for $site (@sites) {
		unless($site =~ /https*:\/\//) {
			&ErrorPrint("リンク元解析 除外 URL の指定は、<tt>http://</tt> から指定して下さい。 : <tt>$site</tt>");
		}
		if($site =~ /[^a-zA-Z0-9\-\_\.\%\:\/\~\&\=\?]/) {
			&ErrorPrint("除外ホストの指定で、不適切な文字が含まれています。 : <tt>$site</tt>");
		}
	}
	$MY_SITE_URLs =~ s/\n/,/g;
	$CONF{'MY_SITE_URLs'} = $MY_SITE_URLs;

	my $REJECT_HOSTS = $q->param('REJECT_HOSTS');
	$REJECT_HOSTS = &UnifyReturnCode($REJECT_HOSTS);
	my @hosts = split(/\n/, $REJECT_HOSTS);
	my @valid_hosts;
	for my $host (@hosts) {
		$host =~ s/\s//g;
		if($host eq "") { next; }
		if($host =~ /https*:\/\//) {
			&ErrorPrint("除外ホストの指定では、<tt>http://</tt> から指定できません。: <tt>$host</tt>");
		}
		if($host =~ /[^a-zA-Z0-9\-\_\.\/]/) {
			&ErrorPrint("除外ホストの指定で、不適切な文字が含まれています。 : <tt>$host</tt>");
		}
		if($host =~ /\//) {
			my $nm;
			eval {$nm = new2 Net::Netmask($host);};
			if($nm) {
				if($host =~ /^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\/\d{1,2}$/) {
					if($nm) {
						my $base = $nm->base();
						if($host !~ /^\Q${base}\E\//) {
							my $correct = $host;
							$correct =~ s/^[^\/]+/${base}/;
							&ErrorPrint("「除外ホストの指定」に指定したアドレスブロック '${host}' が正しくありません。恐らく '${correct}' ではないでしょうか。");
						}
					} else {
						&ErrorPrint("「除外ホストの指定」に指定したアドレスブロックが不適切です。: <tt>$host</tt>");
					}
				} else {
					&ErrorPrint("「除外ホストの指定」に指定したアドレスブロックが不適切です。: <tt>$host</tt>");
				}
			} else {
				&ErrorPrint("ご利用のサーバでは、「除外ホストの指定」にビットマスクを指定することはできません。: <tt>$host</tt>");
			}
		}
		push(@valid_hosts, $host);
	}
	$REJECT_HOSTS = join(",", @valid_hosts);
	$CONF{'REJECT_HOSTS'} = $REJECT_HOSTS;

	my $DIRECTORYINDEX = $q->param('DIRECTORYINDEX');
	$DIRECTORYINDEX = &UnifyReturnCode($DIRECTORYINDEX);
	$DIRECTORYINDEX =~ s/\n/,/g;
	$CONF{'DIRECTORYINDEX'} = $DIRECTORYINDEX;

	$CONF{'URLHANDLE'} = $q->param('URLHANDLE');

	my $USECOOKIE = $q->param('USECOOKIE');
	my $EXPIREDAYS = $q->param('EXPIREDAYS');
	$CONF{'USECOOKIE'} = $USECOOKIE;
	if($USECOOKIE) {
		if($EXPIREDAYS eq "") {
			&ErrorPrint("Cookie 有効期限を指定して下さい。");
		}
		if($EXPIREDAYS =~ /[^0-9]/) {
			&ErrorPrint("Cookie 有効期限は、半角数字のみで指定して下さい。");
		}
		$EXPIREDAYS =~ s/^0*//;
		if($EXPIREDAYS <= 0) {
			&ErrorPrint("Cookie 有効期限に 0 以下は指定できません。");
		}
		$CONF{'EXPIREDAYS'} = $EXPIREDAYS;
	}

	my $INTERVAL = $q->param('INTERVAL');
	if($INTERVAL eq "") {
		&ErrorPrint("セッションインターバルを指定して下さい。");
	}
	if($INTERVAL =~ /[^0-9]/) {
		&ErrorPrint("セッションインターバルは、半角数字のみで指定して下さい。");
	}
	$INTERVAL =~ s/^0*//;
	if($INTERVAL <= 0) {
		&ErrorPrint("セッションインターバルに 0 以下は指定できません。");
	}
	$CONF{'INTERVAL'} = $INTERVAL;
	$CONF{'LOCK_FLAG'} = $q->param('LOCK_FLAG');
	$CONF{'CIRCLE_GLAPH'} = $q->param('CIRCLE_GLAPH');
	$CONF{'IMAGE_TYPE'} = $q->param('IMAGE_TYPE');
	#アクセスページURL置換
	my $req_url_conv_1 = $q->param('REQ_URL_CONV_1');
	my $req_url_conv_2 = $q->param('REQ_URL_CONV_2');
	if($req_url_conv_1 || $req_url_conv_2) {
		if($req_url_conv_1 && $req_url_conv_2) {
			$req_url_conv_1 = &UnifyReturnCode($req_url_conv_1);
			my @arr = split(/\n+/, $req_url_conv_1);
			my @urls;
			for my $u (@arr) {
				if($u eq '') { next; }
				if($u =~ /^https*\:\/\/[a-zA-Z0-9\-\_\.]+\/$/) {
					push(@urls, $u);
				} else {
					&ErrorPrint('アクセスページURL置換の上段に指定したURLが正しくありません。');
				}
			}
			if(@urls) {
				$CONF{'REQ_URL_CONV_1'} = join(",", @urls);
			} else {
				&ErrorPrint('アクセスページURL置換の上段にURLを指定してください。');
			}
			if($req_url_conv_2 =~ /^https*\:\/\/[a-zA-Z0-9\-\_\.]+\/$/) {
				$CONF{'REQ_URL_CONV_2'} = $req_url_conv_2;
			} else {
				&ErrorPrint('アクセスページURL置換の下段に指定したURLが正しくありません。');
			}
		} else {
			&ErrorPrint('アクセスページURL置換を設定する場合は、上下の入力欄を両方とも指定して下さい。');
		}
	}
	#ログオンセッションタイムアウト
	my $STIMEOUT = $q->param('STIMEOUT');
	if($STIMEOUT ne "") {
		if($STIMEOUT =~ /[^0-9]/) {
			&ErrorPrint("ログオンセッションタイムアウトは、半角数字のみで指定して下さい。");
		} elsif($STIMEOUT < 60) {
			&ErrorPrint("ログオンセッションタイムアウトには、60秒未満をセットすることはできません。");
		}
	}
	$STIMEOUT += 0;
	if($STIMEOUT < 60) {
		$STIMEOUT = 3600;
	}
	$CONF{'STIMEOUT'} = $STIMEOUT;
	#
	&WriteConfData;
}

sub PrintConf {
	my($file) = @_;
	my $filestr = &ReadTemplate($file);
	for my $key (@CONF_KEYS) {
		if($key =~ /^(AUTHFLAG|URL2PATH_FLAG|LOTATION|LOTATION_SAVE|USECOOKIE|URLHANDLE|LOCK_FLAG|CIRCLE_GLAPH|IMAGE_TYPE)$/) {
			$filestr =~ s/\%$key$CONF{$key}\%/selected/;
			$filestr =~ s/\%$key[0-9]+\%//g;
		} elsif($key =~ /^(MY_SITE_URLs|REJECT_HOSTS|DIRECTORYINDEX|REQ_URL_CONV_1)$/) {
			my @items = split(/,/, $CONF{$key});
			my $tmp = join("\n", @items);
			$filestr =~ s/\%$key\%/$tmp/;
		} else {
			$filestr =~ s/\%$key\%/$CONF{$key}/;
		}
	}
	if($ENV{'SERVER_NAME'} =~ /($FREE_SERVER_NAME)/) {
		$filestr =~ s/\%COOKIE\%/<META HTTP-EQUIV='Set-Cookie' CONTENT='${COOKIE_NAME_ADM}=${SID};'>/;
	} else {
		$filestr =~ s/\%COOKIE\%//;
		my $CookieHeaderString = &SetCookie($COOKIE_NAME_ADM, $SID);
		print "P3P: CP=\"NOI TAIa\"\n";
		print "$CookieHeaderString\n";
	}
	my $content_length = length $filestr;
	if($ENV{'SERVER_NAME'} !~ /($FREE_SERVER_NAME)/) {
		print "Content-Length: ${content_length}\n";
	}
	print "Content-Type: text/html; charset=utf-8\n";
	print "\n";
	print $filestr;
	exit;
}

sub SetPass {
	my $pass1 = $q->param('PASSWORD1');
	my $pass2 = $q->param('PASSWORD2');
	unless($pass1 && $pass2) {
		&ErrorPrint("パスワードを指定してください。");
	}
	unless($pass1 eq $pass2) {
		&ErrorPrint("パスワード再入力が間違っています。再度注意して入力してください。");
	}
	if($pass1 =~ /[^a-zA-Z0-9\-\_]/) {
		&ErrorPrint("パスワードに不正な文字が含まれています。指定できる文字は、半角の英数、ハイフン、アンダースコアです。");
	}
	my $enc_pass = &EncryptPasswd($pass1);
	$CONF{'ADMINPASS'} = "$enc_pass";
	&WriteConfData;
}

sub PrintComplete {
	my($message, $back_link) = @_;
	my $html = &ReadTemplate('./template/admin.complete.html');
	for my $key (keys %CONF) {
		$html =~ s/\%$key\%/$CONF{$key}/g;
	}
	$html =~ s/\%BACK\%/$back_link/g;
	$html =~ s/\%MESSAGE\%/$message/g;
	$html =~ s/\%COPYRIGHT\%/$COPYRIGHT/g;
	$html =~ s/\%ACTION\%/$action/g;
	$html =~ s/\%CGI_URL\%/$CGI_URL/g;
	my $content_length = length $html;
	if($ENV{'SERVER_NAME'} !~ /($FREE_SERVER_NAME)/) {
		print "Content-Length: ${content_length}\n";
	}
	print "Content-Type: text/html; charset=utf-8\n";
	print "\n";
	print $html;
	exit;
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

sub ErrorPrint {
	my($msg) = @_;
	my $html;
	$html .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";
	$html .= '<html lang="ja" xml:lang="ja" xmlns="http://www.w3.org/1999/xhtml">' . "\n";
	$html .= '<head>' .  "\n";
	$html .= '<meta http-equiv="Content-Language" content="ja" />' . "\n";
	$html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\n";
	$html .= '<title>エラー</title>' . "\n";
	$html .= '</head>' . "\n";
	$html .= '<body>', "\n";
	$html .= "<p style=\"font-size:small;color:#aa0000;\">${msg}</p>\n";
	$html .= '<hr />', "\n";
	$html .= '<div style="font-size:small;"><a href="javascript:history.back()">戻る</a></div>', "\n";
	$html .= '</body></html>';
	print "Content-Type: text/html; charset=utf-8\n";
	print "\n";
	print $html;
	exit;
}

sub SetCookie {
	my($CookieName, $CookieValue, $ExpireTime, $Domain, $Path) = @_;
	# URLエンコード
	$CookieValue =~ s/([^\w\=\& ])/'%' . unpack("H2", $1)/eg;
	$CookieValue =~ tr/ /+/;
	my($CookieHeaderString);
	$CookieHeaderString .= "Set-Cookie: $CookieName=$CookieValue\;";
	if($ExpireTime) {
		my(@MonthString) = ('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
					'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
		my(@WeekString) = ('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
		my($sec, $min, $hour, $monthday, $month, $year, $weekday) = gmtime($ExpireTime);
		$year += 1900;
		$month = $MonthString[$month];
		if($monthday < 10) {$monthday = '0'.$monthday;}
		if($sec < 10) {$sec = '0'.$sec;}
		if($min < 10) {$min = '0'.$min;}
		if($hour < 10) {$hour = '0'.$hour;}
		my($GmtString) = "$WeekString[$weekday], $monthday-$month-$year $hour:$min:$sec GMT";
 		$CookieHeaderString .= " expires=$GmtString\;";
	}
	if($Domain) {
		$CookieHeaderString .= " domain=$Domain;";
	}
	if($Path) {
		$CookieHeaderString .= " path=$Path;";
	}
	return $CookieHeaderString;
}

sub GetCookie {
	my(@CookieList) = split(/\; /, $ENV{'HTTP_COOKIE'});
	my(%Cookie) = ();
	my($key, $CookieName, $CookieValue);
	for $key (@CookieList) {
		($CookieName, $CookieValue) = split(/=/, $key);
		$CookieValue =~ s/\+/ /g;
		$CookieValue =~ s/%([0-9a-fA-F][0-9a-fA-F])/pack("C",hex($1))/eg;
		$Cookie{$CookieName} = $CookieValue;
	}
	return %Cookie;
}

sub ClearCookie {
	my($CookieName) = @_;
	my($CookieHeaderString);
	my($ExpiresTimeString) = 'Thu, 01-Jan-1970 00:00:00 GMT';
	$CookieHeaderString .= "Set-Cookie: $CookieName=clear\; expires=$ExpiresTimeString\;";
	return $CookieHeaderString;
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

sub UnifyReturnCode {
	my($String) = @_;
	$String =~ s/\x0D\x0A/\n/g;
	$String =~ s/\x0D/\n/g;
	$String =~ s/\x0A/\n/g;
	return $String;
}

sub WriteConfData {
	my $err;
	$err .= "<table border=\"0\"><tr><td nowrap>\n";
	$err .= "設定ファイル <tt>$CONF_DATA</tt> をオープンできませんでした。: $!<br>\n";
	$err .= "以下の点をご確認ください。<br>\n";
	$err .= "<ul>\n";
	$err .= "  <li>ディレクトリ <tt>data</tt> のパーミッションを 707 もしくは 777 に変更してみてください。</li>\n";
	$err .= "  <li>ディレクトリ <tt>data</tt> 内の <tt>config.cgi</tt> のパーミッションを 606 もしくは 666 に変更してみてください。</tt>\n";
	$err .= "</ul>\n";
	$err .= "</td></tr></table>\n";
	open(CONF, ">$CONF_DATA") || &ErrorPrint("$err");
	my $key;
	for $key (@CONF_KEYS) {
		print CONF "$key=$CONF{$key}\n";
	}
	close(CONF);
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

sub CommaFormat {
	my($num) = @_;
	if($num =~ /[^0-9\.]/) {return $num;}
	my($int, $decimal) = split(/\./, $num);
	my $figure = length $int;
	my $commaformat;
	for(my $i=1;$i<=$figure;$i++) {
		my $n = substr($int, $figure-$i, 1);
		if(($i-1) % 3 == 0 && $i != 1) {
			$commaformat = "$n,$commaformat";
		} else {
			$commaformat = "$n$commaformat";
		}
	}
	if($decimal) {
		$commaformat .= "\.$decimal";
	}
	return $commaformat;
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
	$html =~ s/\%ACTION\%/$action/g;
	$html =~ s/\%IMAGE_URL\%/$CONF{IMAGE_URL}/g;
	return $html;
}

# 連想配列を値（value）でソートした連想配列を返す
sub ValueSort {
	my $x = shift;
	my %array=%$x;
	return sort {$array{$b} <=> $array{$a};} keys %array;
}

sub ConvEpoc2Date {
	my($epoc) = @_;
	my($s, $m, $h, $D, $M, $Y) = localtime($epoc + $CONF{'TIMEDIFF'}*3600);
	$Y += 1900;
	$M += 1;
	$M = sprintf("%02d", $M);
	$D = sprintf("%02d", $D);
	$h = sprintf("%02d", $h);
	$m = sprintf("%02d", $m);
	$s = sprintf("%02d", $s);
	return "$Y/$M/$D $h:$m:$s";
}


