#!/usr/bin/perl
################################################################################
# 高機能アクセス解析CGI Professional版 （解析結果表示用）
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
use Time::Local;
use CGI;
use CGI::Carp qw(fatalsToBrowser);
use Digest::Perl::MD5;
use Jcode;
use Date::Pcalc qw(Nth_Weekday_of_Month_Year);
use FCC::Apache::Session;
my $q = new CGI;
$| = 1;

######################################################################
#  グローバル変数の定義
######################################################################

#バージョン情報
my $CGI_VERSION = '4.11.2';

#フリーサーバのドメインリスト（正規表現）
my $FREE_SERVER_NAME = '\.tok2\.com|\.infoseek\.co\.jp|\.xrea\.com';

#設定値を取得
my %CONF = &GetConf('./data/config.cgi');
my %URL2PATH = ();
$URL2PATH{$CONF{'URL2PATH_URL'}} = $CONF{'URL2PATH_PATH'};
my @MY_SITE_URLs = split(/,/, $CONF{'MY_SITE_URLs'});
my @REJECT_HOSTS = split(/,/, $CONF{'REJECT_HOSTS'});
my @DIRECTORYINDEX = split(/,/, $CONF{'DIRECTORYINDEX'});
if(!$CONF{STIMEOUT}) {
	$CONF{STIMEOUT} = 3600;
}
#管理者用パスワードがセットされていなければ、setup.cgiへリダイレクト
if(! $CONF{ADMINPASS}) {
	print "Location: setup.cgi\n\n";
	exit;
}

#ディレクトリの定義
my $TEMPLATEDIR = './template';
my $LOGDIR = './logs';
my $PRE_LOGNAME = 'access_log';

#著作権表示の定義
my $COPYRIGHT = "futomi's CGI Cafe - 高機能\アクセス解析CGI Professional $CGI_VERSION";
my $COPYRIGHT2 = "<a href=\"http://www.futomi.com\" target=\"_blank\"><img src=\"$CONF{'IMAGE_URL'}/futomilogo.gif\" width=\"80\" height=\"33\" border=\"0\" alt=\"futomi\'s CGI Cafe\"/></a>";
my $COPYRIGHT3 = "<a href=\"http://www.futomi.com\" target=\"_blank\">futomi's CGI Cafe</a>";
my $COPYRIGHT4 = "<a href=\"http://www.futomi.com\" target=\"_blank\">$COPYRIGHT</a>";

#入力パラメータの取得
my $MODE = $q->param('MODE');
my $ANA_MONTH = $q->param('MONTH');
my $ANA_DAY = $q->param('DAY');
my $TARGET_FRAME = $q->param('FRAME');
my $ITEM = $q->param('ITEM');
my $TARGET_VISITOR = $q->param('VISITOR');
#入力パラメータのチェック
if($MODE =~ /[^\w]/) {
	&ErrorPrint('不正な値が送信されました。(MODE)');
}
if($ANA_MONTH =~ /[^\d]/) {
	&ErrorPrint('不正な値が送信されました。(ANA_MONTH)');
}
if($ANA_DAY =~ /[^\d]/) {
	&ErrorPrint('不正な値が送信されました。(ANA_DAY)');
}
if($TARGET_FRAME =~ /[^\w]/) {
	&ErrorPrint('不正な値が送信されました。(FRAME)');
}
if($ITEM =~ /[^\w\d]/) {
	&ErrorPrint('不正な値が送信されました。(ITEM)');
}
if($TARGET_VISITOR =~ /[^\w\d\-\_\.]/) {
	&ErrorPrint('不正な値が送信されました。(VISITOR)');
}

# このCGIのURL
my $CGI_URL = 'acc.cgi';

#Cookie名称
my $COOKIE_NAME = 'ope_sid';
my $COOKIE_NAME_ADM = 'adm_sid';
# 解析対象のログファイル名を特定
my $TARGET_LOGNAME = &SpecifyLogFileName;

######################################################################
#  メインルーチン
######################################################################

#認証
my $action = $q->param('action');
if($CONF{'AUTHFLAG'}) {
	my %cookies = &GetCookie;
	my $sid = $cookies{$COOKIE_NAME};
	if($sid =~ /[^a-zA-Z0-9]/) {
		&ErrorPrint('不正なアクセスです。');
	}
	my %session_data;
	my $session;
	if($action eq 'logon') {
		&LogOn;
	} elsif($action eq 'logoff') {
		&LogOff($sid);
	} elsif($sid) {
		&SessionAuth($sid, \%session_data, $CONF{STIMEOUT});
	} else {
		&PrintAuthForm();
	}
}

# リクエストにredirectキーワードがあれば、指定URLへ転送
if($q->param('REDIRECT')) {
	&RedirectPage($q->param('REDIRECT'));
}

# ターゲットフレームの指定がなければ、親フレームを出力する。
if($TARGET_FRAME eq 'menu') {
	&PrintMenuFrame;
} elsif($TARGET_FRAME eq 'result') {
	&PrintResultFrame;
} else {
	&PrintMainFrame;
}

exit;


######################################################################
#  サブルーチン
######################################################################

sub LogOff {
	my($sid) = @_;
	my $session = new FCC::Apache::Session("./session");
	$session->logoff($sid);
	my $message = 'ログオフしました。';
	my $link_cap = 'ログオン画面へ';
	my $template = './template/logoff.html';
	my $html = &ReadTemplate($template);
	if($ENV{'SERVER_NAME'} =~ /($FREE_SERVER_NAME)/) {
		my $cmeta;
		$cmeta .= "<meta http-equiv=\"Set-Cookie\" content=\"${COOKIE_NAME}=clear; expires=Thu, 01-Jan-1970 00:00:00 GMT;\">\n";
		$cmeta .= "<meta http-equiv=\"Set-Cookie\" content=\"${COOKIE_NAME_ADM}=clear; expires=Thu, 01-Jan-1970 00:00:00 GMT;\">\n";
		$html =~ s/\%COOKIE\%/${cmeta}/;
	} else {
		$html =~ s/\%COOKIE\%//;
	}
	my $content_length = length($html);
	print &ClearCookie($COOKIE_NAME);
	print &ClearCookie($COOKIE_NAME_ADM);
	if($ENV{'SERVER_NAME'} !~ /($FREE_SERVER_NAME)/) {
		print "Content-Length: $content_length\n";
	}
	print "Content-Type: text/html; charset=utf-8\n";
	print "\n";
	print $html;
	exit;
}

sub SessionAuth {
	my($sid, $session_data_ref, $timeout) = @_;
	my $session = new FCC::Apache::Session("./session", $timeout);
	%{$session_data_ref} = $session->sessoin_auth($sid);
	unless($session_data_ref->{_sid}) {
		my $error = "すでにログオフしたか、もしくはセッションタイプアウトしました。再度ログオンしなおしてください。<br />";
		$error .= $session->error();
		$error .= "<hr />［<a href=\"${CGI_URL}\" target=\"_top\">ログオン画面へ</a>］";
		my $html = &ReadTemplate('./template/auth_error.html');
		$html =~ s/\$error\$/${error}/g;
		my $content_length = length $html;
		print &ClearCookie($COOKIE_NAME);
		if($ENV{'SERVER_NAME'} !~ /($FREE_SERVER_NAME)/) {
			print "Content-Length: $content_length\n";
		}
		print "Content-Type: text/html; charset=utf-8\n";
		print "\n";
		print $html;
		exit;
	}
	return $session;
}

sub LogOn {
	my $in_pass = $q->param('PASS');
	if($in_pass eq '') {
		&PrintAuthForm(1);
	}
	if($in_pass ne $CONF{'PASSWORD'}) {
		&PrintAuthForm(1);
	}
	my $session = new FCC::Apache::Session("./session");
	unless($session) {
		&ErrorPrint("システムエラー");
	}
	my %session_data = ${session}->session_create('operator');
	unless($session_data{_sid}) {
		my $err = '認証に失敗しました。:' . $session->error();
		&ErrorPrint($err);
	}
	my $target_url = $CGI_URL . "?t=" . time;
	print &SetCookie($COOKIE_NAME, $session_data{_sid}), "\n";
	print "Content-Type: text/html; charset=utf-8\n";
	print "\n";
	print '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">', "\n";
	print '<html lang="ja" xml:lang="ja" xmlns="http://www.w3.org/1999/xhtml">', "\n";
	print '<head>', "\n";
	print '<meta http-equiv="Content-Language" content="ja" />', "\n";
	print '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />', "\n";
	if($ENV{'SERVER_NAME'} =~ /($FREE_SERVER_NAME)/) {
		print "<meta http-equiv=\"Set-Cookie\" content=\"${COOKIE_NAME}=$session_data{_sid};\" />\n";
	}
	print "<meta http-equiv=\"refresh\" content=\"0;URL=${target_url}\" />\n";
	print '<title>ログオン中...</title>', "\n";
	print '</head>', "\n";
	print '<body>', "\n";
	print '<p style="font-size:small">ログオン中 ...</p>', "\n";
	print '</body></html>';
	exit;
}


sub PrintMainFrame {
	my $menu_url = "$CGI_URL?FRAME=menu";
	my $result_url = "$CGI_URL?FRAME=result";
	if($TARGET_LOGNAME) {
		$menu_url .= "\&amp;LOG=$TARGET_LOGNAME";
		$result_url .= "\&amp;LOG=$TARGET_LOGNAME";
	}
	if($ANA_MONTH) {
		$menu_url .= "\&amp;MONTH=$ANA_MONTH";
		$result_url .= "\&amp;MONTH=$ANA_MONTH";
		if($ANA_DAY) {
			$menu_url .= "\&amp;DAY=$ANA_DAY";
			$result_url .= "\&amp;DAY=$ANA_DAY";
		}
	} else {
		if($ANA_DAY) {
			&ErrorPrint("日を指定する場合には、月を指定して下さい。");
		}
	}
	my $html = &ReadTemplate("$TEMPLATEDIR/mainframe.html");
	$html =~ s/\%MENUURL\%/$menu_url/;
	$html =~ s/\%RESULTURL\%/$result_url/;
	if($ENV{'SERVER_NAME'} =~ /($FREE_SERVER_NAME)/) {
		$html =~ s/\%COOKIE\%/<meta http-equiv='Set-Cookie' CONTENT=PASS=$CONF{'PASSWORD'};' \/>/;
	} else {
		$html =~ s/\%COOKIE\%//;
	}
	&HtmlHeader;
	print "$html\n";
}

sub PrintMenuFrame {
	my(@DateList) = &GetLogDateList("$LOGDIR/$TARGET_LOGNAME");
	my $Today = &GetToday;
	my $MaxDate = pop @DateList;
	my $MinDate = $MaxDate;
	if(scalar(@DateList) >= 1) {
		$MinDate = shift @DateList;
	}

	my $TargetMonth;
	if($ANA_MONTH) {
		$TargetMonth = $ANA_MONTH;
	} elsif($MaxDate) {
		$TargetMonth = substr($MaxDate, 0, 6);
	} else {
		$TargetMonth = substr($Today, 0, 6);
	}

	my $DspYear = substr($TargetMonth, 0, 4);
	my $DspMonth = substr($TargetMonth, 4, 2);
	my $y = $DspYear;	$y =~ s/^0//;
	my $m = $DspMonth;	$m =~ s/^0//;
	my $LastMonth = &GetLastMonth($TargetMonth);
	my $NextMonth = &GetNextMonth($TargetMonth);
	
	my $ThisMonthTag = "<a href=\"$CGI_URL?LOG=$TARGET_LOGNAME&amp;MONTH=$DspYear$DspMonth\" target=\"_top\">$DspYear年 $DspMonth月</a>";
	my $LastMonthTag;
	if($LastMonth >= substr($MinDate, 0, 6)) {
		my $alt = substr($LastMonth, 0, 4) . '年' . substr($LastMonth, 4, 2) . '月';
		$LastMonthTag = "<a href=\"$CGI_URL?LOG=$TARGET_LOGNAME&amp;MONTH=$LastMonth\" target=\"_top\"><img src=\"$CONF{'IMAGE_URL'}/left.gif\" width=\"11\" height=\"15\" alt=\"${alt}\" /></a>\n";
	} else {
		$LastMonthTag = "<img src=\"$CONF{'IMAGE_URL'}/left_g.gif\" width=\"11\" height=\"15\" alt=\"\" />\n";
	}
	my $NextMonthTag;
	if($NextMonth <= substr($MaxDate, 0, 6)) {
		my $alt = substr($NextMonth, 0, 4) . '年' . substr($NextMonth, 4, 2) . '月';
		$NextMonthTag = "<a href=\"$CGI_URL?LOG=$TARGET_LOGNAME&amp;MONTH=$NextMonth\" target=\"_top\"><img src=\"$CONF{'IMAGE_URL'}/right.gif\" width=\"11\" height=\"15\" alt=\"${alt}\" /></a>\n";
	} else {
		$NextMonthTag = "<img src=\"$CONF{'IMAGE_URL'}/right_g.gif\" width=\"11\" height=\"15\" alt=\"\" />\n";
	}

	my $LastDay = &LastDay($DspYear, $DspMonth);
	my $StartWeekNo = &Youbi($DspYear, $DspMonth, "01");
	my $flag = 1;
	my $WeekNo = 0;
	my $day = 1;
	my ($i, $DateBuff, $DspDay, $CalendarTag);
	while($flag) {
		$CalendarTag .= "<tr>\n";
		for($i=0;$i<7;$i++) {
			if($WeekNo < 1 && $i < $StartWeekNo) {
				$CalendarTag .= "  <td class=\"b\">&nbsp;</td>\n";
			} elsif($day > $LastDay) {
				$CalendarTag .= "  <td class=\"b\">&nbsp;</td>\n";
				$day ++;
			} else {
				$DateBuff = $DspYear . $DspMonth;
				if($day < 10) {
					$DateBuff .= "0$day";
				} else {
					$DateBuff .= "$day";
				}
				if($DateBuff == $Today) {
					$DspDay = "<strong>$day</strong>";
				} else {
					$DspDay = "$day";
				}
				if($i == 0) {
					$DspDay = "<span class=\"sun\">$DspDay</span>";
				} elsif(&CheckHoliday($y, $m, $day)) {
					$DspDay = "<span class=\"sun\">$DspDay</span>";
				} elsif($i == 6) {
					$DspDay = "<span class=\"sat\">$DspDay</span>";
				}
				if($DateBuff >= $MinDate && $DateBuff <= $MaxDate) {
					$CalendarTag .= "  <td class=\"a\"><a href=\"$CGI_URL?LOG=$TARGET_LOGNAME&amp;MONTH=$DspYear$DspMonth&amp;DAY=$day\" target=\"_top\">$DspDay</a></td>\n";
				} else {
					$CalendarTag .= "  <td class=\"n\">$DspDay</td>\n";
				}
				$day ++;
			}
		}
		$CalendarTag .= "</tr>\n";
		$WeekNo ++;
		if($day > $LastDay) {
			$flag = 0;
		}
	}

	my $LogListTag = "<form action=\"$CGI_URL\" method=\"post\" target=\"_top\">\n";
	$LogListTag .= "<div><select size=\"1\" name=\"LOG\">\n";
	opendir(LOGDIR, "$LOGDIR") || &ErrorPrint("ログ格納ディレクトリ「$LOGDIR」をオープンできませんでした。 : $!");
	my @files = readdir(LOGDIR);
	closedir(LOGDIR);
	for my $file (sort @files) {
		if($file =~ /^$PRE_LOGNAME/) {
			if($file eq $TARGET_LOGNAME) {
				$LogListTag .= "<option value=\"$file\" selected>$file</option>\n";
			} else {
				$LogListTag .= "<option value=\"$file\">$file</option>\n";
			}
		}
	}
	$LogListTag .= "</select></div>\n";
	$LogListTag .= "<div><input type=\"submit\" name=\"LOGSELECT\" value=\"ログ切替\" /></div>\n";
	$LogListTag .= "</form>\n";


	my $AccModeTag;
	if($ANA_DAY) {
		$AccModeTag = "日指定<br />".substr($ANA_MONTH, 0, 4)."/".substr($ANA_MONTH, 4, 2)."/$ANA_DAY";
	} elsif($ANA_MONTH) {
		$AccModeTag = "月指定<br />".substr($ANA_MONTH, 0, 4)."/".substr($ANA_MONTH, 4, 2);
	} else {
		$AccModeTag = "全指定";
	}

	my $CgiUrl = "$CGI_URL\?FRAME=result\&amp;LOG=$TARGET_LOGNAME";
	if($ANA_MONTH) {
		$CgiUrl .= "&amp;MONTH=$ANA_MONTH";
		if($ANA_DAY) {
			$CgiUrl .= "&amp;DAY=$ANA_DAY";
		}
	}

	my $AllAccUrl = "$CGI_URL\?LOG=$TARGET_LOGNAME";

	my $template = "$TEMPLATEDIR/menuframe.html";
	my $html = &ReadTemplate($template);
	$html =~ s/\%CGIURL\%/$CgiUrl/g;
	$html =~ s/\%LastMonth\%/$LastMonthTag/;
	$html =~ s/\%ThisMonth\%/$ThisMonthTag/;
	$html =~ s/\%NextMonth\%/$NextMonthTag/;
	$html =~ s/\%ALLACCURL\%/$AllAccUrl/;
	$html =~ s/\%calendar\%/$CalendarTag/;
	$html =~ s/\%loglist\%/$LogListTag/;
	$html =~ s/\%ACCMODE\%/$AccModeTag/;
	$html =~ s/\%MYURL\%/$CGI_URL/;
	my $logoff;
	if($CONF{'AUTHFLAG'}) {
		$logoff = "[ <a href=\"${CGI_URL}?action=logoff\" target=\"_top\">ログオフ</a> ]";
	}
	$html =~ s/\%logoff\%/${logoff}/g;
	print "Content-Type: text/html; charset=utf-8\n";
	print "\n";
	print $html;
	exit;
}

sub IsInDate {
	my($date_check) = @_;
	my($date_check_mon, $date_check_day) = $date_check =~ /^(\d{6})(\d{2})/;
	$date_check_day =~ s/^0//;
	if($ANA_MONTH) {
		unless($date_check_mon eq $ANA_MONTH) {
			return 0;
		}
		if($ANA_DAY) {
			unless($date_check_day eq $ANA_DAY) {
				return 0;
			}
		}
	}
	return 1;
}

sub PrintResultFrame {
	if($ITEM eq '') {
		&GeneralStatistics;
	} elsif($ITEM eq 'AccessLogInformation') {
		&AccessLogInformation;
	} elsif($ITEM eq 'LogSearch') {
		&LogSearchForm;
	} elsif($ITEM eq 'LogSearchGo') {
		&LogSearchGo;
	} elsif($ITEM eq 'TopVisitors') {
		&TopVisitors;
	} elsif($ITEM eq 'MostActiveCountries') {
		&MostActiveCountries;
	} elsif($ITEM eq 'MostActivePrefecture') {
		&MostActivePrefecture;
	} elsif($ITEM eq 'MostActiveOrganization') {
		&MostActiveOrganization;
	} elsif($ITEM eq 'NewVsReturningVisitors') {
		&NewVsReturningVisitors;
	} elsif($ITEM eq 'TopPagesByViews') {
		&TopPagesByViews;
	} elsif($ITEM eq 'TopPagesByVisits') {
		&TopPagesByVisits;
	} elsif($ITEM eq 'TopPagesByVisitors') {
		&TopPagesByVisitors;
	} elsif($ITEM eq 'VisitorTrace') {
		&VisitorTrace;
	} elsif($ITEM eq 'ActivityByDayOfTheMonth') {
		&ActivityByDayOfTheMonth;
	} elsif($ITEM eq 'ActivityByDayOfTheWeek') {
		&ActivityByDayOfTheWeek;
	} elsif($ITEM eq 'ActivityByHourOfTheDay') {
		&ActivityByHourOfTheDay;
	} elsif($ITEM eq 'TopReferringSites') {
		&TopReferringSites;
	} elsif($ITEM eq 'TopReferringURLs') {
		&TopReferringURLs;
	} elsif($ITEM eq 'TopSearchKeywords') {
		&TopSearchKeywords;
	} elsif($ITEM eq 'TopSearchEngines') {
		&TopSearchEngines;
	} elsif($ITEM eq 'TopBrowsers') {
		&TopBrowsers;
	} elsif($ITEM eq 'TopPlatforms') {
		&TopPlatforms;
	} elsif($ITEM eq 'TopAcceptLanguage') {
		&TopAcceptLanguage;
	} elsif($ITEM eq 'TopResolution') {
		&TopResolution;
	} elsif($ITEM eq 'TopColorDepth') {
		&TopColorDepth;
	} else {
		&ErrorPrint("不正なリクエストです。");
	}
}

sub GeneralStatistics {
	if(-e "$LOGDIR/$TARGET_LOGNAME") {
		open(LOGFILE, "$LOGDIR/$TARGET_LOGNAME") || &ErrorPrint("アクセスログ「$LOGDIR/$TARGET_LOGNAME」をオープンできませんでした");
	} else {
		&ErrorPrint("アクセスログ（$LOGDIR/$TARGET_LOGNAME）がありません。");
	}
	my $i = 0;
	my $min_date = 99999999999999;
	my $max_date = 0;
	my(%date, %remote_host, %cookies);
	while(<LOGFILE>) {
		chomp;
		my($date_part, $host_part, $cookie_part);
		if(/^(\d{14})\s+(\S+)\s+(\S+)\s+/) {
			$date_part = $1;
			$host_part = $2;
			$cookie_part = $3;
		} else {
			next;
		}
		next if($date_part eq '');
		next unless(&IsInDate($date_part));
		$date{$i} = $date_part;
		$remote_host{$i} = $host_part;
		$cookies{$i} = $cookie_part;
		$max_date = $date_part;
		if($i == 0) {$min_date = $date_part;}
		$i ++;
	}
	close(LOGFILE);
	my $PageViewNum = $i;
	# 総セッション数を調べる
	my $AllSessionNum = &GetSessionNum2(\%date, \%remote_host, \%cookies);
	# 総ユニークユーザー数を調べる。
	my $AllUniqueUserNum = &GetUniqueUserNum2(\%remote_host, \%cookies);
	my $PVperUser = 0;
	if($AllUniqueUserNum > 0) {
		$PVperUser = sprintf("%.2f", $PageViewNum / $AllUniqueUserNum);
	}
	# 今日のログ番号を取得する
	my $Today = &GetToday;
	my $TodayPV = 0;
	my $TodaySessionNum = 0;
	my $TodayUniqueUserNum = 0;
	if( ! ( "${max_date}" lt "${Today}000000" || "${min_date}" gt "${Today}235959" ) ) {
		while( my($no, $d) = each %date ) {
			if($d =~ /^${Today}/) {
				$TodayPV ++;
			} else {
				delete $date{$no};
				delete $remote_host{$no};
				delete $cookies{$no};
			}
		}
		$TodaySessionNum = &GetSessionNum2(\%date, \%remote_host, \%cookies);
		$TodayUniqueUserNum = &GetUniqueUserNum2(\%remote_host, \%cookies);
	}
	undef %date;
	undef %remote_host;
	undef %cookies;
	my $TodayPVperUser;
	if($TodayUniqueUserNum > 0) {
		$TodayPVperUser = sprintf("%.2f", $TodayPV / $TodayUniqueUserNum);
	} else {
		$TodayPVperUser = 0;
	}
	my($min_year, $min_mon, $min_mday, $min_hour, $min_min, $min_sec);
	if($min_date != 99999999999999) {
		($min_year, $min_mon, $min_mday, $min_hour, $min_min, $min_sec) = $min_date =~ /^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/;
	}
	my($max_year, $max_mon, $max_mday, $max_hour, $max_min, $max_sec) = $max_date =~ /^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/;
	my @Keys = (
		'解析対象期間',
		'インプレッション数',
		'セッション数',
		'ユニークユーザー数',
		'一人あたりのインプレッション数'
	);
	my %Data = (
		'解析対象期間' => "$min_year/$min_mon/$min_mday $min_hour:$min_min:$min_sec ～ $max_year/$max_mon/$max_mday $max_hour:$max_min:$max_sec",
		'インプレッション数' => &CommaFormat($PageViewNum),
		'セッション数' => &CommaFormat($AllSessionNum),
		'ユニークユーザー数' => &CommaFormat($AllUniqueUserNum),
		'一人あたりのインプレッション数' => &CommaFormat($PVperUser)
	);
	my $Str;
	$Str .= &MakeTable(\@Keys, \%Data);
	$Str .= "<h2>本日のアクセス状況</h2>\n";
	@Keys = (
		'インプレッション数',
		'セッション数',
		'ユニークユーザー数',
		'一人あたりのインプレッション数'
	);
	%Data = (
		'インプレッション数' => &CommaFormat($TodayPV),
		'セッション数' => &CommaFormat($TodaySessionNum),
		'ユニークユーザー数' => &CommaFormat($TodayUniqueUserNum),
		'一人あたりのインプレッション数' => &CommaFormat($TodayPVperUser)
	);
	$Str .= &MakeTable(\@Keys, \%Data);
	my $Title = '統計概要';
	&PrintResult($Title, $Str);
}


sub AccessLogInformation {
	# 過去ログリストを取得する
	my %LogList = ();
	unless($LOGDIR) {$LOGDIR = '.';}
	opendir(LOGDIR, "$LOGDIR") || &ErrorPrint("ログ格納ディレクトリ「$LOGDIR」をオープンできませんでした。");
	my @log_namaes = readdir(LOGDIR);
	closedir(LOGDIR);
	my($key);
	for $key (@log_namaes) {
		if($key =~ /^$PRE_LOGNAME/) {
			$LogList{$key} = "$LOGDIR\/$key";
		}
	}

	my($Str);
	$Str .= "<form action=\"$CGI_URL\" method=\"post\" target=\"_top\">\n";
	# ログファイル欄
	my($LogListStr);
	$LogListStr .= "<select name=\"LOG\">\n";;
	for $key (sort(keys(%LogList))) {
		if($key eq $TARGET_LOGNAME) {
			$LogListStr .= "<option value=\"$key\" selected>$key</option>\n";
		} else {
			$LogListStr .= "<option value=\"$key\">$key</option>\n";
		}
	}
	$LogListStr .= "</select>\n";
	$LogListStr .= "<input type=\"submit\" value=\"ログ切替\" name=\"LOGSELECT\" />\n";


	# ログファイルサイズ欄
	my $LogSize = &AnalyzeLogfileSize("$LOGDIR/$TARGET_LOGNAME");
	my $LogSizeStr = &CommaFormat($LogSize);
	$LogSizeStr .= " バイト";

	# ログローテーションサイズ欄
	my $LogLotationStr;
	if($CONF{'LOTATION'} eq '0' || $CONF{'LOTATION'} eq '') {
		$LogLotationStr = 'ローテーションしない';
	} elsif($CONF{'LOTATION'} eq '1') {
		my $LogSizeRate = int($LogSize * 1000 / $CONF{'LOTATION_SIZE'}) / 10;
		if($LogSizeRate > 100) {$LogSizeRate = 100;}
		my $LogSizeGraphMaxLen = 150;	#ピクセル
		my $LogSizeGraphLen = int($LogSizeGraphMaxLen * $LogSizeRate / 100);	#ピクセル
		my $dsp_lotation_size = &CommaFormat($CONF{'LOTATION_SIZE'});
		$LogLotationStr .= "${dsp_lotation_size} byte でローテーション<br />\n";
		$LogLotationStr .= "（使用率 ${LogSizeRate}\%）\n";
		$LogLotationStr .= "<div style=\"width:${LogSizeGraphMaxLen}px;height:20px;background-color:#8c8c8c;border-top:1px solid #4c4c4c;border-right:1px solid #ffffff;border-bottom:1px solid #ffffff;border-left:1px solid #4c4c4c;\">\n";
		$LogLotationStr .= "<div style=\"width:${LogSizeGraphLen}px;height:18px;background-color:#acacac;border-top:1px solid #ffffff;border-right:1px solid #4c4c4c;border-bottom:1px solid #4c4c4c;border-left:1px solid #4ffffff;\"></div>\n";
		$LogLotationStr .= "</div>\n";
		# 対象ログの調査開始時と調査終了時を調べる
		if(-e "$LOGDIR/$TARGET_LOGNAME") {
			open(LOGFILE, "$LOGDIR/$TARGET_LOGNAME") || &ErrorPrint("アクセスログ「$LOGDIR/$TARGET_LOGNAME」をオープンできませんでした");
		} else {
			&ErrorPrint("アクセスログ（$LOGDIR/$TARGET_LOGNAME）がありません。");
		}
		my $min_date = 99999999999999;
		my $max_date = 0;
		my $i = 0;
		while(<LOGFILE>) {
			chomp;
			my($date_part);
			if(/^(\d{14})\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+\"([^\"]+)\"\s+\"([^\"]+)\"\s+\"([^\"]+)\"/) {
				$date_part = $1;
			} else {
				next;
			}
			next if($date_part eq '');
			$max_date = $date_part;
			if($i == 0) {$min_date = $date_part;}
			$i ++;
		}
		close(LOGFILE);
		if($i) {
			my $RangeSec = &GetRangeSecond($min_date, $max_date);	# 期間を秒に変換
			my $RemainSec = int( ($CONF{'LOTATION_SIZE'} - $LogSize) * $RangeSec / $LogSize );
			my @DateParts = localtime(time + $CONF{'TIMEDIFF'}*60*60 + $RemainSec);
			$DateParts[5] += 1900;
			$DateParts[4] ++;
			for (my $i=0;$i<=5;$i++) {
				if($DateParts[$i] < 10) {$DateParts[$i] = "0$DateParts[$i]";}
			}
			my $DispRemainSec = &CommaFormat($RemainSec);
			$LogLotationStr .= "ローテーション推定日時<br />\n";
			$LogLotationStr .= "$DateParts[5]年$DateParts[4]月$DateParts[3]日 $DateParts[2]:$DateParts[1]:$DateParts[0] （あと $DispRemainSec 秒）";
		} else {
			$LogLotationStr .= '';
		}
	} elsif($CONF{'LOTATION'} eq '2') {
		$LogLotationStr = '日ごとにローテーション';
	} elsif($CONF{'LOTATION'} eq '3') {
		$LogLotationStr = '月ごとにローテーション';
	} elsif($CONF{'LOTATION'} eq '4') {
		$LogLotationStr = '週ごとにローテーション';
	}

	#ログ一覧
	my(%size_list, %mtime_list);
	for my $file (sort keys %LogList) {
		my @stat = stat("$LOGDIR/$file");
		$size_list{$file} = $stat[7];
		$mtime_list{$file} = $stat[9];
	}
	my $LogAllListStr;
	for my $file ( sort {$mtime_list{$b}<=>$mtime_list{$a}} keys %mtime_list ) {
		my $date = &ConvEpoc2Date($mtime_list{$file});
		my $dsp_size = &CommaFormat($size_list{$file});
		$LogAllListStr .= "  <tr>\n";
		$LogAllListStr .= "    <td>$file</td>\n";	#ファイル名
		$LogAllListStr .= "    <td class=\"right\">$dsp_size byte</td>\n";	#サイズ
		$LogAllListStr .= "    <td>$date</td>\n";	#最終更新日時
		if($file eq $TARGET_LOGNAME) {
			$LogAllListStr .= "    <td class=\"center\">選択中</td>\n";		#ログ切替
		} else {
			$LogAllListStr .= "    <td class=\"center\"><a href=\"$CGI_URL?LOG=$file\" target=\"_top\">解析</a></td>\n";		#ログ切替
		}
		$LogAllListStr .= "  </tr>\n";
	}

	my $html = &ReadTemplate("./template/loginfo.html");
	$html =~ s/\$CGIURL\$/$CGI_URL/;
	$html =~ s/\$LOGFILE\$/$LogListStr/;
	$html =~ s/\$LOGSIZE\$/$LogSizeStr/;
	$html =~ s/\$LOGLOTATION\$/$LogLotationStr/;
	$html =~ s/\$LOGALLLIST\$/$LogAllListStr/;
	print "Content-type: text/html; charset=utf-8\n";
	print "\n";
	print $html;
	exit;
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

sub LogSearchForm {
	my $html = &ReadTemplate("$TEMPLATEDIR/search.html");
	my $log = $q->param('LOG');
	my $hidden;
	$hidden .= "<input type=\"hidden\" name=\"LOG\" value=\"$log\" />\n";
	$hidden .= "<input type=\"hidden\" name=\"ITEM\" value=\"LogSearchGo\" />\n";
	$hidden .= "<input type=\"hidden\" name=\"FRAME\" value=\"result\" />\n";
	$html =~ s/\%CGI_URL\%/$CGI_URL/;
	$html =~ s/\%HIDDEN\%/$hidden/;
	$html =~ s/\%MODE1\%/checked=\"checked\"/;
	$html =~ s/\%MODE2\%//;
	$html =~ s/\%DATE0\%/checked=\"checked\"/;
	$html =~ s/\%DATE1\%//;
	$html =~ s/\%(S|E)(YEAR|MON|DAY)\%//g;
	$html =~ s/\%QSTRING\%//;
	$html =~ s/\%DISPNUM\%/20/;
	$html =~ s/\%HITNUM\%//g;
	$html =~ s/\%DISP_MODE0\%//;
	$html =~ s/\%DISP_MODE1\%/checked=\"checked\"/;
	$html =~ s/\%list\%//;
	my $content_length = length $html;
	if($ENV{'SERVER_NAME'} !~ /($FREE_SERVER_NAME)/) {
		print "Content-Length: $content_length\n";
	}
	print "Content-Type: text/html; charset=utf-8\n";
	print "\n";
	print $html;
	exit;
}

sub LogSearchGo {
	my $mode = $q->param('MODE');
	my $date_opt = $q->param('DATE');
	my $syear = $q->param('SYEAR');
	my $eyear = $q->param('EYEAR');
	my $smon = $q->param('SMON');
	my $emon = $q->param('EMON');
	my $sday = $q->param('SDAY');
	my $eday = $q->param('EDAY');
	my $qstring = $q->param('QSTRING');
	my $disp_mode = $q->param('DISP_MODE');
	my $dispnum = $q->param('DISPNUM');
	my $next = $q->param('NEXT');
	unless($next) {$next = 1;}
	if($mode eq '') {
		$mode = '1';
	} elsif($mode !~ /^(1|2)$/) {
		&ErrorPrint("不正なアクセスです。");
	}
	if($date_opt) {
		unless($syear && $smon && $sday) {
			&ErrorPrint("日付を指定して下さい。");
		}
	}
	my @date_nums = ($syear, $smon, $sday, $eyear, $emon, $eday);
	my @conv = ();
	for my $num (@date_nums) {
		if($num =~ /[^0-9]/) {
			&ErrorPrint("日付は半角数字で指定して下さい。");
		}
		$num += 0;
		if($num < 100) { $num = sprintf("%02d", $num); }
		push(@conv, $num);
	}
	if($conv[0] eq "00") { $conv[0] = "0000"; }
	if($conv[3] eq "00") { $conv[3] = "0000"; }
	my $start = "$conv[0]$conv[1]$conv[2]";
	my $end = "$conv[3]$conv[4]$conv[5]";
	if($disp_mode eq '') {
		$disp_mode = '1';
	}
	if($disp_mode !~ /^(0|1)$/) {
		&ErrorPrint("不正なアクセスです。");
	}
	if($dispnum =~ /[^0-9]/) {
		&ErrorPrint("表示件数は半角数字で指定して下さい。");
	}
	unless($dispnum) {$dispnum = 100;}
	if($next eq '') {$next = 0;}
	if($date_opt) {$end = $start;}
	if($start > $end) {
		&ErrorPrint("検索開始日は、検索終了日より前の日を指定して下さい。");
	}
	my $qstring_secure = &SecureHtml($qstring);
	$qstring =~ s/([\+\.\[\]\(\)\$\@\?\\\-\^\|\*\{\}\/])/\\$1/g;
	$qstring =~ s/\s+/ /g;
	$qstring =~ s/^\s//;
	$qstring =~ s/\s$//;
	my @q_parts = split(/\s/, $qstring);
	if(-e "$LOGDIR/$TARGET_LOGNAME") {
		open(LOGFILE, "$LOGDIR/$TARGET_LOGNAME") || &ErrorPrint("アクセスログ「$LOGDIR/$TARGET_LOGNAME」をオープンできませんでした");
	} else {
		&ErrorPrint("アクセスログ（$LOGDIR/$TARGET_LOGNAME）がありません。");
	}
	my @search_list = ();
	my $hitnum = 0;

	if($mode eq '1') {
		#直近のログだけを検索表示する
		while(<LOGFILE>) {
			chomp;
			my $line = $_;
			if($date_opt) {
				$line =~ m/^(\S+)\s/;
				my $access_date = $1;
				$access_date = substr($access_date, 0, 8);
				unless($access_date >= $start && $access_date <= $end) {next;}
			}
			my $q_flag = 1;
			if(@q_parts) {
				for my $key (@q_parts) {
					unless($line =~ /$key/i) {
						$q_flag = 0;
					}
				}
			}
			unless($q_flag) {next;}
			$hitnum ++;
			my $array_num = scalar @search_list;
			if($array_num >= $dispnum) {
				shift @search_list;
			}
			push(@search_list, $line);
		}
		close(LOGFILE);
	} else {
		#ログの先頭から順次検索表示する
		while(<LOGFILE>) {
			chomp;
			my $line = $_;
			if($date_opt) {
				$line =~ m/^(\S+)\s/;
				my $access_date = $1;
				$access_date = substr($access_date, 0, 8);
				unless($access_date >= $start && $access_date <= $end) {next;}
			}
			my $q_flag = 1;
			if(@q_parts) {
				for my $key (@q_parts) {
					unless($line =~ /$key/i) {
						$q_flag = 0;
					}
				}
			}
			unless($q_flag) {next;}
			$hitnum ++;
			if($hitnum < $next) {next;}
			my $array_num = scalar @search_list;
			if($array_num >= $dispnum) {
				next;
			}
			push(@search_list, $line);
		}
		close(LOGFILE);
	}
	my $html = &ReadTemplate("$TEMPLATEDIR/search.html");
	my $log = $q->param('LOG');
	my $hidden;
	$hidden .= "<input type=\"hidden\" name=\"LOG\" value=\"$log\" />\n";
	$hidden .= "<input type=\"hidden\" name=\"ITEM\" value=\"LogSearchGo\" />\n";
	$hidden .= "<input type=\"hidden\" name=\"FRAME\" value=\"result\" />\n";
	$html =~ s/\%HIDDEN\%/$hidden/;
	$html =~ s/\%MODE$mode\%/checked=\"checked\"/;
	$html =~ s/\%MODE[0-9]+%//g;
	$html =~ s/\%DATE$date_opt\%/checked=\"checked\"/;
	$html =~ s/\%DATE[0-9]+%//g;
	if($date_opt) {
		$html =~ s/\%SYEAR\%/$conv[0]/;
		$html =~ s/\%EYEAR\%/$conv[3]/;
		$html =~ s/\%SMON\%/$conv[1]/;
		$html =~ s/\%EMON\%/$conv[4]/;
		$html =~ s/\%SDAY\%/$conv[2]/;
		$html =~ s/\%EDAY\%/$conv[5]/;
	} else {
		$html =~ s/\%SYEAR\%//;
		$html =~ s/\%EYEAR\%//;
		$html =~ s/\%SMON\%//;
		$html =~ s/\%EMON\%//;
		$html =~ s/\%SDAY\%//;
		$html =~ s/\%EDAY\%//;
	}
	$html =~ s/\%DISPNUM\%/$dispnum/;
	$html =~ s/\%DISP_MODE${disp_mode}\%/checked=\"checked\"/;
	$html =~ s/\%DISP_MODE[0-9]+\%//g;
	$html =~ s/\%QSTRING\%/$qstring_secure/;
	$html =~ s/\%HITNUM\%/検索件数 ：$hitnum 件/g;
	my $n;
	if($mode eq '1') {
		$n = $hitnum - $dispnum + 1;
		if($n <= 0) {$n = 1;}
	} else {
		$n = $next;
	}
	my $lhtml;
	for my $line (@search_list) {
		&Jcode::convert(\$line, 'utf8');
		if($n % 2) {
			$lhtml .= "<div class=\"style1\">";
		} else {
			$lhtml .= "<div class=\"style2\">";
		}
		if($disp_mode) {
			my @parts = $line =~ /^(\d{14})\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+\"([^\"]+)\"\s+\"([^\"]+)\"\s+\"([^\"]+)\"/;
			my $date    = $parts[0];
			my $host    = $parts[1];
			my $cookie  = $parts[2];
			my $req     = $parts[4];
			my $ref     = $parts[5];
			my $ua      = $parts[6];
			my $lang    = $parts[7];
			my $display = $parts[8];
			#日付を整形
			my $date_Y = substr($date,  0, 4);
			my $date_M = substr($date,  4, 2);
			my $date_D = substr($date,  6, 2);
			my $date_h = substr($date,  8, 2);
			my $date_m = substr($date, 10 ,2);
			my $date_s = substr($date, 12, 2);
			#ディスプレー解像度を整形
			my($display_w, $display_h, $display_d) = split(/\s/, $display);
			#HTTP_USER_AGENTをサニタイジング
			$ua = &SecureHtml($ua);
			#結果出力
			$lhtml .= "<div style=\"font-weight:bold\">$n</div>\n";
			$lhtml .= "<table border=\"0\" cellspacing=\"1\" cellpadding=\"0\">\n";
			$lhtml .= "<tr><td valign=\"top\">アクセス日時</td><td width=\"20\" align=\"center\" valign=\"top\">:</td><td>${date_Y}/${date_M}/${date_D} ${date_h}:${date_m}:${date_s}</td></tr>\n";
			$lhtml .= "<tr><td valign=\"top\">ホスト名</td><td width=\"20\" align=\"center\" valign=\"top\">:</td><td>${host}</td></tr>\n";
			$lhtml .= "<tr><td valign=\"top\">ユニークキー</td><td width=\"20\" align=\"center\" valign=\"top\">:</td><td>${cookie}</td></tr>\n";
			if($req =~ /^http/) {
				my $encoded_url = &URL_Encode($req);
				my $link_url = "${CGI_URL}?REDIRECT=${encoded_url}";
				$lhtml .= "<tr><td valign=\"top\">アクセスページ</td><td width=\"20\" align=\"center\" valign=\"top\">:</td><td><a href=\"${link_url}\" target=\"_blank\">${req}</a></td></tr>\n";
			} else {
				$lhtml .= "<tr><td valign=\"top\">アクセスページ</td><td width=\"20\" align=\"center\" valign=\"top\">:</td><td>${req}</td></tr>\n";
			}
			if($ref =~ /^http/) {
				my $encoded_url = &URL_Encode($ref);
				my $link_url = "${CGI_URL}?REDIRECT=${encoded_url}";
				my $disp_url = $ref;
				$disp_url =~ s/\&amp\;/\&/g;
				$disp_url =~ s/\&/\&amp\;/g;
				$lhtml .= "<tr><td valign=\"top\">リンク元URL</td><td width=\"20\" align=\"center\" valign=\"top\">:</td><td><a href=\"${link_url}\" target=\"_blank\">${disp_url}</a></td></tr>\n";
			} else {
				$lhtml .= "<tr><td valign=\"top\">リンク元URL</td><td width=\"20\" align=\"center\" valign=\"top\">:</td><td>${ref}</td></tr>\n";
			}
			$lhtml .= "<tr><td valign=\"top\">USER_AGENT</td><td width=\"20\" align=\"center\" valign=\"top\">:</td><td>${ua}</td></tr>\n";
			$lhtml .= "<tr><td valign=\"top\">ACCEPT_LANGUAGE</td><td width=\"20\" align=\"center\" valign=\"top\">:</td><td>${lang}</td></tr>\n";
			$lhtml .= "<tr><td valign=\"top\">ディスプレー解像度</td><td width=\"20\" align=\"center\" valign=\"top\">:</td><td>${display_w} x ${display_h} x ${display_d}</td></tr>\n";
			$lhtml .= "</table>\n";
		} else {
			$line = &SecureHtml($line);
			$lhtml .= "<b>$n :</b> $line";
		}
		$lhtml .= "</div>\n";
		$n ++;
	}
	if($mode eq '2') {
		$lhtml .= "<hr />\n";
		my $next_tag_num = $n;
		my $prev_tag_num = $next - $dispnum;
		$lhtml .= "<table border=\"0\" width=\"100%\"><tr>\n";
		$lhtml .= "  <td align=\"left\">";
		if($prev_tag_num > 0) {
			#前へを表示
			$lhtml .= "<a href=\"$CGI_URL\?LOG=$log&ITEM=LogSearchGo&FRAME=result&MODE=${mode}&DATE=${date_opt}&SYEAR=${syear}&EYEAR=${eyear}&SMON=${smon}&EMON=${emon}&SDAY=${sday}&EDAY=${eday}&QSTRING=${qstring_secure}&DISPNUM=${dispnum}&DISP_MODE=${disp_mode}&NEXT=${prev_tag_num}\">&lt;&lt;前へ</a>\n";
		}
		$lhtml .= "  </td>\n";
		$lhtml .= "  <td align=\"right\">";
		if($next_tag_num <= $hitnum) {
			#次へを表示
			$lhtml .= "<a href=\"$CGI_URL\?LOG=$log&ITEM=LogSearchGo&FRAME=result&MODE=${mode}&DATE=${date_opt}&SYEAR=${syear}&EYEAR=${eyear}&SMON=${smon}&EMON=${emon}&SDAY=${sday}&EDAY=${eday}&QSTRING=${qstring_secure}&DISPNUM=${dispnum}&DISP_MODE=${disp_mode}&NEXT=${next_tag_num}\">次へ&gt;&gt;</a>\n";
		}
		$lhtml .= "  </td>\n";
		$lhtml .= "</tr></table>\n";
	}
	$html =~ s/\%list\%/$lhtml/;
	my $content_length = length $html;
	if($ENV{'SERVER_NAME'} !~ /($FREE_SERVER_NAME)/) {
		print "Content-Length: $content_length\n";
	}
	print "Content-Type: text/html; charset=utf-8\n";
	print "\n";
	print $html;

	exit;
}

sub TopVisitors {
	if(-e "$LOGDIR/$TARGET_LOGNAME") {
		open(LOGFILE, "$LOGDIR/$TARGET_LOGNAME") || &ErrorPrint("アクセスログ「$LOGDIR/$TARGET_LOGNAME」をオープンできませんでした");
	} else {
		&ErrorPrint("アクセスログ（$LOGDIR/$TARGET_LOGNAME）がありません。");
	}
	my $by = $q->param('by');
	if($by !~ /^(1|2)$/) { $by = 1; }
	my $i = 0;
	my(%date, %remote_host, %cookies);
	while(<LOGFILE>) {
		chomp;
		my($date_part, $host_part, $cookie_part);
		if(/^(\d{14})\s+(\S+)\s+(\S+)\s+/) {
			$date_part = $1;
			$host_part = $2;
			$cookie_part = $3;
		} else {
			next;
		}
		next if($date_part eq '');
		next unless(&IsInDate($date_part));
		$date{$i} = $date_part;
		$remote_host{$i} = $host_part;
		$cookies{$i} = $cookie_part;
		$i ++;
	}
	close(LOGFILE);

	my %HostLogNoList;
	while( my($no, $v) = each %remote_host ) {
		push(@{$HostLogNoList{$v}}, $no);
	}
	my %SessionNumList;
	my %PageViewNumList;
	if($by == 1) {
		while( my($HostName, $v) = each %HostLogNoList ) {
			my $KeyStr = "<a href=\"$CGI_URL?FRAME=result&amp;ITEM=VisitorTrace&amp;LOG=$TARGET_LOGNAME&amp;VISITOR=$HostName";
			if($ANA_MONTH) {
				$KeyStr .= "&amp;MONTH=$ANA_MONTH";
				if($ANA_DAY) {
					$KeyStr .= "&amp;DAY=$ANA_DAY";
				}
			}
			$KeyStr .= "\">";
			$KeyStr .= "$HostName";
			$KeyStr .= "</a>";
			$PageViewNumList{$KeyStr} = scalar @{$HostLogNoList{$HostName}};
		}
	} elsif($by == 2) {
		while( my($HostName, $aref) = each %HostLogNoList ) {
			my $KeyStr = "<a href=\"$CGI_URL?FRAME=result&amp;ITEM=VisitorTrace&amp;LOG=$TARGET_LOGNAME&amp;VISITOR=$HostName";
			if($ANA_MONTH) {
				$KeyStr .= "&amp;MONTH=$ANA_MONTH";
				if($ANA_DAY) {
					$KeyStr .= "&amp;DAY=$ANA_DAY";
				}
			}
			$KeyStr .= "\">";
			$KeyStr .= "$HostName";
			$KeyStr .= "</a>";
			$SessionNumList{$KeyStr} = &GetSessionNum($aref, \%date, \%remote_host, \%cookies);
		}
	}
	undef %HostLogNoList;
	undef %date;
	undef %remote_host;
	undef %cookies;
	my $Str;
	$Str .= "<h2>解析条件指定</h2>\n";
	$Str .= "<form action=\"${CGI_URL}\" method=\"post\">\n";
	$Str .= "<input type=\"hidden\" name=\"FRAME\" value=\"result\" />\n";
	$Str .= "<input type=\"hidden\" name=\"ITEM\" value=\"TopVisitors\" />\n";
	$Str .= "<input type=\"hidden\" name=\"LOG\" value=\"${TARGET_LOGNAME}\" />\n";
	if($ANA_MONTH) {
		$Str .= "<input type=\"hidden\" name=\"MONTH\" value=\"${ANA_MONTH}\" />\n";
		if($ANA_DAY) {
			$Str .= "<input type=\"hidden\" name=\"DAY\" value=\"${ANA_DAY}\" />\n";
		}
	}
	$Str .= "<h3>■ 解析モード</h3>\n";
	my($by1_checked, $by2_checked, $by3_checked);
	if($by == 2) {
		$by2_checked = 'checked="checked "';
	} else {
		$by1_checked = 'checked="checked "';
	}
	$Str .= "<div><input type=\"radio\" name=\"by\" value=\"1\" ${by1_checked}/> インプレッション数　　<input type=\"radio\" name=\"by\" value=\"2\" ${by2_checked}/> セッション数</div>\n";
	$Str .= "<div>　</div>\n";
	$Str .= "<input type=\"submit\" name=\"b1\" value=\"　解　析　\" />\n";
	$Str .= "</form>\n";
	my @Titles;
	if($by == 1) {
		$Str .= "<h2>インプレッション数</h2>\n";
		@Titles = ('順位', 'アクセス元ホスト名', 'インプレッション数', 'グラフ');
		$Str .= &MakeGraph(\%PageViewNumList, \@Titles);
		undef %PageViewNumList;
	}
	if($by == 2) {
		$Str .= "<h2>セッション数</h2>\n";
		@Titles = ('順位', 'アクセス元ホスト名', 'セッション数', 'グラフ');
		$Str .= &MakeGraph(\%SessionNumList, \@Titles);
		undef %SessionNumList;
	}
	my $Title = 'アクセス元ホスト名ランキング';
	&PrintResult($Title, $Str);
}


sub MostActiveCountries {
	if(-e "$LOGDIR/$TARGET_LOGNAME") {
		open(LOGFILE, "$LOGDIR/$TARGET_LOGNAME") || &ErrorPrint("アクセスログ「$LOGDIR/$TARGET_LOGNAME」をオープンできませんでした");
	} else {
		&ErrorPrint("アクセスログ（$LOGDIR/$TARGET_LOGNAME）がありません。");
	}
	my $by = $q->param('by');
	if($by !~ /^(1|2|3)$/) { $by = 1; }
	my $i = 0;
	my(%date, %remote_host, %cookies);
	while(<LOGFILE>) {
		chomp;
		my($date_part, $host_part, $cookie_part);
		if(/^(\d{14})\s+(\S+)\s+(\S+)\s+/) {
			$date_part = $1;
			$host_part = $2;
			$cookie_part = $3;
		} else {
			next;
		}
		next if($date_part eq '');
		next unless(&IsInDate($date_part));
		$date{$i} = $date_part;
		$remote_host{$i} = $host_part;
		$cookies{$i} = $cookie_part;
		$i ++;
	}
	close(LOGFILE);

	my %CountryLogNoList;
	my %IpList = &ReadIpList;
	while( my($i, $rhost) = each %remote_host ) {
		my $domain;
		if($rhost =~ /[^0-9\.]/) {
			$domain = $rhost;
		} else {
			$domain = &GetDomainByAddr($rhost, \%IpList);
		}
		if($domain) {
			my @parts = split(/\./, $domain);
			my $tl = pop @parts;
			$tl = lc $tl;
			push(@{$CountryLogNoList{$tl}}, $i);
		} else {
			push(@{$CountryLogNoList{'?'}}, $i);
		}
	}
	my %SessionNumList;
	my %PageViewNumList;
	my %UniqueNumList;
	if($by == 1) {
		while( my($tld, $aref) = each %CountryLogNoList ) {
			$PageViewNumList{$tld} = scalar @{$aref};
		}
	} elsif($by == 2) {
		while( my($tld, $aref) = each %CountryLogNoList ) {
			$SessionNumList{$tld} = &GetSessionNum($aref, \%date, \%remote_host, \%cookies);
		}
	} elsif($by == 3) {
		while( my($tld, $aref) = each %CountryLogNoList ) {
			$UniqueNumList{$tld} = &GetUniqueUserNum($aref, \%remote_host, \%cookies);
		}
	}
	undef %CountryLogNoList;
	undef %date;
	undef %remote_host;
	undef %cookies;
	my $Str;
	my %TldList = &ReadDef('./data/country.dat');
	my $Str;
	$Str .= "<h2>解析条件指定</h2>\n";
	$Str .= "<form action=\"${CGI_URL}\" method=\"post\">\n";
	$Str .= "<input type=\"hidden\" name=\"FRAME\" value=\"result\" />\n";
	$Str .= "<input type=\"hidden\" name=\"ITEM\" value=\"MostActiveCountries\" />\n";
	$Str .= "<input type=\"hidden\" name=\"LOG\" value=\"${TARGET_LOGNAME}\" />\n";
	if($ANA_MONTH) {
		$Str .= "<input type=\"hidden\" name=\"MONTH\" value=\"${ANA_MONTH}\" />\n";
		if($ANA_DAY) {
			$Str .= "<input type=\"hidden\" name=\"DAY\" value=\"${ANA_DAY}\" />\n";
		}
	}
	$Str .= "<h3>■ 解析モード</h3>\n";
	my($by1_checked, $by2_checked, $by3_checked);
	if($by == 2) {
		$by2_checked = 'checked="checked "';
	} elsif($by == 3) {
		$by3_checked = 'checked="checked "';
	} else {
		$by1_checked = 'checked="checked "';
	}
	$Str .= "<div><input type=\"radio\" name=\"by\" value=\"1\" ${by1_checked}/> インプレッション数　　<input type=\"radio\" name=\"by\" value=\"2\" ${by2_checked}/> セッション数　　<input type=\"radio\" name=\"by\" value=\"3\" ${by3_checked}/> ユニークユーザー数</div>\n";
	$Str .= "<div>　</div>\n";
	$Str .= "<input type=\"submit\" name=\"b1\" value=\"　解　析　\" />\n";
	$Str .= "</form>\n";
	my @Titles;
	if($by == 1) {
		$Str .= "<h2>インプレッション数</h2>\n";
		$Str .= &MakeCircleGraph(\%PageViewNumList);
		@Titles = ('順位', 'TLD', '国名', 'インプレッション数', 'グラフ');
		$Str .= &MakeGraph2(\%PageViewNumList, \@Titles, \%TldList);
		undef %PageViewNumList;
	}
	if($by == 2) {
		$Str .= "<h2>セッション数</h2>\n";
		$Str .= &MakeCircleGraph(\%SessionNumList);
		@Titles = ('順位', 'TLD', '国名', 'セッション数', 'グラフ');
		$Str .= &MakeGraph2(\%SessionNumList, \@Titles, \%TldList);
		undef %SessionNumList;
	}
	if($by == 3) {
		$Str .= "<h2>ユニークユーザー数</h2>\n";
		$Str .= &MakeCircleGraph(\%UniqueNumList);
		@Titles = ('順位', 'TLD', '国名', 'ユニークユーザー数', 'グラフ');
		$Str .= &MakeGraph2(\%UniqueNumList, \@Titles, \%TldList);
		undef %UniqueNumList;
	}
	my $Title = 'アクセス元国名（TLD）ランキング';
	&PrintResult($Title, $Str);
}

sub GetPrefCodeMap {
	my %pref_code_map;
	open(MAP, "./data/pref_code.dat") || &ErrorPrint("./data/pref_code.dat の読み取りに失敗しました。: $!");
	while(<MAP>) {
		if(/^([^\t]+)\t(.+)/) {
			$pref_code_map{$1} = $2;
		}
	}
	close(MAP);
	return %pref_code_map;
}

sub GetPrefList {
	my %pref_list;
	open(LIST, "./data/pref.dat") || &ErrorPrint("./data/pref.dat の読み取りに失敗しました。: $!");
	while(<LIST>) {
		if(/^([^\t]+)\t(.+)/) {
			$pref_list{$1} = $2;
		}
	}
	close(LIST);
	return %pref_list;
}

sub MostActivePrefecture {
	if(-e "$LOGDIR/$TARGET_LOGNAME") {
		open(LOGFILE, "$LOGDIR/$TARGET_LOGNAME") || &ErrorPrint("アクセスログ「$LOGDIR/$TARGET_LOGNAME」をオープンできませんでした");
	} else {
		&ErrorPrint("アクセスログ（$LOGDIR/$TARGET_LOGNAME）がありません。");
	}
	my $by = $q->param('by');
	if($by !~ /^(1|2|3)$/) { $by = 1; }
	my $i = 0;
	my(%date, %remote_host, %cookies);
	while(<LOGFILE>) {
		chomp;
		my($date_part, $host_part, $cookie_part);
		if(/^(\d{14})\s+(\S+)\s+(\S+)\s+/) {
			$date_part = $1;
			$host_part = $2;
			$cookie_part = $3;
		} else {
			next;
		}
		next if($date_part eq '');
		next unless(&IsInDate($date_part));
		$date{$i} = $date_part;
		$remote_host{$i} = $host_part;
		$cookies{$i} = $cookie_part;
		$i ++;
	}
	close(LOGFILE);

	my %pref_code_map = &GetPrefCodeMap;
	my %PrefList = &GetPrefList;
	my %IpList = &ReadIpList;
	my %OrgList = &ReadDef('./data/organization.dat');
	while( my($dmn, $data) = each %OrgList ) {
		my @parts = split(/,/, $data);
		$OrgList{$dmn} = $parts[2];
	}
	my %PrefLogNoList;
	while( my($i, $rhost) = each %remote_host ) {
		my $GetPref = '';
		my $host = lc $rhost;
		if($host =~ /[^\d\.]/) {
			my $PrefKeyword = &GetPrefKeyword($host);
			my $GetPref;
			if($PrefKeyword) {
				$GetPref = $pref_code_map{$PrefList{$PrefKeyword}};
			} else {
				$host =~ m/([\w\-]+\.[\w\-]+\.[\w\-]+)$/;
				my $domain = $1;
				$GetPref = $OrgList{$domain};
				unless($GetPref) {
					$host =~ m/([\w\-]+\.[\w\-]+)$/;
					my $domain = $1;
					$GetPref = $OrgList{$domain};
				}
			}
			unless($GetPref) {next;}
			push(@{$PrefLogNoList{$GetPref}}, $i);
		} else {
			my $domain = &GetDomainByAddr($host, \%IpList);
			unless($OrgList{$domain}) {next;}
			push(@{$PrefLogNoList{$OrgList{$domain}}}, $i);
		}
	}
	my %SessionNumList;
	my %PageViewNumList;
	my %UniqueNumList;
	if($by == 1) {
		while( my($Pref, $aref) = each %PrefLogNoList ) {
			$PageViewNumList{$Pref} = scalar @{$aref};
		}
	} elsif($by == 2) {
		while( my($Pref, $aref) = each %PrefLogNoList ) {
			$SessionNumList{$Pref} = &GetSessionNum($aref, \%date, \%remote_host, \%cookies);
		}
	} elsif($by == 3) {
		while( my($Pref, $aref) = each %PrefLogNoList ) {
			$UniqueNumList{$Pref} = &GetUniqueUserNum($aref, \%remote_host, \%cookies);
		}
	}
	undef %PrefLogNoList;
	undef %date;
	undef %remote_host;
	undef %cookies;
	my $Str;
	$Str .= "<h2>解析条件指定</h2>\n";
	$Str .= "<form action=\"${CGI_URL}\" method=\"post\">\n";
	$Str .= "<input type=\"hidden\" name=\"FRAME\" value=\"result\" />\n";
	$Str .= "<input type=\"hidden\" name=\"ITEM\" value=\"MostActivePrefecture\" />\n";
	$Str .= "<input type=\"hidden\" name=\"LOG\" value=\"${TARGET_LOGNAME}\" />\n";
	if($ANA_MONTH) {
		$Str .= "<input type=\"hidden\" name=\"MONTH\" value=\"${ANA_MONTH}\" />\n";
		if($ANA_DAY) {
			$Str .= "<input type=\"hidden\" name=\"DAY\" value=\"${ANA_DAY}\" />\n";
		}
	}
	$Str .= "<h3>■ 解析モード</h3>\n";
	my($by1_checked, $by2_checked, $by3_checked);
	if($by == 2) {
		$by2_checked = 'checked="checked "';
	} elsif($by == 3) {
		$by3_checked = 'checked="checked "';
	} else {
		$by1_checked = 'checked="checked "';
	}
	$Str .= "<div><input type=\"radio\" name=\"by\" value=\"1\" ${by1_checked}/> インプレッション数　　<input type=\"radio\" name=\"by\" value=\"2\" ${by2_checked}/> セッション数　　<input type=\"radio\" name=\"by\" value=\"3\" ${by3_checked}/> ユニークユーザー数</div>\n";
	$Str .= "<div>　</div>\n";
	$Str .= "<input type=\"submit\" name=\"b1\" value=\"　解　析　\" />\n";
	$Str .= "</form>\n";
	my @Titles;
	if($by == 1) {
		$Str .= "<h2>インプレッション数</h2>\n";
		$Str .= &MakeCircleGraph(\%PageViewNumList);
		@Titles = ('順位', '都道府県名', 'インプレッション数', 'グラフ');
		$Str .= &MakeGraph(\%PageViewNumList, \@Titles);
		undef %PageViewNumList;
	}
	if($by == 2) {
		$Str .= "<h2>セッション数</h2>\n";
		$Str .= &MakeCircleGraph(\%SessionNumList);
		@Titles = ('順位', '都道府県名', 'セッション数', 'グラフ');
		$Str .= &MakeGraph(\%SessionNumList, \@Titles);
		undef %SessionNumList;
	}
	if($by == 3) {
		$Str .= "<h2>ユニークユーザー数</h2>\n";
		$Str .= &MakeCircleGraph(\%UniqueNumList);
		@Titles = ('順位', '都道府県名', 'ユニークユーザー数', 'グラフ');
		$Str .= &MakeGraph(\%UniqueNumList, \@Titles);
		undef %UniqueNumList;
	}
	my $Title = 'アクセス元都道府県ランキング';
	&PrintResult($Title, $Str);
}

sub MostActiveOrganization {
	if(-e "$LOGDIR/$TARGET_LOGNAME") {
		open(LOGFILE, "$LOGDIR/$TARGET_LOGNAME") || &ErrorPrint("アクセスログ「$LOGDIR/$TARGET_LOGNAME」をオープンできませんでした");
	} else {
		&ErrorPrint("アクセスログ（$LOGDIR/$TARGET_LOGNAME）がありません。");
	}
	my $by = $q->param('by');
	if($by !~ /^(1|2|3)$/) { $by = 1; }
	my $i = 0;
	my(%date, %remote_host, %cookies);
	while(<LOGFILE>) {
		chomp;
		my($date_part, $host_part, $cookie_part);
		if(/^(\d{14})\s+(\S+)\s+(\S+)\s+/) {
			$date_part = $1;
			$host_part = $2;
			$cookie_part = $3;
		} else {
			next;
		}
		next if($date_part eq '');
		next unless(&IsInDate($date_part));
		$date{$i} = $date_part;
		$remote_host{$i} = $host_part;
		$cookies{$i} = $cookie_part;
		$i ++;
	}
	close(LOGFILE);
	my %OrgLogNoList;
	my %IpList = &ReadIpList;
	for my $i (keys %remote_host) {
		my $host = lc $remote_host{$i};
		if($host =~ /[^0-9\.]/) {
			my $org_domain = &GetDomainByHostname($host);
			push(@{$OrgLogNoList{$org_domain}}, $i);
		} else {
			my $domain = &GetDomainByAddr($host, \%IpList);
			push(@{$OrgLogNoList{$domain}}, $i);
		}
	}
	my %SessionNumList;
	my %PageViewNumList;
	my %UniqueNumList;
	if($by == 1) {
		while( my($Org, $aref) = each %OrgLogNoList ) {
			$PageViewNumList{$Org} = scalar @{$aref};
		}
	} elsif($by == 2) {
		while( my($Org, $aref) = each %OrgLogNoList ) {
			$SessionNumList{$Org} = &GetSessionNum($aref, \%date, \%remote_host, \%cookies);
		}
	} elsif($by == 3) {
		while( my($Org, $aref) = each %OrgLogNoList ) {
			$UniqueNumList{$Org} = &GetUniqueUserNum($aref, \%remote_host, \%cookies);
		}
	}
	undef %OrgLogNoList;
	undef %date;
	undef %remote_host;
	undef %cookies;
	my %OrgList = &ReadDef('./data/organization.dat');
	for my $Domain (keys %OrgList) {
		my($Str1, $Str2) = split(/,/, $OrgList{$Domain});
		if($Str2) {
			$OrgList{$Domain} = "<div>$Str1</div><div class=\"size2\">$Str2</div>";
		} else {
			$OrgList{$Domain} = "$Str1";
		}
	}
	my $Str;
	$Str .= "<h2>解析条件指定</h2>\n";
	$Str .= "<form action=\"${CGI_URL}\" method=\"post\">\n";
	$Str .= "<input type=\"hidden\" name=\"FRAME\" value=\"result\" />\n";
	$Str .= "<input type=\"hidden\" name=\"ITEM\" value=\"MostActiveOrganization\" />\n";
	$Str .= "<input type=\"hidden\" name=\"LOG\" value=\"${TARGET_LOGNAME}\" />\n";
	if($ANA_MONTH) {
		$Str .= "<input type=\"hidden\" name=\"MONTH\" value=\"${ANA_MONTH}\" />\n";
		if($ANA_DAY) {
			$Str .= "<input type=\"hidden\" name=\"DAY\" value=\"${ANA_DAY}\" />\n";
		}
	}
	$Str .= "<h3>■ 解析モード</h3>\n";
	my($by1_checked, $by2_checked, $by3_checked);
	if($by == 2) {
		$by2_checked = 'checked="checked "';
	} elsif($by == 3) {
		$by3_checked = 'checked="checked "';
	} else {
		$by1_checked = 'checked="checked "';
	}
	$Str .= "<div><input type=\"radio\" name=\"by\" value=\"1\" ${by1_checked}/> インプレッション数　　<input type=\"radio\" name=\"by\" value=\"2\" ${by2_checked}/> セッション数　　<input type=\"radio\" name=\"by\" value=\"3\" ${by3_checked}/> ユニークユーザー数</div>\n";
	$Str .= "<div>　</div>\n";
	$Str .= "<input type=\"submit\" name=\"b1\" value=\"　解　析　\" />\n";
	$Str .= "</form>\n";
	my @Titles;
	if($by == 1) {
		$Str .= "<h2>インプレッション数</h2>\n";
		@Titles = ('順位', 'ドメイン名', '組織名', 'インプレッション数', 'グラフ');
		$Str .= &MakeGraph2(\%PageViewNumList, \@Titles, \%OrgList);
		undef %PageViewNumList;
	}
	if($by == 2) {
		$Str .= "<h2>セッション数</h2>\n";
		@Titles = ('順位', 'ドメイン名', '組織名', 'セッション数', 'グラフ');
		$Str .= &MakeGraph2(\%SessionNumList, \@Titles, \%OrgList);
		undef %SessionNumList;
	}
	if($by == 3) {
		$Str .= "<h2>ユニークユーザー</h2>\n";
		@Titles = ('順位', 'ドメイン名', '組織名', 'ユニークユーザー数', 'グラフ');
		$Str .= &MakeGraph2(\%UniqueNumList, \@Titles, \%OrgList);
		undef %UniqueNumList;
	}
	undef %OrgList;
	my $Title = 'アクセス元組織名ランキング';
	&PrintResult($Title, $Str);
}

sub GetDomainByAddr {
	my($addr, $list_ref) = @_;
	$addr =~ m/^([0-9]+)\.([0-9]+)\.([0-9]+)\.([0-9]+)$/;
	my $bin = unpack("B32", pack("c4", $1, $2, $3, $4));
	my $flag = 0;
	for my $mask_bin (keys %$list_ref) {
		if($bin =~ /^$mask_bin/) {
			return $$list_ref{$mask_bin};
			last;
		} else {
			next;
		}
	}
	return '?';
}

sub ReadIpList {
	open(IP, './data/ipaddr.dat') || &ErrorPrint("IPアドレスデータファイル ipaddr.dat をオープンできませんでした。 : $!");
	my %ip_list;
	while(<IP>) {
		chomp;
		if(/^([0-9]+)\.([0-9]+)\.([0-9]+)\.([0-9]+)(\/([0-9]+))*\=(.+)$/) {
			my $ip1 = $1;
			my $ip2 = $2;
			my $ip3 = $3;
			my $ip4 = $4;
			my $mask = $6;
			my $domain = $7;
			my $bin = unpack("B32", pack("c4", $ip1, $ip2, $ip3, $ip4));
			unless($mask) {
				if($ip4 eq '0') {
					$mask = '24';
				} else {
					$mask = 32;
				}
			}
			my $mask_bin = substr($bin, 0, $mask);
			$ip_list{$mask_bin} = $domain;
		} else {
			next;
		}
	}
	close(IP);
	return %ip_list;
}

sub NewVsReturningVisitors {
	if(-e "$LOGDIR/$TARGET_LOGNAME") {
		open(LOGFILE, "$LOGDIR/$TARGET_LOGNAME") || &ErrorPrint("アクセスログ「$LOGDIR/$TARGET_LOGNAME」をオープンできませんでした");
	} else {
		&ErrorPrint("アクセスログ（$LOGDIR/$TARGET_LOGNAME）がありません。");
	}
	my $i = 0;
	my $min_date = 99999999999999;
	my $max_date = 0;
	my(%remote_host, %cookies);
	while(<LOGFILE>) {
		chomp;
		my($date_part, $host_part, $cookie_part);
		if(/^(\d{14})\s+(\S+)\s+(\S+)/) {
			$date_part = $1;
			$host_part = $2;
			$cookie_part = $3;
		} else {
			next;
		}
		next if($date_part eq '');
		next unless(&IsInDate($date_part));
		$remote_host{$i} = $host_part;
		$cookies{$i} = $cookie_part;
		$max_date = $date_part;
		if($i == 0) {$min_date = $date_part;}
		$i ++;
	}
	close(LOGFILE);

	my %RepeaterCookies;
	for my $i (keys %cookies) {
		my($first_epoch) = $cookies{$i} =~ m/\.(\d+)$/;
		my ($Sec, $Min, $Hour, $Day, $Mon, $Year) = localtime($first_epoch + $CONF{'TIMEDIFF'}*60*60);
		$Year += 1900;
		$Mon ++;
		$Mon = sprintf("%02d", $Mon);
		$Day = sprintf("%02d", $Day);
		$Hour = sprintf("%02d", $Hour);
		$Min = sprintf("%02d", $Min);
		$Sec = sprintf("%02d", $Sec);
		my $AccessDate = "${Year}${Mon}${Day}${Hour}${Min}${Sec}";
		if($AccessDate < $min_date) {
			$RepeaterCookies{$cookies{$i}} ++;
		}
	}
    # 総ユニークユーザー数を調べる。
    my $AllUniqueUserNum = &GetUniqueUserNum2(\%remote_host, \%cookies);
	my $RepeaterUserNum = scalar keys %RepeaterCookies;
	undef %remote_host;
	undef %cookies;
	undef %RepeaterCookies;
	my @Keys = (
		'初めての訪問者数',
		'リピーター訪問者数',
		'総訪問者数'
	);
	my %Data = (
		'初めての訪問者数' => &CommaFormat($AllUniqueUserNum - $RepeaterUserNum),
		'リピーター訪問者数' => &CommaFormat($RepeaterUserNum),
		'総訪問者数' => &CommaFormat($AllUniqueUserNum)
	);
	my($Str);
	$Str .= &MakeTable(\@Keys, \%Data);
	%Data = ('初めての訪問者'=>$AllUniqueUserNum - $RepeaterUserNum, 'リピーター'=>$RepeaterUserNum);
	$Str .= &MakeCircleGraph(\%Data);
	my $Title = 'リピーター比率分析';
	&PrintResult($Title, $Str);
}

sub TopPagesByViews {
	&AnalyzeRequestResource('view');
} 

sub TopPagesByVisits {
	&AnalyzeRequestResource('session');
} 

sub TopPagesByVisitors {
	&AnalyzeRequestResource('unique');
}
	
sub AnalyzeRequestResource {
	my($MODE) = @_;
	if(-e "$LOGDIR/$TARGET_LOGNAME") {
		open(LOGFILE, "$LOGDIR/$TARGET_LOGNAME") || &ErrorPrint("アクセスログ「$LOGDIR/$TARGET_LOGNAME」をオープンできませんでした");
	} else {
		&ErrorPrint("アクセスログ（$LOGDIR/$TARGET_LOGNAME）がありません。");
	}
	my @req_url_conv_1_arr = split(/,/, $CONF{REQ_URL_CONV_1});
	my %req_url_conv_1_hash;
	for my $u (@req_url_conv_1_arr) {
		$req_url_conv_1_hash{$u} = 1;
	}
	my $req_url_conv_2 = $CONF{REQ_URL_CONV_2};
	my $i = 0;
	my(%date, %remote_host, %cookies, %request);
	while(<LOGFILE>) {
		chomp;
		my($date_part, $host_part, $cookie_part, $request_part);
		if(/^(\d{14})\s+(\S+)\s+(\S+)\s+\S+\s+(\S+)\s+/) {
			$date_part = $1;
			$host_part = $2;
			$cookie_part = $3;
			$request_part = $4;
		} else {
			next;
		}
		next if($date_part eq '');
		next unless(&IsInDate($date_part));
		$date{$i} = $date_part;
		$remote_host{$i} = $host_part;
		if($request_part =~ /^http:\/\/[^\/]+$/) {
			$request_part .= '/';
		}
		$request_part =~ s/\%7E/\~/ig;
		if($CONF{'URLHANDLE'}) {
			$request_part =~ s/\?.*$//;
		}
		my($req_host_url) = $request_part =~ /^(https*\:\/\/[a-zA-Z0-9\-\_\.]+\/)/;
		if($req_url_conv_1_hash{$req_host_url}) {
			$request_part =~ s/^${req_host_url}/${req_url_conv_2}/;
		}
		$request{$i} = $request_part;
		$cookies{$i} = $cookie_part;
		$i ++;
	}
	close(LOGFILE);

	my %LogNoListBuff;
	for my $i (keys %request) {
		my($HtmlFilePath, $Index, $HitFlag, $FileTest, $uri, $RequestPath);
		if($request{$i} eq '' || $request{$i} eq '-') {next;}
		if($request{$i} =~ /\/$/) {
			unless($request{$i}) {next;}
			$_ = $request{$i};
			m|https*://[^/]+/(.*)|;
			$RequestPath = '/'.$1;
			if($CONF{'URL2PATH_FLAG'}) {
				my $key;
				for $key (keys %URL2PATH) {
					if($request{$i} =~ /^$key/) {
						$HtmlFilePath = $request{$i};
						$HtmlFilePath =~ s/^$key/$URL2PATH{$key}/;
						last;
					}
				}
			} else {
				$HtmlFilePath = $ENV{'DOCUMENT_ROOT'}.$RequestPath;
			}

			$HitFlag = 0;
			for $Index (@DIRECTORYINDEX) {
				$FileTest = $HtmlFilePath.$Index;
				if(-e $FileTest) {
					$uri = $request{$i}.$Index;
					$HitFlag = 1;
					last;
				}
			}
			unless($HitFlag) {$uri = $request{$i};}
		} else {
		    $uri = $request{$i};
		}
		push(@{$LogNoListBuff{$uri}}, $i);
	}
	my $HtmlTitle;
	my %LogNoList;
	my %ManualTitle = &ReadTitleDat;
	for my $uri (keys %LogNoListBuff) {
		my $HtmlTitle = $ManualTitle{$uri};
		unless($HtmlTitle) {
			$HtmlTitle = &GetHtmlTitle("$uri");
		}
		unless($HtmlTitle) {$HtmlTitle = "<span class=\"italic\">不明</span>"}
		my $disp_uri = $uri;
		if(length($disp_uri) > 70) {
			$disp_uri = substr($disp_uri, 0, 70);
			$disp_uri .= '...';
		}
		$disp_uri =~ s/\&/\&amp;/g;
		my $a_uri = $uri;
		$a_uri =~ s/\&/\&amp;/g;
		$LogNoList{"$HtmlTitle<br /><div class=\"size2\"><a href=\"${a_uri}\" target=\"blank\">${disp_uri}</a></div>"} = $LogNoListBuff{$uri};
	}
	undef %LogNoListBuff;
	my %SessionNumList;
	my %PageViewNumList;
	my %UniqueNumList;
	if($MODE eq 'view') {
		while( my($Path, $aref) = each %LogNoList ) {
			$PageViewNumList{$Path} = scalar @{$aref};
		}
	} elsif($MODE eq 'session') {
		while( my($Path, $aref) = each %LogNoList ) {
			$SessionNumList{$Path} = &GetSessionNum($aref, \%date, \%remote_host, \%cookies);
		}
	} elsif($MODE eq 'unique') {
		while( my($Path, $aref) = each %LogNoList ) {
			$UniqueNumList{$Path} = &GetUniqueUserNum($aref, \%remote_host, \%cookies);
		}
	}
	undef %LogNoList;
	undef %date;
	undef %remote_host;
	undef %cookies;
	undef %request;
	my($Str, $Title);
	if($MODE eq 'view') {
		$Str .= "<h2>インプレッション数</h2>\n";
		my(@Titles) = ('順位', 'ページ', 'インプレッション数', 'グラフ');
		$Str .= &MakeGraph(\%PageViewNumList, \@Titles);
		$Title = 'アクセスページランキング（インプレッション数解析）';
		undef %PageViewNumList;
	} elsif($MODE eq 'session') {
		$Str .= "<h2>セッション数</h2>\n";
		my(@Titles) = ('順位', 'ページ', 'セッション数', 'グラフ');
		$Str .= &MakeGraph(\%SessionNumList, \@Titles);
		$Title = 'アクセスページランキング（セッション数解析）';
		undef %SessionNumList;
	} elsif($MODE eq 'unique') {
		$Str .= "<h2>ユニークユーザー数</h2>\n";
		my(@Titles) = ('順位', 'ページ', 'ユニークユーザー数', 'グラフ');
		$Str .= &MakeGraph(\%UniqueNumList, \@Titles);
		$Title = 'アクセスページランキング（ユニークユーザー数解析）';
		undef %UniqueNumList;
	}
	&PrintResult($Title, $Str);
}

sub VisitorTrace {
	if(-e "$LOGDIR/$TARGET_LOGNAME") {
		open(LOGFILE, "$LOGDIR/$TARGET_LOGNAME") || &ErrorPrint("アクセスログ「$LOGDIR/$TARGET_LOGNAME」をオープンできませんでした");
	} else {
		&ErrorPrint("アクセスログ（$LOGDIR/$TARGET_LOGNAME）がありません。");
	}
	my $i = 0;
	my(%date, %remote_host, %request, %referer, %user_agent, %screen);
	while(<LOGFILE>) {
		chomp;
		my($date_part, $host_part, $request_part, $referer_part, $ua_part, $accept_lang_part, $screen_part);
		if(/^(\d{14})\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+\"([^\"]+)\"\s+\"([^\"]+)\"\s+\"([^\"]+)\"/) {
			$date_part = $1;
			$host_part = $2;
			$request_part = $5;
			$referer_part = $6;
			$ua_part = $7;
			$accept_lang_part = $8;
			$screen_part = $9;
		} else {
			next;
		}
		next if($date_part eq '');
		next unless(&IsInDate($date_part));
		$date{$i} = $date_part;
		$remote_host{$i} = $host_part;
		if($TARGET_VISITOR) {
			if($host_part eq $TARGET_VISITOR) {
				$request{$i} = $request_part;
				$referer{$i} = $referer_part;
				$user_agent{$i} = $ua_part;
				$screen{$i} = $screen_part;
			}
		}
		$i ++;
	}
	close(LOGFILE);

	my $TraceDomain = $q->param('TRACEDOMAIN');
	if($TraceDomain =~ /[^\d\w\.\-\_]/) {
		&ErrorPrint('不正な値が送信されました。(TRACEDOMAIN)');
	}
	my %VisitorList;
	while( my($i, $rhost) = each %remote_host ) {
		$VisitorList{$rhost} ++;
	}
	my $PrintStr;
	$PrintStr .= "<form action=\"$CGI_URL\" method=\"post\" target=\"_self\">\n";
	$PrintStr .= "<input type=\"hidden\" name=\"FRAME\" value=\"result\" />\n";
	$PrintStr .= "<input type=\"hidden\" name=\"ITEM\" value=\"VisitorTrace\" />\n";
	$PrintStr .= "<input type=\"hidden\" name=\"LOG\" value=\"$TARGET_LOGNAME\" />\n";
	$PrintStr .= "<input type=\"hidden\" name=\"TRACEDOMAIN\" value=\"$TraceDomain\" />\n";
	if($ANA_MONTH) {
		$PrintStr .= "<input type=\"hidden\" name=\"MONTH\" value=\"$ANA_MONTH\" />\n";
		if($ANA_DAY) {
			$PrintStr .= "<input type=\"hidden\" name=\"DAY\" value=\"$ANA_DAY\" />\n";
		}
	}

	$PrintStr .= "<select size=\"1\" name=\"VISITOR\">\n";
	$PrintStr .= "<option value=\"\">ホストを選択して下さい</option>\n";
	for my $visitor ( sort {$VisitorList{$b}<=>$VisitorList{$a}} keys %VisitorList ) {
		if($TraceDomain) {
			unless($visitor =~ /$TraceDomain/) {next;}
		}
		$PrintStr .= "<option value=\"$visitor\"";
		if($visitor eq $TARGET_VISITOR) {
			$PrintStr .= " selected";
		}
		$PrintStr .= ">$visitor ($VisitorList{$visitor})</option>\n";
	}
	$PrintStr .= "</select>\n";
	$PrintStr .= "<input type=\"submit\" value=\"追跡\" name=\"B1\" />\n";
	$PrintStr .= "</form>\n";

	$PrintStr .= "<form action=\"$CGI_URL\" method=\"post\" target=\"_self\">\n";
	$PrintStr .= "<input type=\"hidden\" name=\"FRAME\" value=\"result\" />\n";
	$PrintStr .= "<input type=\"hidden\" name=\"ITEM\" value=\"VisitorTrace\" />\n";
	$PrintStr .= "<input type=\"hidden\" name=\"LOG\" value=\"$TARGET_LOGNAME\" />\n";
	if($ANA_MONTH) {
		$PrintStr .= "<input type=\"hidden\" name=\"MONTH\" value=\"$ANA_MONTH\" />\n";
		if($ANA_DAY) {
			$PrintStr .= "<input type=\"hidden\" name=\"DAY\" value=\"$ANA_DAY\" />\n";
		}
	}
	$PrintStr .= "<table border=\"0\"><tr>\n";
	$PrintStr .= "<td>ドメイン名から絞込み検索</td>\n";
	$PrintStr .= "<td><input type=\"text\" name=\"TRACEDOMAIN\" value=\"$TraceDomain\" class=\"inputlimit_host\" /></td>\n";
	$PrintStr .= "<td><input type=\"submit\" value=\"検索\" /></td>\n";
	$PrintStr .= "</tr></table>\n";
	$PrintStr .= "</form>\n";

	if($TARGET_VISITOR) {
		$PrintStr .= "<h2>■ 訪問者情報</h2>\n";
		my($i, $UserAgent, $ScreenInfo, $FirstNo);
		my $FirstFlag = 0;
		for $i (sort{$a <=> $b} keys %request) {
			unless($FirstFlag) {
				$FirstNo = $i;
				$FirstFlag = 1;
			}
			$UserAgent = $user_agent{$i};
			$ScreenInfo = $screen{$i};
			unless($ScreenInfo) {
				$ScreenInfo = '&nbsp;';
			} else {
				$ScreenInfo =~ s/ /×/g;
			}
		}
		my $ReferUrl = $referer{$FirstNo};

		#OS,ブラウザーを特定
		my ($OS, $OS_V, $BR, $BR_V) = &User_Agent($UserAgent);
		unless($OS) {$OS = '&nbsp;';}
		unless($OS_V) {$OS_V = '&nbsp;';}
		unless($BR) {$BR = '&nbsp;';}
		unless($BR_V) {$BR_V = '&nbsp;';}

		my @HostParts = split(/\./, $TARGET_VISITOR);
		my $Part1 = lc pop(@HostParts);
		my $Part2 = lc pop(@HostParts);
		my $Part3 = lc pop(@HostParts);

		#アクセス元国名（TLD）の特定
		my $CountryStr;
		unless($Part1 =~ /[0-9]/) {
			my %TldList = &ReadDef('./data/country.dat');
			$CountryStr = $TldList{$Part1}.'('.$Part1.')';
			undef %TldList;
		}
		unless($CountryStr) {$CountryStr = '&nbsp;';}

		#アクセス元都道府県名、組織名の特定
		my %OrgList = &ReadDef('./data/organization.dat');
		my ($GetPref, $DomainTmp, $Str1, $Str2);

		if(length($Part1) >= 3) {
			$DomainTmp = "$Part2.$Part1";
		} elsif($Part1 eq 'jp') {
			my @AreaList = ('hokkaido', 'aomori', 'iwate', 'miyagi', 'akita', 'yamagata', 'fukushima',
				'ibaraki', 'tochigi', 'gunma', 'saitama', 'chiba', 'tokyo', 'kanagawa',
				'niigata', 'toyama', 'ishikawa', 'fukui', 'yamanashi', 'nagano', 'gifu',
				'shizuoka', 'aichi', 'mie', 'shiga', 'kyoto', 'osaka', 'hyogo', 'nara',
				'wakayama', 'tottori', 'shimane', 'okayama', 'hiroshima', 'yamaguchi',
				'tokushima', 'kagawa', 'ehime', 'kochi', 'fukuoka', 'saga', 'nagasaki',
				'kumamoto', 'oita', 'miyazaki', 'kagoshima', 'okinawa', 'sapporo',
				'sendai', 'chiba', 'yokohama', 'kawasaki', 'nagoya', 'kyoto', 'osaka',
				'kobe', 'hiroshima', 'fukuoka', 'kitakyushu');

			if(length($Part2) ==2 || grep(/^$Part2$/, @AreaList)) {
				$DomainTmp = "$Part3.$Part2.$Part1";
			} else {
				$DomainTmp = "$Part2.$Part1";
			}
		} else {
			$DomainTmp = "$Part2.$Part1";
		}

		($Str1, $Str2, $GetPref) = split(/,/, $OrgList{$DomainTmp});
		undef %OrgList;
		my $Organization = "$Str1";
		unless($Organization) {$Organization = '&nbsp;';}
		if($Str2) {
			$Organization .= "<span class=\"size2\"> ($Str2)</span>";
		}
		unless($GetPref) {
			my %pref_code_map = &GetPrefCodeMap;
			my %PrefList = &GetPrefList;
			my $host = lc $TARGET_VISITOR;
			my $PrefKeyword = &GetPrefKeyword($host);
			my $pref_code = $PrefList{$PrefKeyword};
			$GetPref = $pref_code_map{$pref_code};
			undef %PrefList;
		}
		unless($GetPref) {$GetPref = '&nbsp;';}

		my @Keys = (
			'ホスト名',
			'国（TLD）',
			'都道府県',
			'組織名',
			'HTTP_USER_AGENT',
			'プラットフォーム',
			'ブラウザ',
			'画面解像度情報',
		);
		&Jcode::convert(\$UserAgent, 'utf8');
		my %Data = (
			'ホスト名' => $TARGET_VISITOR,
			'国（TLD）' => $CountryStr,
			'都道府県' => $GetPref,
			'組織名', => $Organization,
			'HTTP_USER_AGENT' => $UserAgent,
			'プラットフォーム' => "$OS $OS_V",
			'ブラウザ' => "$BR $BR_V",
			'画面解像度情報' => $ScreenInfo
		);
		$PrintStr .= &MakeTable(\@Keys, \%Data);
		my $SessionNo = 1;
		my $DspReferUrl = $ReferUrl;
		if(length($ReferUrl) > 50) {
			$DspReferUrl = substr($ReferUrl, 0, 50) . '...';
		}
		$PrintStr .= "<h2>■ 閲覧ページ追跡</h2>\n";
		$PrintStr .= "<table class=\"tbl2\">\n";
		$PrintStr .= "<tr><th colspan=\"3\" style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\">セッション $SessionNo</th></tr>\n";
		$PrintStr .= "<tr><td class=\"row1\"><img src=\"$CONF{'IMAGE_URL'}/refer.gif\" width=\"17\" height=\"17\" alt=\"リンク元\" /></td><td class=\"row1\"> リンク元</td><td class=\"row1\">";
		if($ReferUrl eq '-' || $ReferUrl eq '') {
			$PrintStr .= "$DspReferUrl";
		} else {
			$DspReferUrl =~ s/\&/\&amp;/g;
			$ReferUrl =~ s/\&/\&amp;/g;
			$PrintStr .= "<a href=\"$ReferUrl\" target=\"_blank\">$DspReferUrl</a>";
		}
		$PrintStr .= "</td></tr>\n";
		my($IntervalSec, $PreNo, $DspDate, $DspRequestUrl);
		my %ManualTitle = &ReadTitleDat;
		for $i (sort{$a <=> $b} keys %request) {
			unless($i == $FirstNo) {
				$IntervalSec = &GetRangeSecond($date{$PreNo}, $date{$i});
				if($IntervalSec <= $CONF{'INTERVAL'}) {
					$PrintStr .= "<tr><td class=\"row3 right\" colspan=\"3\">$IntervalSec秒 <img src=\"$CONF{'IMAGE_URL'}/timer.gif\" width=\"17\" height=\"17\" alt=\"${IntervalSec}秒\" /></td></tr>\n";
				} else {
					$PrintStr .= "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>\n";
					$PrintStr .= "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>\n";
					$SessionNo ++;
					$PrintStr .= "<tr><th colspan=\"3\" style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\">セッション $SessionNo</th></tr>\n";
					$DspReferUrl = $referer{$i};
					if(length($referer{$i}) > 50) {
						$DspReferUrl = substr($referer{$i}, 0, 50) . '...';
					}
					$PrintStr .= "<tr><td class=\"row1\"><img src=\"$CONF{'IMAGE_URL'}/refer.gif\" width=\"17\" height=\"17\" alt=\"リンク元\" /></td><td class=\"row1\"> リンク元</td><td class=\"row1\">";
					if($referer{$i} eq '-' || $referer{$i} eq '') {
						$PrintStr .= "$DspReferUrl";
					} else {
						$PrintStr .= "<a href=\"$referer{$i}\" target=\"_blank\">$DspReferUrl</a>";
					}
					$PrintStr .= "</td></tr>\n";
				}
			}
			$DspDate = &ConvDspDate($date{$i});
			if(length($request{$i}) > 50) {
				$DspRequestUrl = substr($request{$i}, 0, 50) . '...';
			} else {
				$DspRequestUrl = $request{$i};
			}
			$PrintStr .= "<tr>\n";
			$PrintStr .= "  <td class=\"row2\"><img src=\"$CONF{'IMAGE_URL'}/file.gif\" width=\"17\" height=\"17\" alt=\"\" /></td>\n";
			$PrintStr .= "  <td class=\"row2\"> $DspDate</td>\n";
			if($request{$i} eq '-' || $request{$i} eq '') {
				$PrintStr .= "  <td class=\"row2\"> $DspRequestUrl</td>\n";
			} else {
				$PrintStr .= "  <td class=\"row2\"> <a href=\"$request{$i}\" target=\"_blank\">$DspRequestUrl</a></td>\n";
			}
			$PrintStr .= "</tr>\n";
			$PreNo = $i;
		}
		$PrintStr .= "</table>\n";
	}
	my $Title = "訪問者追跡";
	&PrintResult($Title, $PrintStr);

}

sub ActivityByDayOfTheMonth {
	if(-e "$LOGDIR/$TARGET_LOGNAME") {
		open(LOGFILE, "$LOGDIR/$TARGET_LOGNAME") || &ErrorPrint("アクセスログ「$LOGDIR/$TARGET_LOGNAME」をオープンできませんでした");
	} else {
		&ErrorPrint("アクセスログ（$LOGDIR/$TARGET_LOGNAME）がありません。");
	}
	my $by = $q->param('by');
	if($by !~ /^(1|2|3)$/) { $by = 1; }
	my $i = 0;
	my(%date, %remote_host, %cookies, $lastdate);
	while(<LOGFILE>) {
		chomp;
		my($date_part, $host_part, $cookie_part);
		if(/^(\d{14})\s+(\S+)\s+(\S+)\s+/) {
			$date_part = $1;
			$host_part = $2;
			$cookie_part = $3;
		} else {
			next;
		}
		next if($date_part eq '');
		next unless(&IsInDate($date_part));
		$date{$i} = $date_part;
		$lastdate = $date_part;
		$remote_host{$i} = $host_part;
		$cookies{$i} = $cookie_part;
		$i ++;
	}
	close(LOGFILE);
	my($ThisYear, $ThisMonth) = $lastdate =~ /^(\d{4})(\d{2})/;
	my($Y, $M, $D, @DateBuff);
	my %LogNoList;
	while( my($i, $d) = each %date ) {
		unless($d) {next;}
		if($d eq '-') {next;}
		unless($ANA_MONTH) {
			unless($d =~ /^$ThisYear$ThisMonth/) {next;}
		}
		($Y, $M, $D) = $d =~ /^(\d{4})(\d{2})(\d{2})/;
		$D += 0;
		push(@{$LogNoList{$D}}, $i);
	}
	my $LastDayOfThisMonth = &LastDay($ThisYear, $ThisMonth);
	my %SessionNumList;
	my %PageViewNumList;
	my %UniqueNumList;
	my @DateList;
	my @WeekMap = ('日', '月', '火', '水', '木', '金', '土');
	if($by == 1) {
		for (my $i=0;$i<$LastDayOfThisMonth;$i++) {
			my $Date = $i + 1;
			eval { $PageViewNumList{$i} = scalar @{$LogNoList{$Date}}; };
			if($@) { $PageViewNumList{$i} = 0; }
			my $WeekNum = &Youbi($ThisYear, $ThisMonth, $Date);
			my $DateStr = "$ThisYear年$ThisMonth月$Date日（";
			if($WeekNum == 0) {
				$DateStr .= "<font color=\"#FF0000\">$WeekMap[$WeekNum]</font>）";
			} elsif($WeekNum == 6) {
				$DateStr .= "<font color=\"#0000FF\">$WeekMap[$WeekNum]</font>）";
			} else {
				$DateStr .= "$WeekMap[$WeekNum]）";
			}
			push(@DateList, $DateStr);
		}
	} elsif($by == 2) {
		for (my $i=0;$i<$LastDayOfThisMonth;$i++) {
			my $Date = $i + 1;
			$SessionNumList{$i} = &GetSessionNum($LogNoList{$Date}, \%date, \%remote_host, \%cookies);
			my $WeekNum = &Youbi($ThisYear, $ThisMonth, $Date);
			my $DateStr = "$ThisYear年$ThisMonth月$Date日（";
			if($WeekNum == 0) {
				$DateStr .= "<font color=\"#FF0000\">$WeekMap[$WeekNum]</font>）";
			} elsif($WeekNum == 6) {
				$DateStr .= "<font color=\"#0000FF\">$WeekMap[$WeekNum]</font>）";
			} else {
				$DateStr .= "$WeekMap[$WeekNum]）";
			}
			push(@DateList, $DateStr);
		}
	} elsif($by == 3) {
		for (my $i=0;$i<$LastDayOfThisMonth;$i++) {
			my $Date = $i + 1;
			$UniqueNumList{$i} = &GetUniqueUserNum($LogNoList{$Date}, \%remote_host, \%cookies);
			my $WeekNum = &Youbi($ThisYear, $ThisMonth, $Date);
			my $DateStr = "$ThisYear年$ThisMonth月$Date日（";
			if($WeekNum == 0) {
				$DateStr .= "<font color=\"#FF0000\">$WeekMap[$WeekNum]</font>）";
			} elsif($WeekNum == 6) {
				$DateStr .= "<font color=\"#0000FF\">$WeekMap[$WeekNum]</font>）";
			} else {
				$DateStr .= "$WeekMap[$WeekNum]）";
			}
			push(@DateList, $DateStr);
		}
	}
	undef %LogNoList;
	undef %date;
	undef %remote_host;
	undef %cookies;
	my $Str;
	$Str .= "<h2>解析条件指定</h2>\n";
	$Str .= "<form action=\"${CGI_URL}\" method=\"post\">\n";
	$Str .= "<input type=\"hidden\" name=\"FRAME\" value=\"result\" />\n";
	$Str .= "<input type=\"hidden\" name=\"ITEM\" value=\"ActivityByDayOfTheMonth\" />\n";
	$Str .= "<input type=\"hidden\" name=\"LOG\" value=\"${TARGET_LOGNAME}\" />\n";
	if($ANA_MONTH) {
		$Str .= "<input type=\"hidden\" name=\"MONTH\" value=\"${ANA_MONTH}\" />\n";
		if($ANA_DAY) {
			$Str .= "<input type=\"hidden\" name=\"DAY\" value=\"${ANA_DAY}\" />\n";
		}
	}
	$Str .= "<h3>■ 解析モード</h3>\n";
	my($by1_checked, $by2_checked, $by3_checked);
	if($by == 2) {
		$by2_checked = 'checked="checked "';
	} elsif($by == 3) {
		$by3_checked = 'checked="checked "';
	} else {
		$by1_checked = 'checked="checked "';
	}
	$Str .= "<div><input type=\"radio\" name=\"by\" value=\"1\" ${by1_checked}/> インプレッション数　　<input type=\"radio\" name=\"by\" value=\"2\" ${by2_checked}/> セッション数　　<input type=\"radio\" name=\"by\" value=\"3\" ${by3_checked}/> ユニークユーザー数</div>\n";
	$Str .= "<div>　</div>\n";
	$Str .= "<input type=\"submit\" name=\"b1\" value=\"　解　析　\" />\n";
	$Str .= "</form>\n";
	my @Titles;
	if($by == 1) {
		$Str .= "<h2>インプレッション数</h2>\n";
		@Titles = ('日付', 'インプレッション数', 'グラフ');
		$Str .= &MakeGraph3(\%PageViewNumList, \@Titles, \@DateList);
		undef %PageViewNumList;
	}
	if($by == 2) {
		$Str .= "<h2>セッション数</h2>\n";
		@Titles = ('日付', 'セッション数', 'グラフ');
		$Str .= &MakeGraph3(\%SessionNumList, \@Titles, \@DateList);
		undef %SessionNumList;
	}
	if($by == 3) {
		$Str .= "<h2>ユニークユーザー数</h2>\n";
		@Titles = ('日付', 'ユニークユーザー数', 'グラフ');
		$Str .= &MakeGraph3(\%UniqueNumList, \@Titles, \@DateList);
		undef %UniqueNumList;
	}
	my $Title = '日別アクセス数';
	&PrintResult($Title, $Str);
}


sub ActivityByDayOfTheWeek {
	if(-e "$LOGDIR/$TARGET_LOGNAME") {
		open(LOGFILE, "$LOGDIR/$TARGET_LOGNAME") || &ErrorPrint("アクセスログ「$LOGDIR/$TARGET_LOGNAME」をオープンできませんでした");
	} else {
		&ErrorPrint("アクセスログ（$LOGDIR/$TARGET_LOGNAME）がありません。");
	}
	#
	my $by = $q->param('by');
	if($by !~ /^(1|2|3)$/) { $by = 1; }
	my $in_avrg = $q->param('avrg');
	if($in_avrg ne '' && $in_avrg ne '1') {
		&ErrorPrint('不正なパラメータが送信されました。');
	}
	#
	my $i = 0;
	my(%date, %remote_host, %cookies);
	while(<LOGFILE>) {
		chomp;
		my($date_part, $host_part, $cookie_part);
		if(/^(\d{14})\s+(\S+)\s+(\S+)\s+/) {
			$date_part = $1;
			$host_part = $2;
			$cookie_part = $3;
		} else {
			next;
		}
		next if($date_part eq '');
		next unless(&IsInDate($date_part));
		$date{$i} = $date_part;
		$remote_host{$i} = $host_part;
		$cookies{$i} = $cookie_part;
		$i ++;
	}
	close(LOGFILE);
	my %LogNoList;
	my %week_day_num_hash;
	while( my($i, $d) = each %date ) {
		unless($d) {next;}
		if($d eq '-') {next;}
		my($Y, $M, $D) = $d =~ /^(\d{4})(\d{2})(\d{2})/;
		my $ymd = "${Y}${M}${D}";
		$M =~ s/^0//;
		$D =~ s/^0//;
		my @tm = localtime(timelocal(0, 0, 0, $D, $M - 1, $Y));
		push(@{$LogNoList{$tm[6]}}, $i);
		$week_day_num_hash{$tm[6]}->{$ymd} ++;
	}
	my %week_day_num;
	for(my $i=0; $i<=6; $i++) {
		my $n = scalar keys %{$week_day_num_hash{$i}};
		if($n == 0) { $n = 1; }
		$week_day_num{$i} = $n;
	}
	my %SessionNumList;
	my %PageViewNumList;
	my %UniqueNumList;
	if($by == 1) {
		for (my $Youbi=0;$Youbi<7;$Youbi++) {
			my $Views = 0;
			eval { $Views = scalar @{$LogNoList{$Youbi}}; };
			if($@) { $Views = 0; }
			if($in_avrg) {
				$Views = &RoundOff($Views / $week_day_num{$Youbi}, 1);
				$Views = sprintf("%9.1f", $Views);
			}
			$PageViewNumList{$Youbi} = $Views;
		}
	} elsif($by == 2) {
		for (my $Youbi=0;$Youbi<7;$Youbi++) {
			my $Sessions = &GetSessionNum($LogNoList{$Youbi}, \%date, \%remote_host, \%cookies);
			if($in_avrg) {
				$Sessions = &RoundOff($Sessions / $week_day_num{$Youbi}, 1);
				$Sessions = sprintf("%9.1f", $Sessions);
			}
			$SessionNumList{$Youbi} = $Sessions;
		}
	} elsif($by == 3) {
		for (my $Youbi=0;$Youbi<7;$Youbi++) {
			my $Unique = &GetUniqueUserNum($LogNoList{$Youbi}, \%remote_host, \%cookies);
			if($in_avrg) {
				$Unique = &RoundOff($Unique / $week_day_num{$Youbi}, 1);
				$Unique = sprintf("%9.1f", $Unique);
			}
			$UniqueNumList{$Youbi} = $Unique;
		}
	}
	undef %LogNoList;
	undef %date;
	undef %remote_host;
	undef %cookies;
	my $Str;
	$Str .= "<h2>解析条件指定</h2>\n";
	$Str .= "<form action=\"${CGI_URL}\" method=\"post\">\n";
	$Str .= "<input type=\"hidden\" name=\"FRAME\" value=\"result\" />\n";
	$Str .= "<input type=\"hidden\" name=\"ITEM\" value=\"ActivityByDayOfTheWeek\" />\n";
	$Str .= "<input type=\"hidden\" name=\"LOG\" value=\"${TARGET_LOGNAME}\" />\n";
	if($ANA_MONTH) {
		$Str .= "<input type=\"hidden\" name=\"MONTH\" value=\"${ANA_MONTH}\" />\n";
		if($ANA_DAY) {
			$Str .= "<input type=\"hidden\" name=\"DAY\" value=\"${ANA_DAY}\" />\n";
		}
	}
	$Str .= "<h3>■ 解析モード</h3>\n";
	my($by1_checked, $by2_checked, $by3_checked);
	if($by == 2) {
		$by2_checked = 'checked="checked "';
	} elsif($by == 3) {
		$by3_checked = 'checked="checked "';
	} else {
		$by1_checked = 'checked="checked "';
	}
	$Str .= "<div><input type=\"radio\" name=\"by\" value=\"1\" ${by1_checked}/> インプレッション数　　<input type=\"radio\" name=\"by\" value=\"2\" ${by2_checked}/> セッション数　　<input type=\"radio\" name=\"by\" value=\"3\" ${by3_checked}/> ユニークユーザー数</div>\n";
	$Str .= "<div>　</div>\n";
	$Str .= "<h3>■ オプション</h3>\n";
	my $avrg_checked = '';
	if($in_avrg) {
		$avrg_checked = ' checked="checked"';
	}
	$Str .= "<input type=\"checkbox\" name=\"avrg\" value=\"1\"${avrg_checked} /> 曜日毎の1日の平均で解析する　\n";
	$Str .= "<div>　</div>\n";
	$Str .= "<input type=\"submit\" name=\"b1\" value=\"　解　析　\" />\n";
	$Str .= "</form>\n";
	my $mode_desc = "<div>解析オプション ： 解析期間における合計値</div>\n";
	if($in_avrg) {
		$mode_desc = "<div>解析オプション ： 曜日毎の1日の平均値</div>\n";
	}
	my @Titles;
	my @WeekMap = ('日', '月', '火', '水', '木', '金', '土');
	if($by == 1) {
		$Str .= "<h2>インプレッション数</h2>\n";
		$Str .= $mode_desc;
		@Titles = ('曜日', 'インプレッション数', 'グラフ');
		$Str .= &MakeGraph3(\%PageViewNumList, \@Titles, \@WeekMap);
		undef %PageViewNumList;
	}
	if($by == 2) {
		$Str .= "<h2>セッション数</h2>\n";
		$Str .= $mode_desc;
		@Titles = ('曜日', 'セッション数', 'グラフ');
		$Str .= &MakeGraph3(\%SessionNumList, \@Titles, \@WeekMap);
		undef %SessionNumList;
	}
	if($by == 3) {
		$Str .= "<h2>ユニークユーザー数</h2>\n";
		$Str .= $mode_desc;
		@Titles = ('曜日', 'ユニークユーザー数', 'グラフ');
		$Str .= &MakeGraph3(\%UniqueNumList, \@Titles, \@WeekMap);
		undef %UniqueNumList;
	}
	my $Title = '曜日別アクセス数';
	&PrintResult($Title, $Str);
}

sub ActivityByHourOfTheDay {
	if(-e "$LOGDIR/$TARGET_LOGNAME") {
		open(LOGFILE, "$LOGDIR/$TARGET_LOGNAME") || &ErrorPrint("アクセスログ「$LOGDIR/$TARGET_LOGNAME」をオープンできませんでした");
	} else {
		&ErrorPrint("アクセスログ（$LOGDIR/$TARGET_LOGNAME）がありません。");
	}
	my $by = $q->param('by');
	if($by !~ /^(1|2|3)$/) { $by = 1; }
	my $i = 0;
	my(%date, %remote_host, %cookies);
	while(<LOGFILE>) {
		chomp;
		my($date_part, $host_part, $cookie_part);
		if(/^(\d{14})\s+(\S+)\s+(\S+)\s+/) {
			$date_part = $1;
			$host_part = $2;
			$cookie_part = $3;
		} else {
			next;
		}
		next if($date_part eq '');
		next unless(&IsInDate($date_part));
		$date{$i} = $date_part;
		$remote_host{$i} = $host_part;
		$cookies{$i} = $cookie_part;
		$i ++;
	}
	close(LOGFILE);
	my %LogNoList;
	while( my($i, $d) = each %date ) {
		unless($d) {next;}
		if($d eq '-') {next;}
		my $h = substr($d, 8, 2);
		$h += 0;
		push(@{$LogNoList{$h}}, $i);
	}
	my %SessionNumList;
	my %PageViewNumList;
	my %UniqueNumList;
	if($by == 1) {
		for (my $h=0;$h<24;$h++) {
			eval { $PageViewNumList{$h} = scalar @{$LogNoList{$h}}; };
			if($@) { $PageViewNumList{$h} = 0; }
		}
	} elsif($by == 2) {
		for (my $h=0;$h<24;$h++) {
			$SessionNumList{$h} = &GetSessionNum($LogNoList{$h}, \%date, \%remote_host, \%cookies);
		}
	} elsif($by == 3) {
		for (my $h=0;$h<24;$h++) {
			$UniqueNumList{$h} = &GetUniqueUserNum($LogNoList{$h}, \%remote_host, \%cookies);
		}
	}
	undef %date;
	undef %remote_host;
	undef %cookies;
	my $Str;
	$Str .= "<h2>解析条件指定</h2>\n";
	$Str .= "<form action=\"${CGI_URL}\" method=\"post\">\n";
	$Str .= "<input type=\"hidden\" name=\"FRAME\" value=\"result\" />\n";
	$Str .= "<input type=\"hidden\" name=\"ITEM\" value=\"ActivityByHourOfTheDay\" />\n";
	$Str .= "<input type=\"hidden\" name=\"LOG\" value=\"${TARGET_LOGNAME}\" />\n";
	if($ANA_MONTH) {
		$Str .= "<input type=\"hidden\" name=\"MONTH\" value=\"${ANA_MONTH}\" />\n";
		if($ANA_DAY) {
			$Str .= "<input type=\"hidden\" name=\"DAY\" value=\"${ANA_DAY}\" />\n";
		}
	}
	$Str .= "<h3>■ 解析モード</h3>\n";
	my($by1_checked, $by2_checked, $by3_checked);
	if($by == 2) {
		$by2_checked = 'checked="checked "';
	} elsif($by == 3) {
		$by3_checked = 'checked="checked "';
	} else {
		$by1_checked = 'checked="checked "';
	}
	$Str .= "<div><input type=\"radio\" name=\"by\" value=\"1\" ${by1_checked}/> インプレッション数　　<input type=\"radio\" name=\"by\" value=\"2\" ${by2_checked}/> セッション数　　<input type=\"radio\" name=\"by\" value=\"3\" ${by3_checked}/> ユニークユーザー数</div>\n";
	$Str .= "<div>　</div>\n";
	$Str .= "<input type=\"submit\" name=\"b1\" value=\"　解　析　\" />\n";
	$Str .= "</form>\n";
	my @Titles;
	if($by == 1) {
		$Str .= "<h2>インプレッション数</h2>\n";
		@Titles = ('時間', 'インプレッション数', 'グラフ');
		$Str .= &MakeGraph3(\%PageViewNumList, \@Titles);
		undef %PageViewNumList;
	}
	if($by == 2) {
		$Str .= "<h2>セッション数</h2>\n";
		@Titles = ('時間', 'セッション数', 'グラフ');
		$Str .= &MakeGraph3(\%SessionNumList, \@Titles);
		undef %SessionNumList;
	}
	if($by == 3) {
		$Str .=	"<h2>ユニークユーザー数</h2>\n";
		@Titles = ('時間', 'ユニークユーザー数', 'グラフ');
		$Str .= &MakeGraph3(\%UniqueNumList, \@Titles);
		undef %UniqueNumList;
	}
	my $Title = '時間別アクセス数';
	&PrintResult($Title, $Str);
}

sub TopReferringSites {
	if(-e "$LOGDIR/$TARGET_LOGNAME") {
		open(LOGFILE, "$LOGDIR/$TARGET_LOGNAME") || &ErrorPrint("アクセスログ「$LOGDIR/$TARGET_LOGNAME」をオープンできませんでした");
	} else {
		&ErrorPrint("アクセスログ（$LOGDIR/$TARGET_LOGNAME）がありません。");
	}
	my $i = 0;
	my(%date, %remote_host, %cookies, %referer);
	while(<LOGFILE>) {
		chomp;
		my($date_part, $referer_part, $host_part, $cookie_part);
		if(/^(\d{14})\s+(\S+)\s+(\S+)\s+\S+\s+\S+\s+(\S+)\s+/) {
			$date_part = $1;
			$host_part = $2;
			$cookie_part = $3;
			$referer_part = $4;
		} else {
			next;
		}
		next if($date_part eq '');
		next unless(&IsInDate($date_part));
		$date{$i} = $date_part;
		$remote_host{$i} = $host_part;
		$cookies{$i} = $cookie_part;
		$referer{$i} = $referer_part;
		$i ++;
	}
	close(LOGFILE);
	my %LogNoList;
	my %SiteList = &ReadDef('./data/site.dat');
	while( my($i, $r) = each %referer ) {
		unless($r) {next;}
		if($r eq '-') {next;}
		my $f = 0;
		if(scalar @MY_SITE_URLs) {
			for my $k (@MY_SITE_URLs) {
				if($r =~ /^${k}/i) {
					$f = 1;
					last;
				}
			}
		}
		if($f) {next;}
		my $host = lc $r;
		my @url_parts = split(/\//, $host);
		my $site_url = "$url_parts[0]//$url_parts[2]/";
		my $SiteName = &GetSiteName(\%SiteList, $site_url, 1);
		my $EncodedUrl = &URL_Encode($site_url);
		my $LinkUrl = "${CGI_URL}?REDIRECT=${EncodedUrl}";
		my $tag;
		if($SiteName) {
			$tag = "<div>$SiteName</div>";
			$tag .= "<div class=\"size2\"><a href=\"${LinkUrl}\" target=\"_blank\">${site_url}</a></div>";
		} else {
			$tag = "<a href=\"${LinkUrl}\" target=\"_blank\">${site_url}</a>";
		}
		push(@{$LogNoList{$tag}}, $i);
	}
	undef %SiteList;
	my %SessionNumList;
	while( my($key, $aref) = each %LogNoList ) {
		$SessionNumList{$key} = &GetSessionNum($aref, \%date, \%remote_host, \%cookies);
	}
	undef %LogNoList;
	undef %date;
	undef %remote_host;
	undef %cookies;
	undef %referer;
	my @Titles = ('順位', 'URL', '訪問数', 'グラフ');
	my $Str = &MakeGraph(\%SessionNumList, \@Titles);
	undef %SessionNumList;
	my $Title = 'リンク元サイトランキング';
	&PrintResult($Title, $Str);
}

sub GetSiteName {
	my($site_hash_ref, $url, $site_flag) = @_;
	if($site_flag) {
		my @url_parts = split(/\//, $url);
		$url = $url_parts[2];
	}
	my $domain;
	my $hit_domain;
	while( my($domain, $dummy) = each %{$site_hash_ref} ) {
		if($site_flag) {
			if($domain =~ /\//) {next;}
		}
		if($url =~ /$domain/) {
			if(length($domain) > length($hit_domain)) {
				$hit_domain = $domain;
			}
		}
	}
	return $site_hash_ref->{$hit_domain};
}

sub TopReferringURLs {
	if(-e "$LOGDIR/$TARGET_LOGNAME") {
		open(LOGFILE, "$LOGDIR/$TARGET_LOGNAME") || &ErrorPrint("アクセスログ「$LOGDIR/$TARGET_LOGNAME」をオープンできませんでした");
	} else {
		&ErrorPrint("アクセスログ（$LOGDIR/$TARGET_LOGNAME）がありません。");
	}
	my $i = 0;
	my(%date, %remote_host, %cookies, %referer);
	while(<LOGFILE>) {
		chomp;
		my($date_part, $referer_part, $host_part, $cookie_part);
		if(/^(\d{14})\s+(\S+)\s+(\S+)\s+\S+\s+\S+\s+(\S+)\s+/) {
			$date_part = $1;
			$host_part = $2;
			$cookie_part = $3;
			$referer_part = $4;
		} else {
			next;
		}
		next if($date_part eq '');
		next unless(&IsInDate($date_part));
		$date{$i} = $date_part;
		$remote_host{$i} = $host_part;
		$cookies{$i} = $cookie_part;
		$referer{$i} = $referer_part;
		$i ++;
	}
	close(LOGFILE);
	my %LogNoList;
	my %SiteList = &ReadDef('./data/site.dat');
	while( my($i, $r) = each %referer ) {
		unless($r) {next;}
		if($r eq '-') {next;}
		my $f = 0;
		if(scalar @MY_SITE_URLs) {
			my $ExceptUrl;
			for $ExceptUrl (@MY_SITE_URLs) {
				if($r =~ /^$ExceptUrl/i) {
					$f = 1;
					last;
				}
			}
		}
		if($f) {next;}
		my $DspUrl;
		if(length($r) > 50) {
			$DspUrl = substr($r, 0, 50);
			$DspUrl .= '...';
		} else {
			$DspUrl = $r;
		}
		my $SiteName = &GetSiteName(\%SiteList, $r);
		my $KeyStr;
		my $EncodedUrl = &URL_Encode($r);
		my $LinkUrl = "${CGI_URL}?REDIRECT=${EncodedUrl}";
		$DspUrl =~ s/\&/\&amp;/g;
		$LinkUrl =~ s/\&/\&amp;/g;
		my $tag;
		if($SiteName) {
			$tag = "<div>$SiteName</div>";
			$tag .= "<div class=\"size2\"><a href=\"$LinkUrl\" target=\"_blank\">$DspUrl</a></div>";
		} else {
			$tag = "<a href=\"$LinkUrl\" target=\"_blank\">$DspUrl</a>";
		}
		push(@{$LogNoList{$tag}}, $i);
	}
	undef %SiteList;
	my %SessionNumList;
	while( my($key, $aref) = each %LogNoList ) {
		$SessionNumList{$key} = &GetSessionNum($aref, \%date, \%remote_host, \%cookies);
	}
	undef %LogNoList;
	undef %date;
	undef %remote_host;
	undef %cookies;
	undef %referer;
	my @Titles = ('順位', 'URL', '訪問数', 'グラフ');
	my $Str = &MakeGraph(\%SessionNumList, \@Titles);
	undef %SessionNumList;
	my $Title = 'リンク元URLランキング';
	&PrintResult($Title, $Str);
}

sub TopSearchKeywords {
	if(-e "$LOGDIR/$TARGET_LOGNAME") {
		open(LOGFILE, "$LOGDIR/$TARGET_LOGNAME") || &ErrorPrint("アクセスログ「$LOGDIR/$TARGET_LOGNAME」をオープンできませんでした");
	} else {
		&ErrorPrint("アクセスログ（$LOGDIR/$TARGET_LOGNAME）がありません。");
	}
	my $in_req = $q->param('req');
	my $in_split = $q->param('split');
	if($in_split ne '' && $in_split ne '1') {
		&ErrorPrint('不正なパラメータが送信されました。');
	}
	my $in_capital = $q->param('capital');
	if($in_capital ne '' && $in_capital ne '1') {
		&ErrorPrint('不正なパラメータが送信されました。');
	}
	my $in_zenhan = $q->param('zenhan');
	if($in_zenhan ne '' && $in_zenhan ne '1') {
		&ErrorPrint('不正なパラメータが送信されました。');
	}
	my $i = 0;
	my(%referer, %req);
	while(<LOGFILE>) {
		chomp;
		my($date_part, $referer_part, $req_part);
		if(/^(\d{14})\s+\S+\s+\S+\s+\S+\s+(\S+)\s+(\S+)\s+/) {
			$date_part = $1;
			$req_part = $2;
			$referer_part = $3;
		} else {
			next;
		}
		next if($date_part eq '');
		next unless(&IsInDate($date_part));
		$referer{$i} = $referer_part;
		if($CONF{'URLHANDLE'}) {
			$req_part =~ s/\?.*$//;
		}
		$req{$i} = $req_part;
		$i ++;
	}
	close(LOGFILE);
	my %KeywordCount;
	my %req_list;
	while( my($i, $r) = each %referer ) {
		if($r eq '' || $r eq '-') {
			next;
		}
		my($keyword) = &GetSearchKeyword($r);
		if($keyword ne '') {
			if($in_req eq '' || ($in_req ne '' && $in_req eq $req{$i})) {
				$keyword =~ s/</&lt;/g;
				$keyword =~ s/>/&gt;/g;
				$keyword =~ s/　/ /g;
				$keyword =~ s/\s+/ /g;
				$keyword =~ s/^\s+//;
				$keyword =~ s/\s+$//;
				if($in_zenhan) {
					$keyword = &alpha_num_z2h($keyword);
					$keyword = &kana_h2z($keyword);
				}
				if($in_capital) {
					$keyword = lc $keyword;
				}
				if($in_split) {
					my @words = split(/\s/, $keyword);
					for my $w (@words) {
						$KeywordCount{$w} ++;
					}
				} else {
					$KeywordCount{$keyword} ++;
				}
			}
			$req_list{$req{$i}} ++;
		}
	}
	undef %referer;
	undef %req;
	#ページ選択プルダウン選択
	my $Str;
	$Str .= "<h2>解析条件指定</h2>\n";
	$Str .= "<form action=\"${CGI_URL}\" method=\"post\">\n";
	$Str .= "<input type=\"hidden\" name=\"FRAME\" value=\"result\" />\n";
	$Str .= "<input type=\"hidden\" name=\"ITEM\" value=\"TopSearchKeywords\" />\n";
	$Str .= "<input type=\"hidden\" name=\"LOG\" value=\"${TARGET_LOGNAME}\" />\n";
	if($ANA_MONTH) {
		$Str .= "<input type=\"hidden\" name=\"MONTH\" value=\"${ANA_MONTH}\" />\n";
		if($ANA_DAY) {
			$Str .= "<input type=\"hidden\" name=\"DAY\" value=\"${ANA_DAY}\" />\n";
		}
	}
	$Str .= "<h3>■ ページ絞り込み</h3>\n";
	$Str .= "<div><select name=\"req\">\n";
	$Str .= "<option value=\"\">指定なし</option>\n";
	for my $r (sort {$req_list{$b} <=> $req_list{$a}} keys %req_list) {
		my $selected = '';
		if($r eq $in_req) {
			$selected = ' selected="selected"';
		}
		my $r2 = $r;
		if(length($r2) > 80) {
			$r2 = substr($r, 0, 77) . '...';
		}
		$r2 = &SecureHtml($r2);
		my $r1 = &SecureHtml($r);
		my $n = $req_list{$r};
		$Str .= "<option value=\"${r1}\"${selected}>${r2} (${n})</option>\n";
	}
	undef %req_list;
	$Str .= "</select></div>\n";
	$Str .= "<div>　</div>\n";
	$Str .= "<h3>■ オプション</h3>\n";
	my $split_checked = '';
	if($in_split) {
		$split_checked = ' checked="checked"';
	}
	my $capital_checked = '';
	if($in_capital) {
		$capital_checked = ' checked="checked"';
	}
	my $zenhan_checked = '';
	if($in_zenhan) {
		$zenhan_checked = ' checked="checked"';
	}

	$Str .= "<input type=\"checkbox\" name=\"split\" value=\"1\"${split_checked} /> 単語別に解析する　\n";
	$Str .= "<input type=\"checkbox\" name=\"capital\" value=\"1\"${capital_checked} /> 大文字・小文字を区別しない　\n";
	$Str .= "<input type=\"checkbox\" name=\"zenhan\" value=\"1\"${zenhan_checked} /> 半角・全角を区別しない　\n";

	$Str .= "<div>　</div>\n";
	$Str .= "<input type=\"submit\" name=\"b1\" value=\"　解　析　\" />\n";
	$Str .= "</form>\n";
	$Str .= "<h2>ランキング</h2>\n";
	my $target_url = &SecureHtml($in_req);
	if($in_req eq '') {
		$target_url = '全ページ';
	}
	$Str .= "<div>対象ページ ： ${target_url}</div>\n";
	if($in_split) {
		$Str .= "<div>単語分割解析 ： 有効</div>\n";
	} else {
		$Str .= "<div>単語分割解析 ： 無効</div>\n";
	}
	my @Titles = ('順位', 'キーワード', '訪問数', 'グラフ');
	$Str .= &MakeGraph(\%KeywordCount, \@Titles);
	my $Title = '検索キーワードランキング';
	&PrintResult($Title, $Str);
}

sub alpha_num_z2h {
	my($str) = @_;
	my %map = (
		"\xef\xbc\x90" => "0", "\xef\xbc\x91" => "1", "\xef\xbc\x92" => "2", "\xef\xbc\x93" => "3",
		"\xef\xbc\x94" => "4", "\xef\xbc\x95" => "5", "\xef\xbc\x96" => "6", "\xef\xbc\x97" => "7",
		"\xef\xbc\x98" => "8", "\xef\xbc\x99" => "9", "\xef\xbc\xa1" => "A", "\xef\xbc\xa2" => "B",
		"\xef\xbc\xa3" => "C", "\xef\xbc\xa4" => "D", "\xef\xbc\xa5" => "E", "\xef\xbc\xa6" => "F",
		"\xef\xbc\xa7" => "G", "\xef\xbc\xa8" => "H", "\xef\xbc\xa9" => "I", "\xef\xbc\xaa" => "J",
		"\xef\xbc\xab" => "K", "\xef\xbc\xac" => "L", "\xef\xbc\xad" => "M", "\xef\xbc\xae" => "N",
		"\xef\xbc\xaf" => "O", "\xef\xbc\xb0" => "P", "\xef\xbc\xb1" => "Q", "\xef\xbc\xb2" => "R",
		"\xef\xbc\xb3" => "S", "\xef\xbc\xb4" => "T", "\xef\xbc\xb5" => "U", "\xef\xbc\xb6" => "V",
		"\xef\xbc\xb7" => "W", "\xef\xbc\xb8" => "X", "\xef\xbc\xb9" => "Y", "\xef\xbc\xba" => "Z",
		"\xef\xbd\x81" => "a", "\xef\xbd\x82" => "b", "\xef\xbd\x83" => "c", "\xef\xbd\x84" => "d",
		"\xef\xbd\x85" => "e", "\xef\xbd\x86" => "f", "\xef\xbd\x87" => "g", "\xef\xbd\x88" => "h",
		"\xef\xbd\x89" => "i", "\xef\xbd\x8a" => "j", "\xef\xbd\x8b" => "k", "\xef\xbd\x8c" => "l",
		"\xef\xbd\x8d" => "m", "\xef\xbd\x8e" => "n", "\xef\xbd\x8f" => "o", "\xef\xbd\x90" => "p",
		"\xef\xbd\x91" => "q", "\xef\xbd\x92" => "r", "\xef\xbd\x93" => "s", "\xef\xbd\x94" => "t",
		"\xef\xbd\x95" => "u", "\xef\xbd\x96" => "v", "\xef\xbd\x97" => "w", "\xef\xbd\x98" => "x",
		"\xef\xbd\x99" => "y", "\xef\xbd\x9a" => "z"
	);
	$str =~ s/(\xef\xbc([\x90-\x99]|[\xa1-\xba])|\xef\xbd[\x81-\x9a])/$map{$1}/g;
	return $str;
}

sub kana_h2z {
	my($str) = @_;
	my %map1 = (
		"\xef\xbd\xb3\xef\xbe\x9e" => "\xe3\x83\xb4", "\xef\xbd\xb6\xef\xbe\x9e" => "\xe3\x82\xac",
		"\xef\xbd\xb7\xef\xbe\x9e" => "\xe3\x82\xae", "\xef\xbd\xb8\xef\xbe\x9e" => "\xe3\x82\xb0",
		"\xef\xbd\xb9\xef\xbe\x9e" => "\xe3\x82\xb2", "\xef\xbd\xba\xef\xbe\x9e" => "\xe3\x82\xb4",
		"\xef\xbd\xbb\xef\xbe\x9e" => "\xe3\x82\xb6", "\xef\xbd\xbc\xef\xbe\x9e" => "\xe3\x82\xb8",
		"\xef\xbd\xbd\xef\xbe\x9e" => "\xe3\x82\xba", "\xef\xbd\xbe\xef\xbe\x9e" => "\xe3\x82\xbc",
		"\xef\xbd\xbf\xef\xbe\x9e" => "\xe3\x82\xbe", "\xef\xbe\x80\xef\xbe\x9e" => "\xe3\x83\x80",
		"\xef\xbe\x81\xef\xbe\x9e" => "\xe3\x83\x82", "\xef\xbe\x82\xef\xbe\x9e" => "\xe3\x83\x85",
		"\xef\xbe\x83\xef\xbe\x9e" => "\xe3\x83\x87", "\xef\xbe\x84\xef\xbe\x9e" => "\xe3\x83\x89",
		"\xef\xbe\x8a\xef\xbe\x9e" => "\xe3\x83\x90", "\xef\xbe\x8a\xef\xbe\x9f" => "\xe3\x83\x91",
		"\xef\xbe\x8b\xef\xbe\x9e" => "\xe3\x83\x93", "\xef\xbe\x8b\xef\xbe\x9f" => "\xe3\x83\x94",
		"\xef\xbe\x8c\xef\xbe\x9e" => "\xe3\x83\x96", "\xef\xbe\x8c\xef\xbe\x9f" => "\xe3\x83\x97",
		"\xef\xbe\x8d\xef\xbe\x9e" => "\xe3\x83\x99", "\xef\xbe\x8d\xef\xbe\x9f" => "\xe3\x83\x9a",
		"\xef\xbe\x8e\xef\xbe\x9e" => "\xe3\x83\x9c", "\xef\xbe\x8e\xef\xbe\x9f" => "\xe3\x83\x9d"
	);
	$str =~ s/(\xef\xbd\xb3\xef\xbe\x9e|\xef\xbd[\xb6-\xbf]\xef\xbe\x9e|\xef\xbe[\x80-\x84]\xef\xbe\x9e|\xef\xbe(\x8a\xef\xbe\x9e|\x8a\xef\xbe\x9f|\x8b\xef\xbe\x9e|\x8b\xef\xbe\x9f|\x8c\xef\xbe\x9e|\x8c\xef\xbe\x9f|\x8d\xef\xbe\x9e|\x8d\xef\xbe\x9f|\x8e\xef\xbe\x9e|\x8e\xef\xbe\x9f))/$map1{$1}/g;
	my %map2 = (
		"\xef\xbd\xa1" => "\xe3\x80\x82", "\xef\xbd\xa2" => "\xe3\x80\x8c",
		"\xef\xbd\xa3" => "\xe3\x80\x8d", "\xef\xbd\xa4" => "\xe3\x80\x81",
		"\xef\xbd\xa5" => "\xe3\x83\xbb", "\xef\xbd\xa6" => "\xe3\x83\xb2",
		"\xef\xbd\xa7" => "\xe3\x82\xa1", "\xef\xbd\xa8" => "\xe3\x82\xa3",
		"\xef\xbd\xa9" => "\xe3\x82\xa5", "\xef\xbd\xaa" => "\xe3\x82\xa7",
		"\xef\xbd\xab" => "\xe3\x82\xa9", "\xef\xbd\xac" => "\xe3\x83\xa3",
		"\xef\xbd\xad" => "\xe3\x83\xa5", "\xef\xbd\xae" => "\xe3\x83\xa7",
		"\xef\xbd\xaf" => "\xe3\x83\x83", "\xef\xbd\xb0" => "\xe3\x83\xbc",
		"\xef\xbd\xb1" => "\xe3\x82\xa2", "\xef\xbd\xb2" => "\xe3\x82\xa4",
		"\xef\xbd\xb3" => "\xe3\x82\xa6", "\xef\xbd\xb4" => "\xe3\x82\xa8",
		"\xef\xbd\xb5" => "\xe3\x82\xaa", "\xef\xbd\xb6" => "\xe3\x82\xab",
		"\xef\xbd\xb7" => "\xe3\x82\xad", "\xef\xbd\xb8" => "\xe3\x82\xaf",
		"\xef\xbd\xb9" => "\xe3\x82\xb1", "\xef\xbd\xba" => "\xe3\x82\xb3",
		"\xef\xbd\xbb" => "\xe3\x82\xb5", "\xef\xbd\xbc" => "\xe3\x82\xb7",
		"\xef\xbd\xbd" => "\xe3\x82\xb9", "\xef\xbd\xbe" => "\xe3\x82\xbb",
		"\xef\xbd\xbf" => "\xe3\x82\xbd", "\xef\xbe\x80" => "\xe3\x82\xbf",
		"\xef\xbe\x81" => "\xe3\x83\x81", "\xef\xbe\x82" => "\xe3\x83\x84",
		"\xef\xbe\x83" => "\xe3\x83\x86", "\xef\xbe\x84" => "\xe3\x83\x88",
		"\xef\xbe\x85" => "\xe3\x83\x8a", "\xef\xbe\x86" => "\xe3\x83\x8b",
		"\xef\xbe\x87" => "\xe3\x83\x8c", "\xef\xbe\x88" => "\xe3\x83\x8d",
		"\xef\xbe\x89" => "\xe3\x83\x8e", "\xef\xbe\x8a" => "\xe3\x83\x8f",
		"\xef\xbe\x8b" => "\xe3\x83\x92", "\xef\xbe\x8c" => "\xe3\x83\x95",
		"\xef\xbe\x8d" => "\xe3\x83\x98", "\xef\xbe\x8e" => "\xe3\x83\x9b",
		"\xef\xbe\x8f" => "\xe3\x83\x9e", "\xef\xbe\x90" => "\xe3\x83\x9f",
		"\xef\xbe\x91" => "\xe3\x83\xa0", "\xef\xbe\x92" => "\xe3\x83\xa1",
		"\xef\xbe\x93" => "\xe3\x83\xa2", "\xef\xbe\x94" => "\xe3\x83\xa4",
		"\xef\xbe\x95" => "\xe3\x83\xa6", "\xef\xbe\x96" => "\xe3\x83\xa8",
		"\xef\xbe\x97" => "\xe3\x83\xa9", "\xef\xbe\x98" => "\xe3\x83\xaa",
		"\xef\xbe\x99" => "\xe3\x83\xab", "\xef\xbe\x9a" => "\xe3\x83\xac",
		"\xef\xbe\x9b" => "\xe3\x83\xad", "\xef\xbe\x9c" => "\xe3\x83\xaf",
		"\xef\xbe\x9d" => "\xe3\x83\xb3", "\xef\xbe\x9e" => "\xe3\x82\x9b",
		"\xef\xbe\x9f" => "\xe3\x82\x9c"
	);
	$str =~ s/(\xef\xbd[\xa1-\xbf]|\xef\xbe[\x80-\x9f])/$map2{$1}/g;
	return $str;
}

sub TopSearchEngines {
	if(-e "$LOGDIR/$TARGET_LOGNAME") {
		open(LOGFILE, "$LOGDIR/$TARGET_LOGNAME") || &ErrorPrint("アクセスログ「$LOGDIR/$TARGET_LOGNAME」をオープンできませんでした");
	} else {
		&ErrorPrint("アクセスログ（$LOGDIR/$TARGET_LOGNAME）がありません。");
	}
	my $in_req = $q->param('req');
	my $i = 0;
	my(%referer, %req);
	while(<LOGFILE>) {
		chomp;
		my($date_part, $referer_part, $req_part);
		if(/^(\d{14})\s+\S+\s+\S+\s+\S+\s+(\S+)\s+(\S+)\s+/) {
			$date_part = $1;
			$req_part = $2;
			$referer_part = $3;
		} else {
			next;
		}
		next if($date_part eq '');
		next unless(&IsInDate($date_part));
		$referer{$i} = $referer_part;
		if($CONF{'URLHANDLE'}) {
			$req_part =~ s/\?.*$//;
		}
		$req{$i} = $req_part;
		$i ++;
	}
	close(LOGFILE);
	my %engine_list;
	my %keyword_list;
	my %engine_urls;
	my $sum = 0;
	my %req_list;
	while( my($i, $r) = each %referer ) {
		if($r eq '' || $r eq '-') {
			next;
		}
		my($keyword, $engine_name, $engine_url) = &GetSearchKeyword($r);
		if($keyword ne '') {
			if($in_req eq '' || ($in_req ne '' && $in_req eq $req{$i})) {
				$keyword =~ s/</&lt;/g;
				$keyword =~ s/>/&gt;/g;
				$keyword =~ s/　/ /g;
				$keyword =~ s/\s+/ /g;
				$keyword =~ s/^\s+//;
				$keyword =~ s/\s+$//;
				$engine_list{$engine_name} ++;
				$keyword_list{"${engine_name}\t${keyword}"} ++;
				$engine_urls{$engine_name} = $engine_url;
				$sum ++;
			}
			$req_list{$req{$i}} ++;
		}
	}
	undef %referer;
	undef %req;
	my $Str;
	$Str .= "<h2>解析条件指定</h2>\n";
	$Str .= "<form action=\"${CGI_URL}\" method=\"post\">\n";
	$Str .= "<input type=\"hidden\" name=\"FRAME\" value=\"result\" />\n";
	$Str .= "<input type=\"hidden\" name=\"ITEM\" value=\"TopSearchEngines\" />\n";
	$Str .= "<input type=\"hidden\" name=\"LOG\" value=\"${TARGET_LOGNAME}\" />\n";
	if($ANA_MONTH) {
		$Str .= "<input type=\"hidden\" name=\"MONTH\" value=\"${ANA_MONTH}\" />\n";
		if($ANA_DAY) {
			$Str .= "<input type=\"hidden\" name=\"DAY\" value=\"${ANA_DAY}\" />\n";
		}
	}
	$Str .= "<h3>■ ページ絞り込み</h3>\n";
	$Str .= "<div><select name=\"req\">\n";
	$Str .= "<option value=\"\">指定なし</option>\n";
	for my $r (sort {$req_list{$b} <=> $req_list{$a}} keys %req_list) {
		my $selected = '';
		if($r eq $in_req) {
			$selected = ' selected="selected"';
		}
		my $r2 = $r;
		if(length($r2) > 80) {
			$r2 = substr($r, 0, 77) . '...';
		}
		$r2 = &SecureHtml($r2);
		my $r1 = &SecureHtml($r);
		my $n = $req_list{$r};
		$Str .= "<option value=\"${r1}\"${selected}>${r2} (${n})</option>\n";
	}
	undef %req_list;
	$Str .= "</select></div>\n";
	$Str .= "<div>　</div>\n";
	$Str .= "<input type=\"submit\" name=\"b1\" value=\"　解　析　\" />\n";
	$Str .= "</form>\n";
	$Str .= "<h2>ランキング</h2>\n";
	my $target_url = &SecureHtml($in_req);
	if($in_req eq '') {
		$target_url = '全ページ';
	}
	$Str .= "<div>対象ページ ： ${target_url}</div>\n";
	$Str .= &MakeCircleGraph(\%engine_list);
	$Str .= "<table class=\"tbl3\">\n";
	$Str .= "<tr>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\" colspan=\"2\">順位</th>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\">検索エンジン</th>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\">訪問数</th>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\">グラフ</th>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\" class=\"right\">\n";
	$Str .= "<img src=\"$CONF{IMAGE_URL}/arrowr2.gif\" width=\"17\" height=\"17\" alt=\"\" id=\"marrorwr\" class=\"marrorw\" />\n";
	$Str .= "<img src=\"$CONF{IMAGE_URL}/arrowb2.gif\" width=\"17\" height=\"17\" alt=\"\" id=\"marrorwb\" class=\"marrorw\" />\&nbsp\;\&nbsp\;\n";
	$Str .= "</th>\n";
	$Str .= "</tr>\n";
	my $engine_order = 1;
	for my $key ( sort {$engine_list{$b}<=>$engine_list{$a}} keys %engine_list ) {
		my $rate = int($engine_list{$key} * 10000 / $sum) / 100;
		my $GraphLength = int($CONF{'GRAPHMAXLENGTH'} * $rate / 100);
		my $n1 = &CommaFormat($engine_list{$key});
		$Str .= "<tr>\n";
		$Str .= "<td class=\"row1\"><img src=\"$CONF{IMAGE_URL}/arrowb.gif\" width=\"17\" height=\"17\" alt=\"\" class=\"arrow\" id=\"arrow${engine_order}\" /></td>\n";
		$Str .= "<td class=\"row1 center\">$engine_order</td>\n";
		$Str .= "<td class=\"row1\"><a href=\"$engine_urls{$key}\" target=\"_blank\">$key</a></td>\n";
		$Str .= "<td class=\"row1 right\">${n1}</td>\n";
		$Str .= "<td class=\"row1\" colspan=\"2\">";
		if($rate < 1) {
			$Str .= "";
		} else {
			$Str .= "<img src=\"$CONF{'IMAGE_URL'}/graphbar\.gif\" width=\"$GraphLength\" height=\"10\" alt=\"${rate}\%\" class=\"gbar1\" />";
		}
		$Str .= " <span class=\"small\">(${rate}\%)</span></td>\n";
		$Str .= "</tr>\n";
		$Str .= "<tbody id=\"sub${engine_order}\">\n";
		for my $key1 ( sort {$keyword_list{$b}<=>$keyword_list{$a}} keys %keyword_list ) {
			my($name, $word) = split(/\t/, $key1);
			if($name eq $key) {
				my $rate2 = int($keyword_list{$key1} * 10000 / $sum) / 100;
				my $GraphLength2 = int($CONF{'GRAPHMAXLENGTH'} * $rate2 / 100);
				my $n2 = &CommaFormat($keyword_list{$key1});
				$Str .= "<tr>\n";
				$Str .= "<td class=\"row2\" colspan=\"2\">\&nbsp\;</td>\n";
				$Str .= "<td class=\"row2\">$word</td>\n";
				$Str .= "<td class=\"row2 right\">${n2}</td>\n";
				$Str .= "<td class=\"row2\" colspan=\"2\">";
				if($rate2 < 1) {
					$Str .= "\&nbsp\;";
				} else {
					$Str .= "<img src=\"$CONF{'IMAGE_URL'}/graphbar2\.gif\" width=\"$GraphLength2\" height=\"8\" alt=\"${rate2}\%\" class=\"gbar2\" />";
				}
				$Str .= " (${rate2}\%)</td>\n";
				$Str .= "</tr>\n";
			}
		}
		$Str .= "</tbody>\n";
		$engine_order ++;
	}
	undef %engine_list;
	undef %keyword_list;
	$Str .= "</table>\n";
	my $Title = '検索エンジンランキング';
	&PrintResult($Title, $Str, './template/result2.html');
}

sub GetSearchKeyword {
	my($requested_url) = @_;
	my ($url, $getstr) = split(/\?/, $requested_url);
	if($getstr eq '' && $url !~ /(a9\.com|\.excite\.com|technorati\.jp)/) {
		return '';
	}
	my @parts = split(/\&/, $getstr);
	my %variables;
	for my $part (@parts) {
		my ($name, $value) = split(/=/, $part);
		if($value ne '') {
			$variables{$name} = $value;
		}
	}
	my @url_parts = split(/\//, $url);
	my @url_parts2 = split(/\./, $url_parts[2]);
	my $tld = pop @url_parts2;
	my $word = '';
	my $engine_name = '';
	my $engine_url = '';
	if($url =~ /\.google\./) {
		if($url =~ /images\.google\./) {
			my $prev = $variables{'prev'};
			$prev = &URL_Decode($prev);
			if($prev =~ /q=([^&]+)&/) {
				$word = $1;
			}
		} elsif($variables{'q'} ne '') {
			$word = $variables{'q'};
		} elsif($variables{'as_q'} ne '') {
			$word = $variables{'as_q'};
		}
		$engine_name = "Google($tld)";
		my @tmp = split(/\.google\./, $url);
		my $suffix = pop @tmp;
		$engine_url = 'http://www.google.' . $suffix;
	} elsif($url =~ /\.yahoo\./) {
		if($variables{'p'} ne '') {
			$word = $variables{'p'};
		} elsif($variables{'key'} ne '') {
			$word = $variables{'key'};
		}
		$engine_name = "Yahoo!($tld)";
		my @tmp = split(/\.yahoo\./, $url);
		my $suffix = pop @tmp;
		$engine_url = 'http://www.yahoo.' . $suffix;
	} elsif($url =~ /\.excite\./) {
		if($url =~ /odn\.excite\.co\.jp/) {
			$word = $variables{'search'};
			$engine_name = "ODN Search";
			$engine_url = 'http://www.odn.ne.jp/';
		} elsif($url =~ /dion\.excite\.co\.jp/) {
			$word = $variables{'search'};
			$engine_name = "DION Search";
			$engine_url = 'http://www.dion.ne.jp/';
		} else {
			if($variables{'search'}) {
				$word = $variables{'search'};
			} elsif($variables{'s'}) {
				$word = $variables{'s'};
			}
			$engine_name = "excite($tld)";
			my @tmp = split(/\.excite\./, $url);
			my $suffix = pop @tmp;
			$engine_url = 'http://www.excite.' . $suffix;
		}
	} elsif($url =~ /\.msn\./) {
		$word = $variables{'q'};
		$engine_name = "MSN($tld)";
		my @tmp = split(/\.msn\./, $url);
		my $suffix = pop @tmp;
		$engine_url = 'http://www.msn.' . $suffix;
	} elsif($url =~ /\.live\.com/) {
		$word = $variables{'q'};
		$engine_name = 'Live Search';
		$engine_url = 'http://www.live.com/';
	} elsif($url =~ /\.infoseek\./) {
		$word = $variables{'qt'};
		$engine_name = 'infoseek';
		$engine_url = 'http://www.infoseek.co.jp/';
	} elsif($url =~ /\.goo\.ne\.jp/) {
		$word = $variables{'MT'};
		$engine_name = 'goo';
		$engine_url = 'http://www.goo.ne.jp/';
	} elsif($url =~ /search\.livedoor\.com/) {
		$word = $variables{'q'};
		$engine_name = 'livedoor';
		$engine_url = 'http://www.livedoor.com/';
	} elsif($url =~ /ask\.[a-z]+\//) {
		$word = $variables{'q'};
		$engine_name = "Ask($tld)";
		$engine_url = 'http://ask.' . $tld;
	} elsif($url =~ /lycos/) {
		if($url =~ /wisenut/) {
			$word = $variables{'q'};
		} else {
			$word = $variables{'query'};
		}
		$engine_name = "Lycos($tld)";
		my @tmp = split(/\.lycos\./, $url);
		my $suffix = pop @tmp;
		$engine_url = 'http://www.lycos.' . $suffix;
	} elsif($url =~ /\.fresheye\.com/) {
		$word = $variables{'kw'};
		$engine_name = 'フレッシュアイ';
		$engine_url = 'http://www.fresheye.com/';
	} elsif($url =~ /search\.biglobe\.ne\.jp/) {
		$word = $variables{'q'};
		$engine_name = 'BIGLOBEサーチ attayo';
		$engine_url = 'http://search.biglobe.ne.jp/';
	} elsif($url =~ /\.netscape\.com/) {
		$word = $variables{'s'};
		$engine_name = 'Netscape Search';
		$engine_url = 'http://www.netscape.com/';
	} elsif($url =~ /www\.overture\.com/) {
		$word = $variables{'Keywords'};
		$engine_name = 'overture';
		$engine_url = 'http://www.overture.com/';
	} elsif($url =~ /\.altavista\.com/) {
		$word = $variables{'q'};
		$engine_name = 'altavista';
		$engine_url = 'http://www.altavista.com/';
	} elsif($url =~ /search\.aol\.com/) {
		$word = $variables{'query'};
		$engine_name = 'AOL Search(com)';
		$engine_url = 'http://search.aol.com/aolcom/webhome';
	} elsif($url =~ /search\.jp\.aol\.com/) {
		$word = $variables{'query'};
		$engine_name = 'AOL Search(jp)';
		$engine_url = 'http://search.jp.aol.com/index';
	} elsif($url =~ /\.looksmart\.com/) {
		$word = $variables{'qt'};
		$engine_name = 'looksmart';
		$engine_url = 'http://search.looksmart.com/';
	} elsif($url =~ /bach\.istc\.kobe\-u\.ac\.jp\/cgi\-bin\/metcha\.cgi/) {
		$word = $variables{'q'};
		$engine_name = 'Metcha Seearch';
		$engine_url = 'http://bach.cs.kobe-u.ac.jp/metcha/';
	} elsif($url =~ /\.alltheweb\.com/) {
		$word = $variables{'q'};
		$engine_name = 'alltheweb';
		$engine_url = 'http://www.alltheweb.com/';
	} elsif($url =~ /\.alexa\.com\/search/) {
		$word = $variables{'q'};
		$engine_name = 'Alexa';
		$engine_url = 'http://www.alexa.com/';
	} elsif($url =~ /search\.naver\.com/) {
		$word = $variables{'query'};
		$engine_name = 'NEVER';
		$engine_url = 'http://www.naver.com/';
	} elsif($url =~ /\.baidu\.(com|jp)/) {
		my $tld = $1;
		$word = $variables{'wd'};
		$engine_name = "百度(${tld})";
		$engine_url = "http://www.baidu.${tld}/";
	} elsif($url =~ /\.mooter\.co\.jp/) {
		$word = $variables{'keywords'};
		$engine_name = 'Mooter';
		$engine_url = 'http://www.mooter.co.jp/';
	} elsif($url =~ /\.marsflag\.com/) {
		$word = $variables{'phrase'};
		$engine_name = 'MARS FLAG';
		$engine_url = 'http://www.marsflag.com/';
	} elsif($url =~ /clusty\.jp/) {
		$word = $variables{'query'};
		$engine_name = 'Clusty';
		$engine_url = 'http://clusty.jp/';
	} elsif($url =~ /(search|newsflash)\.nifty\.com/) {
		if($variables{'Text'} ne '') {
			$word = $variables{'Text'};
		} elsif($variables{'q'} ne '') {
			$word = $variables{'q'};
		} elsif($variables{'key'} ne '') {
			$word = $variables{'key'};
		}
		$engine_name = '@nifty アット・サーチ';
		$engine_url = 'http://www.nifty.com/search/';
	} elsif($url =~ /\.technorati\.jp\/search\/(.+)$/) {
		$word = $1;
		$engine_name = 'テクノラティ';
		$engine_url = 'http://www.technorati.jp/';

	} else {
		return '';
	}
	if($word eq '') {
		return '';
	}
	$word = &URL_Decode($word);
	if($requested_url =~ /\&(ei|ie)\=utf\-8/i) {
		#何もしない
	} elsif($requested_url =~ /\&(ei|ie)\=euc\-jp/i) {
		&Jcode::convert(\$word, "utf8", "euc");
	} elsif($requested_url =~ /\&(ei|ie)\=(Shift_JIS|SJIS)/i) {
		&Jcode::convert(\$word, "utf8", "sjis");
	} elsif($requested_url =~ /\.google\./) {
		#何もしない
	} else {
		&Jcode::convert(\$word, "utf8");
	}
	$word =~ s/　/ /g;
	$word =~ s/\s+/ /g;
	$word =~ s/^\s//;
	$word =~ s/\s$//;
	return $word, $engine_name, $engine_url;
}

sub TopBrowsers {
	if(-e "$LOGDIR/$TARGET_LOGNAME") {
		open(LOGFILE, "$LOGDIR/$TARGET_LOGNAME") || &ErrorPrint("アクセスログ「$LOGDIR/$TARGET_LOGNAME」をオープンできませんでした");
	} else {
		&ErrorPrint("アクセスログ（$LOGDIR/$TARGET_LOGNAME）がありません。");
	}
	my $i = 0;
	my $loglines = 0;
	my(%user_agent);
	while(<LOGFILE>) {
		chomp;
		my($date_part, $ua_part);
		if(/^(\d{14})\s+\S+\s+\S+\s+\S+\s+\S+\s+\S+\s+\"([^\"]+)\"\s+/) {
			$date_part = $1;
			$ua_part = $2;
		} else {
			next;
		}
		next if($date_part eq '');
		next unless(&IsInDate($date_part));
		$user_agent{$i} = $ua_part;
		$i ++;
		$loglines ++;
	}
	close(LOGFILE);
	my(%browser_list, %browser_v_list, %platform_list, %platform_v_list);
	while( my($i, $ua) = each %user_agent ) {
		my($platform, $platform_v, $browser, $browser_v) = &User_Agent($ua);
		$browser_list{$browser} ++;
		$browser_v_list{"$browser:$browser_v"} ++;
		$platform_list{"$platform"} ++;
		$platform_v_list{"$platform:$platform_v"} ++;
	}
	undef %user_agent;
	my($Str);
	$Str .= &MakeCircleGraph(\%browser_list);
	$Str .= "<table class=\"tbl3\">\n";
	$Str .= "<tr>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\" colspan=\"2\">順位</th>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\">ブラウザー</th>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\">インプレッション数</th>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\">グラフ</th>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\" class=\"right\">\n";
	$Str .= "<img src=\"$CONF{IMAGE_URL}/arrowr2.gif\" width=\"17\" height=\"17\" alt=\"\" id=\"marrorwr\" class=\"marrorw\" />\n";
	$Str .= "<img src=\"$CONF{IMAGE_URL}/arrowb2.gif\" width=\"17\" height=\"17\" alt=\"\" id=\"marrorwb\" class=\"marrorw\" />\&nbsp\;\&nbsp\;\n";
	$Str .= "</th>\n";
	$Str .= "</tr>\n";
	my $browser_order = 1;
	for my $key ( sort {$browser_list{$b}<=>$browser_list{$a}} keys %browser_list ) {
		my $rate = int($browser_list{$key} * 10000 / $loglines) / 100;
		my $GraphLength = int($CONF{'GRAPHMAXLENGTH'} * $rate / 100);
		my $n1 = &CommaFormat($browser_list{$key});

		$Str .= "<tr>\n";
		$Str .= "<td class=\"row1\"><img src=\"$CONF{IMAGE_URL}/arrowb.gif\" width=\"17\" height=\"17\" alt=\"\" class=\"arrow\" id=\"arrow${browser_order}\" /></td>\n";
		$Str .= "<td class=\"row1 center\">$browser_order</td>\n";
		$Str .= "<td class=\"row1\">";
		if($key eq '') {
			$Str .= " 不明</td><td class=\"row1 right\">${n1}";
		} else {
			$Str .= " $key</td><td class=\"row1 right\">${n1}";
		}
		$Str .= "</td>\n";
		$Str .= "<td class=\"row1\" colspan=\"2\">";
		if($rate < 1) {
			$Str .= "";
		} else {
			$Str .= "<img src=\"$CONF{'IMAGE_URL'}/graphbar\.gif\" width=\"$GraphLength\" height=\"10\" alt=\"${rate}\%\" class=\"gbar1\" />";
		}
		$Str .= " <span class=\"small\">(${rate}\%)</span>\n";
		$Str .= "</td>\n";
		$Str .= "</tr>\n";
		$Str .= "<tbody id=\"sub${browser_order}\">\n";
		for my $key1 (sort keys %browser_v_list) {
			if($key1 =~ /^$key:/) {
				my $rate2 = int($browser_v_list{$key1} * 10000 / $loglines) / 100;
				my $GraphLength2 = int($CONF{'GRAPHMAXLENGTH'} * $rate2 / 100);
				my $v = $key1;
				$v =~ s/^$key://;
				my $n2 = &CommaFormat($browser_v_list{$key1});
				$Str .= "<tr>\n";
				$Str .= "<td class=\"row2\" colspan=\"2\">\&nbsp\;</td>\n";
				$Str .= "<td class=\"row2\">$v</TD>\n";
				$Str .= "<td class=\"row2 right\">${n2}</td>";
				$Str .= "<td class=\"row2\" colspan=\"2\">";
				if($rate2 < 1) {
					$Str .= "\&nbsp\;";
				} else {
					$Str .= "<img src=\"$CONF{'IMAGE_URL'}/graphbar2\.gif\" width=\"$GraphLength2\" height=\"8\" alt=\"${rate2}\%\" class=\"gbar2\" />";
				}
				$Str .= " (${rate2}\%)</td>\n";
				$Str .= "</tr>\n";
			}
		}
		$Str .= "</tbody>\n";
		$browser_order ++;
	}
	undef %browser_list;
	undef %browser_v_list;
	undef %platform_list;
	undef %platform_v_list;
	$Str .= "</table>\n";
	my $Title = 'ブラウザーランキング';
	&PrintResult($Title, $Str, './template/result2.html');
}

sub TopAcceptLanguage {
	if(-e "$LOGDIR/$TARGET_LOGNAME") {
		open(LOGFILE, "$LOGDIR/$TARGET_LOGNAME") || &ErrorPrint("アクセスログ「$LOGDIR/$TARGET_LOGNAME」をオープンできませんでした");
	} else {
		&ErrorPrint("アクセスログ（$LOGDIR/$TARGET_LOGNAME）がありません。");
	}
	my $by = $q->param('by');
	if($by !~ /^(1|2|3)$/) { $by = 1; }
	my $i = 0;
	my(%date, %remote_host, %cookies, %accept_language);
	while(<LOGFILE>) {
		chomp;
		my($date_part, $host_part, $cookie_part, $accept_lang_part);
		if(/^(\d{14})\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+\"([^\"]+)\"\s+\"([^\"]+)\"\s+\"([^\"]+)\"/) {
			$date_part = $1;
			$host_part = $2;
			$cookie_part = $3;
			$accept_lang_part = $8;
		} else {
			next;
		}
		next if($date_part eq '');
		next unless(&IsInDate($date_part));
		$date{$i} = $date_part;
		$remote_host{$i} = $host_part;
		$accept_language{$i} = $accept_lang_part;
		$cookies{$i} = $cookie_part;
		$i ++;
	}
	close(LOGFILE);
	my %LogNoList;
	while( my($i, $lan) = each %accept_language ) {
		if($lan eq '-') {next;}
		unless($lan) {next;}
		my @buff = split(/,/, $lan);
		my $max = 0;
		my($lang);
		for my $j (@buff) {
			my ($lang_tmp, $value_tmp) = split(/\;/, $j);
			$value_tmp =~ s/q=//;
			$value_tmp = 1 if($value_tmp eq '');
			if($max < $value_tmp) {
				$lang = $lang_tmp;
				$max = $value_tmp;
			}
		}
		$lang = lc $lang;
		push(@{$LogNoList{$lang}}, $i);
	}
	undef %accept_language;
	my %num_list;
	if($by == 1) {
		while( my($key, $aref) = each %LogNoList ) {
			$num_list{$key} = scalar @{$LogNoList{$key}};
		}
	} elsif($by == 2) {
		while( my($key, $aref) = each %LogNoList ) {
			$num_list{$key} = &GetSessionNum($LogNoList{$key}, \%date, \%remote_host, \%cookies);
		}
	} elsif($by == 3) {
		while( my($key, $aref) = each %LogNoList ) {
			$num_list{$key} = &GetUniqueUserNum($LogNoList{$key}, \%remote_host, \%cookies);
		}
	}
	undef %LogNoList;
	undef %date;
	undef %remote_host;
	undef %cookies;
	undef %accept_language;
	my $Str;
	$Str .= "<h2>解析条件指定</h2>\n";
	$Str .= "<form action=\"${CGI_URL}\" method=\"post\">\n";
	$Str .= "<input type=\"hidden\" name=\"FRAME\" value=\"result\" />\n";
	$Str .= "<input type=\"hidden\" name=\"ITEM\" value=\"TopAcceptLanguage\" />\n";
	$Str .= "<input type=\"hidden\" name=\"LOG\" value=\"${TARGET_LOGNAME}\" />\n";
	if($ANA_MONTH) {
		$Str .= "<input type=\"hidden\" name=\"MONTH\" value=\"${ANA_MONTH}\" />\n";
		if($ANA_DAY) {
			$Str .= "<input type=\"hidden\" name=\"DAY\" value=\"${ANA_DAY}\" />\n";
		}
	}
	$Str .= "<h3>■ 解析モード</h3>\n";
	my($by1_checked, $by2_checked, $by3_checked);
	if($by == 2) {
		$by2_checked = 'checked="checked "';
	} elsif($by == 3) {
		$by3_checked = 'checked="checked "';
	} else {
		$by1_checked = 'checked="checked "';
	}
	$Str .= "<div><input type=\"radio\" name=\"by\" value=\"1\" ${by1_checked}/> インプレッション数　　<input type=\"radio\" name=\"by\" value=\"2\" ${by2_checked}/> セッション数　　<input type=\"radio\" name=\"by\" value=\"3\" ${by3_checked}/> ユニークユーザー数</div>\n";
	$Str .= "<div>　</div>\n";
	$Str .= "<input type=\"submit\" name=\"b1\" value=\"　解　析　\" />\n";
	$Str .= "</form>\n";
	#
	my %lang_code_map = &ReadDef('./data/language.dat');
	my %country_code_map = &ReadDef('./data/country.dat');
	my %clist;
	my %llist;
	my $sum = 0;
	while( my($k, $v) = each %num_list ) {
		my $p1 = $k;
		my $p2 = "";
		if($k =~ /^([^\-]+)\-([^\-]+)/) {
			$p1 = $1;
			$p2 = $2;
		}
		if($lang_code_map{$p1}) {
			$p1 = $lang_code_map{$p1};
		}
		if($country_code_map{$p2}) {
			$p2 = "${p1}/$country_code_map{$p2}";
		} else {
			$p2 = "${p1}/${p2}";
		}
		$llist{$p1} += $v;
		$clist{$p1}->{$p2} += $v;
		$sum += $v;
	}
	#
	my $gtitle;
	if($by == 1) {
		$gtitle = 'インプレッション数';
	}
	if($by == 2) {
		$gtitle = 'セッション数';
	}
	if($by == 3) {
		$gtitle = 'ユニークユーザー数';
	}
	$Str .= "<h2>${gtitle}</h2>\n";
	$Str .= &MakeCircleGraph(\%llist);
	#
	$Str .= "<table class=\"tbl3\">\n";
	$Str .= "<tr>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\" colspan=\"2\">順位</th>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\">言語</th>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\">${gtitle}</th>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\">グラフ</th>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\" class=\"right\">\n";
	$Str .= "<img src=\"$CONF{IMAGE_URL}/arrowr2.gif\" width=\"17\" height=\"17\" alt=\"\" id=\"marrorwr\" class=\"marrorw\" />\n";
	$Str .= "<img src=\"$CONF{IMAGE_URL}/arrowb2.gif\" width=\"17\" height=\"17\" alt=\"\" id=\"marrorwb\" class=\"marrorw\" />\&nbsp\;\&nbsp\;\n";
	$Str .= "</th>\n";
	$Str .= "  </tr>\n";
	my $lorder = 1;
	foreach my $key ( sort {$llist{$b}<=>$llist{$a}} keys %llist ) {
		my $rate = int($llist{$key} * 10000 / $sum) / 100;
		my $GraphLength = int($CONF{GRAPHMAXLENGTH} * $rate / 100);
		my $n1 = &CommaFormat($llist{$key});
		$Str .= "<tr>\n";
		$Str .= "<td class=\"row1\"><img src=\"$CONF{IMAGE_URL}/arrowb.gif\" width=\"17\" height=\"17\" alt=\"\" class=\"arrow\" id=\"arrow${lorder}\" /></td>\n";
		$Str .= "<td class=\"row1 center\">$lorder</td>\n";
		$Str .= "<td class=\"row1\">";
		if($key eq '') {
			$Str .= " 不明";
		} else {
			$Str .= " $key";
		}
		$Str .= "</td>\n";
		$Str .= "<td class=\"row1 right\">${n1}</td>\n";
		$Str .= "<td class=\"row1\" colspan=\"2\">\n";
		if($rate < 1) {
			$Str .= "";
		} else {
			$Str .= "<img src=\"$CONF{IMAGE_URL}/graphbar\.gif\" width=\"$GraphLength\" height=\"10\" alt=\"${rate}\%\" class=\"gbar1\" />";
		}
		$Str .= " <span class=\"small\">(${rate}\%)</span>\n";
		$Str .= "</td>\n";
		$Str .= "</tr>\n";
		$Str .= "  <tbody id=\"sub${lorder}\">\n";
		for my $key1 (sort keys %{$clist{$key}}) {
			my $n = $clist{$key}->{$key1};
			my $rate2 = int($n * 10000 / $sum) / 100;
			my $GraphLength2 = int($CONF{GRAPHMAXLENGTH} * $rate2 / 100);
			$n = &CommaFormat($clist{$key}->{$key1});
			$Str .= "<tr>\n";
			$Str .= "<td class=\"row2\" colspan=\"2\">\&nbsp\;</td>\n";
			$Str .= "<td class=\"row2\">${key1}</td>\n";
			$Str .= "<td class=\"row2 right\">${n}</td>\n";
			$Str .= "<td class=\"row2\" colspan=\"2\">";
			if($rate2 < 1) {
				$Str .= "\&nbsp\;";
			} else {
				$Str .= "<img src=\"$CONF{IMAGE_URL}/graphbar2\.gif\" width=\"$GraphLength2\" height=\"8\" alt=\"${rate2}\%\" class=\"gbar2\" />";
			}
			$Str .= " (${rate2}\%)</td></tr>\n";
		}
		$Str .= "  </tbody>\n";
		$lorder ++;
	}
	$Str .= "</table>\n";
	my $Title = 'ブラウザー表示言語ランキング';
	&PrintResult($Title, $Str, './template/result2.html');
	#
	my $Title = 'ブラウザー表示言語ランキング';
	&PrintResult($Title, $Str);
}

sub TopPlatforms {
	if(-e "$LOGDIR/$TARGET_LOGNAME") {
		open(LOGFILE, "$LOGDIR/$TARGET_LOGNAME") || &ErrorPrint("アクセスログ「$LOGDIR/$TARGET_LOGNAME」をオープンできませんでした");
	} else {
		&ErrorPrint("アクセスログ（$LOGDIR/$TARGET_LOGNAME）がありません。");
	}
	my $i = 0;
	my $loglines = 0;
	my(%user_agent);
	while(<LOGFILE>) {
		chomp;
		my($date_part, $ua_part);
		if(/^(\d{14})\s+\S+\s+\S+\s+\S+\s+\S+\s+\S+\s+\"([^\"]+)\"\s+/) {
			$date_part = $1;
			$ua_part = $2;
		} else {
			next;
		}
		next if($date_part eq '');
		next unless(&IsInDate($date_part));
		$user_agent{$i} = $ua_part;
		$i ++;
		$loglines ++;
	}
	close(LOGFILE);
	my(%browser_list, %browser_v_list, %platform_list, %platform_v_list);
	while( my($i, $ua) = each %user_agent ) {
		my ($platform, $platform_v, $browser, $browser_v) = &User_Agent($ua);
		$browser_list{$browser} ++;
		$browser_v_list{"$browser:$browser_v"} ++;
		$platform_list{"$platform"} ++;
		$platform_v_list{"$platform:$platform_v"} ++;
	}
	undef %user_agent;
	my($Str);
	$Str .= &MakeCircleGraph(\%platform_list);
	$Str .= "<table class=\"tbl3\">\n";
	$Str .= "<tr>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\" colspan=\"2\">順位</th>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\">OS</th>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\">インプレッション数</th>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\">グラフ</th>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\" class=\"right\">\n";
	$Str .= "<img src=\"$CONF{IMAGE_URL}/arrowr2.gif\" width=\"17\" height=\"17\" alt=\"\" id=\"marrorwr\" class=\"marrorw\" />\n";
	$Str .= "<img src=\"$CONF{IMAGE_URL}/arrowb2.gif\" width=\"17\" height=\"17\" alt=\"\" id=\"marrorwb\" class=\"marrorw\" />\&nbsp\;\&nbsp\;\n";
	$Str .= "</th>\n";
	$Str .= "  </tr>\n";
	my $os_order = 1;
	for my $key ( sort {$platform_list{$b}<=>$platform_list{$a}} keys %platform_list ) {
		my $rate = int($platform_list{$key} * 10000 / $loglines) / 100;
		my $GraphLength = int($CONF{'GRAPHMAXLENGTH'} * $rate / 100);
		my $n1 = &CommaFormat($platform_list{$key});

		$Str .= "<tr>\n";
		$Str .= "<td class=\"row1\"><img src=\"$CONF{IMAGE_URL}/arrowb.gif\" width=\"17\" height=\"17\" alt=\"\" class=\"arrow\" id=\"arrow${os_order}\" /></td>\n";
		$Str .= "<td class=\"row1 center\">$os_order</td>\n";
		$Str .= "<td class=\"row1\">";
		if($key eq '') {
			$Str .= " 不明";
		} else {
			$Str .= " $key";
		}
		$Str .= "</td>\n";
		$Str .= "<td class=\"row1 right\">${n1}</td>\n";
		$Str .= "<td class=\"row1\" colspan=\"2\">\n";
		if($rate < 1) {
			$Str .= "";
		} else {
			$Str .= "<img src=\"$CONF{'IMAGE_URL'}/graphbar\.gif\" width=\"$GraphLength\" height=\"10\" alt=\"${rate}\%\" class=\"gbar1\" />";
		}
		$Str .= " <span class=\"small\">(${rate}\%)</span>\n";
		$Str .= "</td>\n";
		$Str .= "</tr>\n";
		$Str .= "  <tbody id=\"sub${os_order}\">\n";
		for my $key1 (sort keys %platform_v_list) {
			if($key1 =~ /^$key:/) {
				my $rate2 = int($platform_v_list{$key1} * 10000 / $loglines) / 100;
				my $GraphLength2 = int($CONF{'GRAPHMAXLENGTH'} * $rate2 / 100);
				my $v = $key1;
				$v =~ s/^$key://;
				my $n2 = &CommaFormat($platform_v_list{$key1});
				$Str .= "<tr>\n";
				$Str .= "<td class=\"row2\" colspan=\"2\">\&nbsp\;</td>\n";
				$Str .= "<td class=\"row2\">$v</td>\n";
				$Str .= "<td class=\"row2 right\">${n2}</td>\n";
				$Str .= "<td class=\"row2\" colspan=\"2\">";
				if($rate2 < 1) {
					$Str .= "\&nbsp\;";
				} else {
					$Str .= "<img src=\"$CONF{'IMAGE_URL'}/graphbar2\.gif\" width=\"$GraphLength2\" height=\"8\" alt=\"${rate2}\%\" class=\"gbar2\" />";
				}
				$Str .= " (${rate2}\%)</td></tr>\n";
			}
		}
		$Str .= "  </tbody>\n";
		$os_order ++;
	}
	undef %browser_list;
	undef %browser_v_list;
	undef %platform_list;
	undef %platform_v_list;
	$Str .= "</table>\n";
	my $Title = 'OSランキング';
	&PrintResult($Title, $Str, './template/result2.html');
}

sub TopResolution {
	if(-e "$LOGDIR/$TARGET_LOGNAME") {
		open(LOGFILE, "$LOGDIR/$TARGET_LOGNAME") || &ErrorPrint("アクセスログ「$LOGDIR/$TARGET_LOGNAME」をオープンできませんでした");
	} else {
		&ErrorPrint("アクセスログ（$LOGDIR/$TARGET_LOGNAME）がありません。");
	}
	my $by = $q->param('by');
	if($by !~ /^(1|2|3)$/) { $by = 1; }
	my $i = 0;
	my $min_date = 99999999999999;
	my $max_date = 0;
	my(%date, %cookies, %remote_host, %screen);
	while(<LOGFILE>) {
		chomp;
		my($date_part, $cookie_part, $host_part, $screen_part);
		if(/^(\d{14})\s+(\S+)\s+(\S+)\s+\S+\s+\S+\s+\S+\s+\"[^\"]+\"\s+\"[^\"]+\"\s+\"([^\"]+)\"/) {
			$date_part = $1;
			$host_part = $2;
			$cookie_part = $3;
			$screen_part = $4;
		} else {
			next;
		}
		next if($date_part eq '');
		next unless(&IsInDate($date_part));
		$date{$i} = $date_part;
		$remote_host{$i} = $host_part;
		$screen{$i} = $screen_part;
		$cookies{$i} = $cookie_part;
		$i ++;
	}
	close(LOGFILE);
	my %LogNoList;
	while( my($i, $s) = each %screen ) {
		if($s eq '-') {next;}
		unless($s) {next;}
		my($ScreenWidth, $ScreenHeight, $ColorDepth) = split(/\s/, $s);
		push(@{$LogNoList{"$ScreenWidth×$ScreenHeight"}}, $i);
	}
	undef %screen;
	my %SessionNumList;
	my %PageViewNumList;
	my %UniqueNumList;
	if($by == 1) {
		while( my($key, $aref) = each %LogNoList ) {
			$PageViewNumList{$key} = scalar @{$aref};
		}
	} elsif($by == 2) {
		while( my($key, $aref) = each %LogNoList ) {
			$SessionNumList{$key} = &GetSessionNum($aref, \%date, \%remote_host, \%cookies);
		}
	} elsif($by == 3) {
		while( my($key, $aref) = each %LogNoList ) {
			$UniqueNumList{$key} = &GetUniqueUserNum($aref, \%remote_host, \%cookies);
		}
	}
	undef %LogNoList;
	undef %date;
	undef %cookies;
	undef %remote_host;
	my $Str;
	$Str .= "<h2>解析条件指定</h2>\n";
	$Str .= "<form action=\"${CGI_URL}\" method=\"post\">\n";
	$Str .= "<input type=\"hidden\" name=\"FRAME\" value=\"result\" />\n";
	$Str .= "<input type=\"hidden\" name=\"ITEM\" value=\"TopResolution\" />\n";
	$Str .= "<input type=\"hidden\" name=\"LOG\" value=\"${TARGET_LOGNAME}\" />\n";
	if($ANA_MONTH) {
		$Str .= "<input type=\"hidden\" name=\"MONTH\" value=\"${ANA_MONTH}\" />\n";
		if($ANA_DAY) {
			$Str .= "<input type=\"hidden\" name=\"DAY\" value=\"${ANA_DAY}\" />\n";
		}
	}
	$Str .= "<h3>■ 解析モード</h3>\n";
	my($by1_checked, $by2_checked, $by3_checked);
	if($by == 2) {
		$by2_checked = 'checked="checked "';
	} elsif($by == 3) {
		$by3_checked = 'checked="checked "';
	} else {
		$by1_checked = 'checked="checked "';
	}
	$Str .= "<div><input type=\"radio\" name=\"by\" value=\"1\" ${by1_checked}/> インプレッション数　　<input type=\"radio\" name=\"by\" value=\"2\" ${by2_checked}/> セッション数　　<input type=\"radio\" name=\"by\" value=\"3\" ${by3_checked}/> ユニークユーザー数</div>\n";
	$Str .= "<div>　</div>\n";
	$Str .= "<input type=\"submit\" name=\"b1\" value=\"　解　析　\" />\n";
	$Str .= "</form>\n";
	my @Titles;
	if($by == 1) {
		$Str .= "<h2>インプレッション数</h2>\n";
		$Str .= &MakeCircleGraph(\%PageViewNumList);
		@Titles = ('順位', '解像度', 'インプレッション数', 'グラフ');
		$Str .=&MakeGraph(\%PageViewNumList, \@Titles);
		undef %PageViewNumList;
	}
	if($by == 2) {
		$Str .= "<h2>セッション数</h2>\n";
		$Str .= &MakeCircleGraph(\%SessionNumList);
		@Titles = ('順位', '解像度', 'セッション数', 'グラフ');
		$Str .= &MakeGraph(\%SessionNumList, \@Titles);
		undef %SessionNumList;
	}
	if($by == 3) {
		$Str .= "<h2>ユニークユーザー数</h2>\n";
		$Str .= &MakeCircleGraph(\%UniqueNumList);
		@Titles = ('順位', '解像度', 'ユニークユーザー数', 'グラフ');
		$Str .= &MakeGraph(\%UniqueNumList, \@Titles);
		undef %UniqueNumList;
	}
	my $Title = 'クライアント画面解像度ランキング';
	&PrintResult($Title, $Str);
}

sub TopColorDepth {
	if(-e "$LOGDIR/$TARGET_LOGNAME") {
		open(LOGFILE, "$LOGDIR/$TARGET_LOGNAME") || &ErrorPrint("アクセスログ「$LOGDIR/$TARGET_LOGNAME」をオープンできませんでした");
	} else {
		&ErrorPrint("アクセスログ（$LOGDIR/$TARGET_LOGNAME）がありません。");
	}
	my $by = $q->param('by');
	if($by !~ /^(1|2|3)$/) { $by = 1; }
	my $i = 0;
	my $min_date = 99999999999999;
	my $max_date = 0;
	my(%date, %remote_host, %cookies, %screen);
	while(<LOGFILE>) {
		chomp;
		my($date_part, $host_part, $cookie_part, $screen_part);
		if(/^(\d{14})\s+(\S+)\s+(\S+)\s+\S+\s+\S+\s+\S+\s+\"[^\"]+\"\s+\"[^\"]+\"\s+\"([^\"]+)\"/) {
			$date_part = $1;
			$host_part = $2;
			$cookie_part = $3;
			$screen_part = $4;
		} else {
			next;
		}
		next if($date_part eq '');
		next unless(&IsInDate($date_part));
		$date{$i} = $date_part;
		$remote_host{$i} = $host_part;
		$screen{$i} = $screen_part;
		$cookies{$i} = $cookie_part;
		$i ++;
	}
	close(LOGFILE);
	my %LogNoList;
	while( my($i, $s) = each %screen ) {
		if($s eq '-') {next;}
		unless($s) {next;}
		my ($ScreenWidth, $ScreenHeight, $ColorDepth) = split(/\s/, $s);
		my $KeyStr = 2**$ColorDepth;
		$KeyStr .= '色';
		$KeyStr .= "（${ColorDepth} bit）";
		push(@{$LogNoList{$KeyStr}}, $i);
	}
	undef %screen;
	my %SessionNumList;
	my %PageViewNumList;
	my %UniqueNumList;
	if($by == 1) {
		while( my($key, $aref) = each %LogNoList ) {
			$PageViewNumList{$key} = scalar @{$aref};
		}
	} elsif($by == 2) {
		while( my($key, $aref) = each %LogNoList ) {
			$SessionNumList{$key} = &GetSessionNum($aref, \%date, \%remote_host, \%cookies);
		}
	} elsif($by == 3) {
		while( my($key, $aref) = each %LogNoList ) {
			$UniqueNumList{$key} = &GetUniqueUserNum($aref, \%remote_host, \%cookies);
		}
	}
	undef %LogNoList;
	undef %date;
	undef %remote_host;
	undef %cookies;
	my $Str;
	$Str .= "<h2>解析条件指定</h2>\n";
	$Str .= "<form action=\"${CGI_URL}\" method=\"post\">\n";
	$Str .= "<input type=\"hidden\" name=\"FRAME\" value=\"result\" />\n";
	$Str .= "<input type=\"hidden\" name=\"ITEM\" value=\"TopColorDepth\" />\n";
	$Str .= "<input type=\"hidden\" name=\"LOG\" value=\"${TARGET_LOGNAME}\" />\n";
	if($ANA_MONTH) {
		$Str .= "<input type=\"hidden\" name=\"MONTH\" value=\"${ANA_MONTH}\" />\n";
		if($ANA_DAY) {
			$Str .= "<input type=\"hidden\" name=\"DAY\" value=\"${ANA_DAY}\" />\n";
		}
	}
	$Str .= "<h3>■ 解析モード</h3>\n";
	my($by1_checked, $by2_checked, $by3_checked);
	if($by == 2) {
		$by2_checked = 'checked="checked "';
	} elsif($by == 3) {
		$by3_checked = 'checked="checked "';
	} else {
		$by1_checked = 'checked="checked "';
	}
	$Str .= "<div><input type=\"radio\" name=\"by\" value=\"1\" ${by1_checked}/> インプレッション数　　<input type=\"radio\" name=\"by\" value=\"2\" ${by2_checked}/> セッション数　　<input type=\"radio\" name=\"by\" value=\"3\" ${by3_checked}/> ユニークユーザー数</div>\n";
	$Str .= "<div>　</div>\n";
	$Str .= "<input type=\"submit\" name=\"b1\" value=\"　解　析　\" />\n";
	$Str .= "</form>\n";
	my @Titles;
	if($by == 1) {
		$Str .= "<h2>インプレッション数</h2>\n";
		$Str .= &MakeCircleGraph(\%PageViewNumList);
		@Titles = ('順位', '色深度', 'インプレッション数', 'グラフ');
		$Str .= &MakeGraph(\%PageViewNumList, \@Titles);
		undef %PageViewNumList;
	}
	if($by == 2) {
		$Str .= "<h2>セッション数</h2>\n";
		$Str .= &MakeCircleGraph(\%SessionNumList);
		@Titles = ('順位', '色深度', 'セッション数', 'グラフ');
		$Str .= &MakeGraph(\%SessionNumList, \@Titles);
		undef %SessionNumList;
	}
	if($by == 3) {
		$Str .= "<h2>ユニークユーザー数</h2>\n";
		$Str .= &MakeCircleGraph(\%UniqueNumList);
		@Titles = ('順位', '色深度', 'ユニークユーザー数', 'グラフ');
		$Str .= &MakeGraph(\%UniqueNumList, \@Titles);
		undef %UniqueNumList;
	}
	my $Title = 'クライアント画面色深度ランキング';
	&PrintResult($Title, $Str);
}

sub MakeTable {
	my($Keys, $Data) = @_;
	my($key);
	my($Str) = "<table class=\"tbl\">\n";
	for $key (@$Keys) {
		$Str .= "<tr>\n";
		$Str .= "  <th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\">$key</th>\n";
		$Str .= "  <td>$$Data{$key}</td>\n";
		$Str .= "</tr>\n";
	}
	$Str .= "</table>\n";
	return $Str;
}

sub GetUniqueUserNum2 {
	my($host_ref, $cookies_ref) = @_;
	my %unique_cookies;
	while( my($no, $v) = each %{$cookies_ref} ) {
		$unique_cookies{$v} ++;
	}
	my $n = 0;
	my %unique_remotehosts;
	my %reverse_cookies = reverse %{$cookies_ref};
	while( my($key, $v) = each %unique_cookies ) {
		if($v > 1) {
			$n ++;
		} else {
    		$unique_remotehosts{$host_ref->{$reverse_cookies{$key}}} ++;
		}
	}
	my $n2 = scalar keys %unique_remotehosts;
	$n += $n2;
	return $n;
}

sub GetUniqueUserNum {
	my($no_ref, $host_ref, $cookies_ref) = @_;
	my $i;
	my $UniqueNum = 0;
	my %UniqueCookies = ();
	for my $i (@$no_ref) {
		$UniqueCookies{$$cookies_ref{$i}} ++;
	}
	my $key;
	my %UniqueRemoteHosts = ();
	my %ReverseCookies = reverse %$cookies_ref;
	while( my($key, $v) = each %UniqueCookies ) {
		if($v > 1) {
			$UniqueNum ++;
		} else {
    		my $No = $ReverseCookies{$key};
    		$UniqueRemoteHosts{$$host_ref{$No}} ++;
		}
	}
	my $TempNum = scalar keys %UniqueRemoteHosts;
	$UniqueNum += $TempNum;
	return $UniqueNum;
}

sub GetSessionNum2 {
	my($date_ref, $host_ref, $cookies_ref) = @_;
	my %UniqueCookies;
	while( my($no, $v) = each %{$cookies_ref} ) {
		$UniqueCookies{$v} ++;
	}
	my %LastAccessDate;
	my %CookieLastAccessDate;
	my $n = 0;
	for my $i (sort {$a<=>$b} keys %{$cookies_ref}) {
		if($UniqueCookies{$cookies_ref->{$i}} > 1) {
			if(exists($CookieLastAccessDate{$cookies_ref->{$i}})) {
				my $diff = &GetSecDiff($$date_ref{$i}, $CookieLastAccessDate{$$cookies_ref{$i}});
				if($diff > $CONF{INTERVAL}) {
					$n ++;
				}
		    } else {
				$n ++;
			}
		} else {
			if(exists($LastAccessDate{$host_ref->{$i}})) {
				my $diff = &GetSecDiff($date_ref->{$i}, $LastAccessDate{$host_ref->{$i}});
				if($diff > $CONF{INTERVAL}) {
					$n ++;
				}
			} else {
				$n ++;
			}
		}
		$CookieLastAccessDate{$cookies_ref->{$i}} = $date_ref->{$i};
		$LastAccessDate{$host_ref->{$i}} = $date_ref->{$i};
	}
	return $n;
}

sub GetSessionNum {
	my($no_ref, $date_ref, $host_ref, $cookies_ref) = @_;
	my($i, $diff);
	my %UniqueCookies = ();
	for my $i (@$no_ref) {
		$UniqueCookies{$$cookies_ref{$i}} ++;
	}
	my %LastAccessDate  = ();
	my %CookieLastAccessDate = ();
	my $SessionNum = 0;
	for my $i (sort {$a<=>$b} @$no_ref) {
		if($UniqueCookies{$$cookies_ref{$i}} > 1) {
			if(exists($CookieLastAccessDate{$$cookies_ref{$i}})) {
				my $diff = &GetSecDiff($$date_ref{$i}, $CookieLastAccessDate{$$cookies_ref{$i}});
				if($diff > $CONF{'INTERVAL'}) {
					$SessionNum ++;
				}
		    } else {
				$SessionNum ++;
			}
		} else {
			if(exists($LastAccessDate{$$host_ref{$i}})) {
				$diff = &GetSecDiff($$date_ref{$i}, $LastAccessDate{$$host_ref{$i}});
				if($diff > $CONF{'INTERVAL'}) {
					$SessionNum ++;
				}
			} else {
				$SessionNum ++;
			}
		}
		$CookieLastAccessDate{$$cookies_ref{$i}} = $$date_ref{$i};
		$LastAccessDate{$$host_ref{$i}} = $$date_ref{$i};
	}
	return $SessionNum;
}

sub GetSecDiff {
	my(@DateList) = @_;
	my @TimeList = ();
	for my $DateStr (@DateList) {
		my ($Year, $Mon, $Day, $Hour, $Min, $Sec) = $DateStr =~ /^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/;
		$Year -= 1900;
		$Mon +=0;	$Mon --;
		$Day += 0;
		$Hour += 0;
		$Min += 0;
		$Sec += 0;
		my $Time = timelocal($Sec, $Min, $Hour, $Day, $Mon, $Year);
		push(@TimeList, $Time);
	}
	my $Diff = abs($TimeList[0] - $TimeList[1]);
	return $Diff;
}

sub GetToday {
	my @Date = localtime(time + $CONF{'TIMEDIFF'}*60*60);
	my $Year = $Date[5] + 1900;
	my $Mon = $Date[4] + 1;
	my $Day = $Date[3];
	$Mon = sprintf("%02d", $Mon);
	$Day = sprintf("%02d", $Day);
	return $Year.$Mon.$Day;
}

# 日時文字列（YYYYMMDDhhmmss）を YYYY/MM/DD hh:mm:ss に変換する
sub ConvDspDate {
	my($DateStr) = @_;
	my($Year, $Mon, $Day, $Hour, $Min, $Sec) = $DateStr =~ /^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/;
	return "$Year/$Mon/$Day $Hour:$Min:$Sec";
}

# 開始時刻と終了時刻を引数とし、その間の秒数を返す。
# 引数は、YYYYMMDDhhmmss 形式
sub GetRangeSecond {
	my($StartStr, $EndStr) = @_;
	my($MinYear, $MinMon, $MinDay, $MinHour, $MinMin, $MinSec) = $StartStr =~ /^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/;
	my($MaxYear, $MaxMon, $MaxDay, $MaxHour, $MaxMin, $MaxSec) = $EndStr =~ /^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/;
	$MinYear -= 1900;
	$MinMon += 0;	$MinMon --;
	$MinDay += 0;
	$MinHour += 0;
	$MinMin += 0;
	$MinSec += 0;
	$MaxYear -= 1900;
	$MaxMon += 0;	$MaxMon --;
	$MaxDay += 0;
	$MaxHour += 0;
	$MaxMin += 0;
	$MaxSec += 0;
	my($StartTime) = timelocal($MinSec, $MinMin, $MinHour, $MinDay, $MinMon, $MinYear);
	my($EndTime) = timelocal($MaxSec, $MaxMin, $MaxHour, $MaxDay, $MaxMon, $MaxYear);
	return abs($EndTime - $StartTime);
}


# ログファイルのサイズを調べる(KB)
sub AnalyzeLogfileSize {
	my($File) = @_;
	my @log_stat = stat($File);
	return $log_stat[7];
}

sub PrintResult {
	my($Title, $ResultStr, $tfile) = @_;
	unless($tfile) {
		$tfile = "$TEMPLATEDIR/result.html";
	}
	my $html = &ReadTemplate($tfile);
	$html =~ s/\%RESULT\%/$ResultStr/;
	$html =~ s/\%TITLE\%/$Title/;
	print "Content-type: text/html; charset=utf-8\n\n";
	print "$html\n";
	exit;
}

sub MakeGraph {
	my($InputData, $Titles) = @_;
	my %ElementList = %$InputData;
	my($Str);
	$Str .= "<table class=\"tbl\">\n";
	$Str .= "<tr>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\">$$Titles[0]</th>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\">$$Titles[1]</th>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\">$$Titles[2]</th>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\">$$Titles[3]</th>\n";
	$Str .= "</tr>\n";
	my $Sum = 0;
	for my $key (keys %ElementList) {
		$Sum += $ElementList{$key};
	}
	my $order = 1;
	my $dsp_order = 1;
	my($rate, $GraphLength, $pre_velue);
	for my $key ( sort {$ElementList{$b}<=>$ElementList{$a}} keys %ElementList ) {
		unless($ElementList{$key} == $pre_velue) {
			$dsp_order = $order;
			last if($dsp_order > $CONF{'ROW'});
		}
		$rate = int($ElementList{$key} * 10000 / $Sum) / 100;
		$GraphLength = int($CONF{'GRAPHMAXLENGTH'} * $rate / 100);
		my $v = &CommaFormat($ElementList{$key});
		$Str .= "<tr>\n";
		$Str .= "<td class=\"center\">$dsp_order</td>\n";
		$Str .= "<td>$key</td>\n";
		$Str .= "<td class=\"right\">${v}</td>\n";
		$Str .= "<td class=\"size2\">";
		if($rate < 1) {
			$Str .= "";
		} else {
			$Str .= "<img src=\"$CONF{'IMAGE_URL'}/graphbar\.gif\" width=\"$GraphLength\" height=\"10\" alt=\"${rate}\%\" class=\"gbar1\" />";
		}
		$Str .= " (${rate}%)</td></tr>\n";
		$pre_velue = $ElementList{$key};
		$order ++;
	}
	$Str .= "</table>\n";
	return $Str;
}

sub MakeGraph2 {
	my($InputData, $Titles, $ConvList) = @_;
	my %ElementList = %$InputData;
	my $Str;
	$Str .= "<table class=\"tbl\">\n";
	$Str .= "<tr>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\">$$Titles[0]</th>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\">$$Titles[1]</th>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\">$$Titles[2]</th>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\">$$Titles[3]</th>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\">$$Titles[4]</th>\n";
	$Str .= "</tr>\n";
	my $Sum = 0;
	for my $key (keys %ElementList) {
		$Sum += $ElementList{$key};
	}
	my $order = 1;
	my $dsp_order = 1;
	my($rate, $GraphLength, $pre_velue);
	for my $key ( sort {$ElementList{$b}<=>$ElementList{$a}} keys %ElementList ) {
		unless($ElementList{$key} == $pre_velue) {
			$dsp_order = $order;
			last if($dsp_order > $CONF{'ROW'});
		}
		$rate = int($ElementList{$key} * 10000 / $Sum) / 100;
		$GraphLength = int($CONF{'GRAPHMAXLENGTH'} * $rate / 100);
		my $v = &CommaFormat($ElementList{$key});
		$Str .= "<tr>\n";
		$Str .= "<td class=\"center\">$dsp_order</td>\n";
		$Str .= "<td>$key</td>\n";
		if($$ConvList{$key}) {
			$Str .= "<td>$$ConvList{$key}</td>\n";
		} else {
			$Str .= "<td>&nbsp;</td>\n";
		}
		$Str .= "<td class=\"right\">${v}</td>\n";
		$Str .= "<td class=\"size2\">";
		if($rate < 1) {
			$Str .= "";
		} else {
			$Str .= "<img src=\"$CONF{'IMAGE_URL'}/graphbar\.gif\" width=\"$GraphLength\" height=\"10\" alt=\"${rate}\%\" class=\"gbar1\" />";
		}
		$Str .= " (${rate}\%)</td></tr>\n";
		$pre_velue = $ElementList{$key};
		$order ++;
	}
	$Str .= "</table>";
	return $Str;
}

sub MakeGraph3 {
	my($InputData, $Titles, $Map) = @_;
	my %ElementList = %$InputData;
	my($Str);
	$Str .= "<table class=\"tbl\">\n";
	$Str .= "<tr>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\">$$Titles[0]</th>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\">$$Titles[1]</th>\n";
	$Str .= "<th style=\"background-image:url($CONF{IMAGE_URL}/barbg.gif);background-repeat:repeat-x;\">$$Titles[2]</th>\n";
	$Str .= "</tr>\n";
	my $Sum = 0;
	for my $key (keys %ElementList) {
		$Sum += $ElementList{$key};
	}
	my($rate, $GraphLength);
	foreach my $key (sort {$a <=> $b} (keys %ElementList)) {
		if($Sum == 0) {
			$rate = 0;
		} else {
			$rate = int($ElementList{$key} * 10000 / $Sum) / 100;
		}
		$GraphLength = int($CONF{'GRAPHMAXLENGTH'} * $rate / 100);
		my $v = &CommaFormat($ElementList{$key});
		#
		$Str .= "<tr>\n";
		if($Map) {
			$Str .= "<td>$$Map[$key]</td>\n";
		} else {
			$Str .= "<td>$key</td>\n";
		}
		$Str .= "  <td class=\"right\">${v}</td>\n";
		$Str .= "  <td class=\"size2\">";
		if($rate < 1) {
			$Str .= "";
		} else {
			$Str .= "<img src=\"$CONF{'IMAGE_URL'}/graphbar\.gif\" width=\"$GraphLength\" height=\"10\" alt=\"${rate}\%\" class=\"gbar1\" />";
		}
		$Str .= " ($rate%)</td></tr>\n";
	}
	$Str .= "</table>\n";
	return $Str;
}

sub MakeCircleGraph {
	my($InputData) = @_;
	my(%ElementList) = %$InputData;
	my($ItemNum);
	$ItemNum = scalar keys %$InputData;
	if($ItemNum > 10) {
		$ItemNum = 10;
	}
	my $Str;

	if($CONF{'CIRCLE_GLAPH'}) {
		$Str .= "<applet";
		$Str .= "  CODEBASE = \"$CONF{'IMAGE_URL'}\"\n";
		$Str .= "  CODE     = \"CircleGraph.class\"\n";
		$Str .= "  NAME     = \"CircleGraph\"\n";
		$Str .= "  WIDTH    = 400\n";
		$Str .= "  HEIGHT   = 220\n";
		$Str .= "  HSPACE   = 0\n";
		$Str .= "  VSPACE   = 0\n";
		$Str .= "  ALIGN    = top\n";
		$Str .= ">\n";
		$Str .= "<param name=\"ItemNum\" value=\"$ItemNum\" />\n";
		my $key;
		my $i = 1;
		my $OtherCnt = 0;
		for my $key ( sort {$ElementList{$b}<=>$ElementList{$a}} keys %ElementList ) {
			if($i < 10) {
	    		$Str .= "<param name=\"Name$i\" value=\"$key\" />\n";
	    		$Str .= "<param name=\"Value$i\" value=\"$ElementList{$key}\" />\n";
			} else {
				$OtherCnt += $ElementList{$key};
			}
			$i ++;
		}
		if($OtherCnt) {
			$Str .= "<param name=\"Name10\" value=\"その他\" />\n";
			$Str .= "<param name=\"Value10\" value=\"$OtherCnt\" />\n";
		}
		$Str .= "</applet>\n";
	} else {
		my $key;
		my $i = 1;
		my $OtherCnt = 0;
		my $list;
		for my $key ( sort {$ElementList{$b}<=>$ElementList{$a}} keys %ElementList ) {
			if($i < 10) {
				my $enc_name = &URL_Encode($key);
				$list .= "name$i=$enc_name&amp;";
				$list .= "value$i=$ElementList{$key}&amp;";
			} else {
				$OtherCnt += $ElementList{$key};
			}
			$i ++;
		}
		if($OtherCnt) {
			my $key = 'その他';
			my $enc_name = &URL_Encode($key);
			$list .= "name10=$enc_name&amp;value10=$OtherCnt";
		} else {
			$list =~ s/\&$//;
		}
		$Str .= "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\"\n";
		$Str .= "  codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0\"\n";
		$Str .= "  width=\"400\" height=\"220\">\n";
		$Str .= "  <param name=\"movie\" value=\"$CONF{'IMAGE_URL'}/CircleGraph.swf?$list\" />\n";
		$Str .= "  <param name=\"quality\" value=\"high\" />\n";
		$Str .= "  <param name=\"bgcolor\" value=\"#ffffff\" />\n";
		$Str .= "  <embed\n";
		$Str .= "    src=\"$CONF{'IMAGE_URL'}/CircleGraph.swf?$list\"\n";
		$Str .= "    quality=high bgcolor=#FFFFFF\n";
		$Str .= "    WIDTH=400 HEIGHT=220\n";
		$Str .= "    TYPE=\"application/x-shockwave-flash\"\n";
		$Str .= "    PLUGINSPAGE=\"http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash\">\n";
		$Str .= "  </embed>\n";
		$Str .= "</object>\n";
	}
	return $Str;
}

sub URL_Decode {
	my($string) = @_;
	$string =~ s/%([0-9A-Fa-f]{2})/chr(hex($1))/eg;
	$string =~ s/\+/ /g;
	return $string;
}

sub URL_Encode {
	my($string) = @_;
	$string =~ s/ /+/g;
	$string =~ s/([^A-Za-z0-9\+])/'%'.unpack("H2", $1)/ego;
	return $string;
}

# 西暦、月、日を引数に取り、曜日コードを返す。
# 日:0, 月:1, 火:2, 水:3, 木:4, 金:5, 土:6
sub Youbi {
	my($year, $month, $day) = @_;
	$month =~ s/^0//;
	if($month eq '') {return '';}
	$day =~ s/^0//;
	my($time) = timelocal(0, 0, 0, $day, $month - 1, $year);
	my(@date_array) = localtime($time);
	return $date_array[6];
}

# 西暦と月を引数に取り、該当月の最終日を返す
sub LastDay {
	my($year, $month) = @_;
	$month =~ s/^0//;
	if($month =~ /[^0-9]/ || $year =~ /[^0-9]/) {
		return '';
	}
	if($month < 1 && $month > 12) {
		return '';
	}
	if($year > 2037 || $year < 1900) {
		return '';
	}
	my($lastday) = 1;
	my($time) = timelocal(0, 0, 0, 1, $month-1, $year-1900);
	my(@date_array) = localtime($time);
	my($mon) = $date_array[4];
	my($flag) = 1;
	my($count) = 0;
	while($flag) {
		if($mon ne $date_array[4]) {
			return $lastday;
			$flag = 0;
		}
		$lastday = $date_array[3];
		$time = $time + (60 * 60 * 24);
		@date_array = localtime($time);
		$count ++;
		last if($count > 40);
	}
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

sub HtmlHeader {
	if($CONF{'AUTHFLAG'}) {
		print "P3P: CP=\"NOI TAIa\"\n";
		my $CookieHeaderString = &SetCookie('PASS', $CONF{'PASSWORD'});
		print "$CookieHeaderString\n";
	}
	print "Content-type: text/html; charset=utf-8\n\n";
	print "\n";
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
	$html .= "<body><p>${msg}</p></body></html>";
	print "Content-Type: text/html; charset=utf-8\n";
	print "\n";
	print $html;
	exit;
}

# 指定された定義ファイルを読み取り、連想配列を返す。
sub ReadDef {
	my($file) = @_;
	my(@buff, %array);
	open(FILE, "$file") || &ErrorPrint("$file をオープンできませんでした。");
	while(<FILE>) {
		if(/^\s*\#/) {next;}
		chomp;
		@buff = split(/=/);
		if($buff[0] && $buff[1]) {
			$array{$buff[0]} = $buff[1];
		} else {
			next;
		}
	}
	close(FILE);
	return %array;
}

sub ReadTitleDat {
	my $file = './data/title.dat';
	my(@buff, %array);
	open(FILE, "$file") || &ErrorPrint("$file をオープンできませんでした。");
	while(my $line=<FILE>) {
		if($line =~ /^\s*\#/) {next;}
		chop $line;
		if($line =~ /^([^\t]+)\t+(.+)$/) {
			$array{$1} = $2;
		} elsif($line =~ /^([^\=]+)\=(.+)$/) {
			$array{$1} = $2;
		} else {
			next;
		}
	}
	close(FILE);
	return %array;
}

sub PrintAuthForm {
	my($Repeat) = @_;
	my $html = &ReadTemplate("$TEMPLATEDIR/logon.html");
	my $error;
	if($Repeat) {
		$error = 'パスワードが違います。';
	}
	$html =~ s/\%error\%/$error/g;
	my $content_length = length $html;
	if($ENV{'SERVER_NAME'} !~ /($FREE_SERVER_NAME)/) {
		print "Content-Length: $content_length\n";
	}
	print "Content-Type: text/html; charset=utf-8\n";
	print "\n";
	print $html;
	exit;
}

sub SetCookie {
	my($CookieName, $CookieValue) = @_;
	# URLエンコード
	$CookieValue =~ s/([^\w\=\& ])/'%' . unpack("H2", $1)/eg;
	$CookieValue =~ tr/ /+/;
	my($CookieHeaderString) = "Set-Cookie: $CookieName=$CookieValue\;";
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
    my($name) = @_;
    #Cookie有効期限を 1970/1/1 00:00:00 にセットする。
    my $expire = 'Thu, 01-Jan-1970 00:00:00 GMT';
    #Set-Cookieヘッダーを生成する。
    #Cookie値は何でも良いので、ここでは clear という文字列を入れる。
    my $cookie_header = "Set-Cookie: $name=clear; expires=$expire;";
    #Set-Cookieヘッダーの最後に改行を加える。
    $cookie_header .= "\n";
    #Set-Cookieヘッダーを返す。
    return $cookie_header;
}

#指定したURL(URI)から、HTMLタイトルを取得する。
sub GetHtmlTitle {
	my($URL) = @_;
	my($Title, $Path);
	my $HtmlFile;
	if($CONF{'URL2PATH_FLAG'}) {
		my($key);
		for $key (keys %URL2PATH) {
			if($URL =~ /^$key/) {
				$HtmlFile = $URL;
				$HtmlFile =~ s/^$key/$URL2PATH{$key}/;
			}
		}
		unless($HtmlFile) {
			return '';
		}
	} else {
		$_ = $URL;
		m|https*://[^/]+/(.*)|;
		$Path = '/'.$1;
		$HtmlFile = $ENV{'DOCUMENT_ROOT'}.$Path;
	}
	$HtmlFile =~ s/\?.*$//;
	$HtmlFile =~ s/\#.*$//;
	unless(-e $HtmlFile) {return ''};
	my $size = -s $HtmlFile;
	if(!open(HTML, "$HtmlFile")) {
		return '';
	}
	binmode(HTML);	# For Windows
	my $buf;
	sysread(HTML, $buf, $size);
	close(HTML);
	if( $buf =~ /<title[^>]*>([^<]*)<\/title>/i ) {
		$Title = $1;
	}
	if($Title) {
		&Jcode::convert(\$Title, "utf8");
		return $Title;
	} else {
		return '';
	}
}

sub GetLogDateList {
	my($LogFile) = @_;
	my @DateList = ();
	if(-e $LogFile) {
		open(LOGFILE, "$LogFile") || return @DateList;
	} else {
		return @DateList;
	}
	my($Buff, $DateBuff, %DateListBuff);
	while(<LOGFILE>) {
		chomp;
		next if($_ eq '');
		($Buff) = split(/\s/);
		unless($Buff) {next;}
		if($Buff !~ /^\d{14}$/) { next; }
		$DateBuff = substr($Buff, 0, 8);
		$DateListBuff{$DateBuff} ++;
	}
	close(LOGFILE);
	my $key;
	for $key (sort keys %DateListBuff) {
		if($key =~ /^[0-9]{8}$/) {
			push(@DateList, $key);
		}
	}
	return @DateList;
}


sub GetLastMonth {
	my($ThisYearMonth) = @_;
	my $ThisYear = substr($ThisYearMonth, 0, 4);
	my $ThisMonth = substr($ThisYearMonth, 4, 2);
	$ThisMonth =~ s/^0//;
	my($LastMonth, $LastYear);
	if($ThisMonth == 1) {
		$LastMonth = 12;
		$LastYear = $ThisYear - 1;
	} else {
		$LastMonth = $ThisMonth - 1;
		$LastYear = $ThisYear;
	}
	if($LastMonth < 10) {
		$LastMonth = "0$LastMonth";
	}
	return "$LastYear$LastMonth";
}


sub GetNextMonth {
	my($ThisYearMonth) = @_;
	my $ThisYear = substr($ThisYearMonth, 0, 4);
	my $ThisMonth = substr($ThisYearMonth, 4, 2);
	$ThisMonth =~ s/^0//;
	my($NextMonth, $NextYear);
	if($ThisMonth == 12) {
		$NextMonth = 1;
		$NextYear = $ThisYear + 1;
	} else {
		$NextMonth = $ThisMonth + 1;
		$NextYear = $ThisYear;
	}
	if($NextMonth < 10) {
		$NextMonth = "0$NextMonth";
	}
	return "$NextYear$NextMonth";
}


sub GetPrefKeyword {
	my($HostName) = @_;
	my (@HostParts, @TmpList);
	my $key = '';
	@HostParts = split(/\./, $HostName);
	if($HostName =~ /\.ocn\.ne\.jp$/) {
		$key = $HostParts[1];
	} elsif($HostName =~ /\.infoweb\.ne\.jp$/) {
		if($HostName =~ /^\w+\.(\w+)\.\w+\.\w+\.ppp\.infoweb\.ne\.jp$/) {
			$key = $1;
		} elsif($HostParts[0] =~ /^(nt|te)tkyo/) {
			$key = 'tkyo';
		} elsif($HostParts[0] =~ /^ntt([a-z]{4})/) {
			$key = $1;
		} elsif($HostParts[0] =~ /^(ea|ac|nt|ho|ct|st|th|ht|tc)([a-z]{4})/) {
			$key = $2;
		} elsif($HostParts[0] =~ /^([a-z]+)/) {
			$key = $1;
		}
	} elsif($HostName =~ /\.mesh\.ad\.jp$/) {
		$key = $HostParts[1];
		$key =~ s/[0-9]+$//;
	} elsif($HostName =~ /\.s[^\.]+\.a([^\.]+)\.ap\.plala\.or\.jp$/) {
		$key = 'a' . $1;
	} elsif($HostName =~ /\.dion\.ne\.jp$/) {
		$key = $HostParts[0];
		$key =~ s/[0-9\-]+//;
	} elsif($HostName =~ /\.hi-ho\.ne\.jp$/) {
		@TmpList = split(/\-/, $HostParts[0]);
		$key = $TmpList[0];
		$key =~ s/^(adsl|ea|ip)//;
		$key =~ s/[0-9]+$//;
	} elsif($HostName =~ /\.so-net\.ne\.jp$/) {
		if($HostParts[1] =~ /^ntt/) {
			$key = substr($HostParts[1], 3, 4);
		} else {
			$key = substr($HostParts[1], 0, 4);
		}
	} elsif($HostName =~ /\.dti\.ne\.jp$/) {
		@TmpList = split(/\-/, $HostParts[1]);
		$key = $TmpList[0];
	} elsif($HostName =~ /\.alpha-net\.ne\.jp$/) {
		@TmpList = split(/\-/, $HostName);
		if($TmpList[0] =~ /^fl/) {
			$_ = $TmpList[1];
		} else {
			$_ = $TmpList[0];
		}
		m/^([a-zA-Z]+)/;
		$key = $1;
	} elsif($HostName =~ /\.[A-Z]([a-z]+?)FL[0-9]+\.vectant\.ne\.jp$/i) {
		$key = $1;
	} elsif($HostName =~ /\.att\.ne\.jp$/) {
		$key = $HostParts[2];
		$key =~ s/^(ipc|dsl|ftth|newfamily)//;
		$key =~ s/^\d+m//;
		$key =~ s/[0-9]+$//;
	} elsif($HostName =~ /\.([a-zA-Z]+)\d+\.bbiq\.jp$/) {
		$key = $1;
	} elsif($HostName =~ /\.coara\.or\.jp$/) {
		@TmpList = split(/\-/, $HostParts[0]);
		$key = $TmpList[0];
		$key =~ s/[0-9]+$//;
		$key =~ s/ap$//;
	} elsif($HostName =~ /\.highway\.ne\.jp$/) {
		$key = $HostParts[1];
		$key =~ s/^ip\-//;
		$key =~ s/^e\-//;
	} elsif($HostName =~ /\.interq\.or\.jp$/) {
		@TmpList = split(/\-/, $HostParts[0]);
		$key = $TmpList[0];
		$key =~ s/ipconnect$//;
		$key =~ s/[0-9]+$//;
	} elsif($HostName =~ /\.mbn\.or\.jp$/) {
		$key = $HostParts[1];
	} elsif($HostName =~ /\.psinet.ne.jp$/) {
		$key = $HostParts[1];
		$key =~ s/^fli\-//;
	} elsif($HostName =~ /\.sannet\.ne\.jp$/) {
		$key = $HostParts[1];
	} elsif($HostName =~ /\.uu\.net$/) {
		$key = $HostParts[2];
		$key =~ s/[0-9]+$//;
	} elsif($HostName =~ /\.zero\.ad\.jp$/) {
		@TmpList = split(/\-/, $HostParts[0]);
		if($TmpList[0] eq 'f') {
			$key = $TmpList[1];
		} else {
			$key = $TmpList[0];
		}
		$key =~ s/[0-9]+$//;
	} elsif($HostName =~ /\.pias\.ne\.jp$/) {
		$_ = $HostParts[0];
		m/^([a-zA-Z]+)/;
		$key = $1;
	} elsif($HostName =~ /\.nttpc\.ne\.jp$/) {
		$key = $HostParts[2];
	} elsif($HostName =~ /\.interlink\.or\.jp$/) {
		$key = $HostParts[1];
	} elsif($HostName =~ /\.kcom\.ne\.jp$/) {
		$key = $HostParts[1];
		$key =~ s/[0-9\-]+$//;
	} elsif($HostName =~ /\.home\.ne\.jp$/) {
		$key = $HostParts[1];
		$key =~ s/[0-9]+$//;
	} elsif($HostName =~ /\.isao\.net$/) {
		$key = $HostParts[1];
		$key =~ s/[0-9]+$//;
	} elsif($HostName =~ /\.(hokkaido|aomori|iwate|miyagi|akita|yamagata|fukushima|ibaraki|tochigi|gunma|saitama|chiba|tokyo|kanagawa|niigata|toyama|ishikawa|fukui|yamanashi|nagano|gifu|shizuoka|aichi|mie|shiga|kyoto|osaka|hyogo|nara|wakayama|tottori|shimane|okayama|hiroshima|yamaguchi|tokushima|kagawa|ehime|kochi|fukuoka|saga|nagasaki|kumamoto|oita|miyazaki|kagoshima|okinawa|sapporo|sendai|chiba|yokohama|kawasaki|nagoya|kyoto|osaka|kobe|hiroshima|fukuoka|kitakyushu)\.jp$/) {
		$key = $1;
	}
	return $key;
}

sub GetConf {
	my($file) = @_;
	my %data = ();
	open(FILE, "$file") || &ErrorPrint("設定ファイル <tt>$file</tt> をオープンできませんでした。: $!");
	while(<FILE>) {
		chomp;
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
	$html =~ s/\%COPYRIGHT\%/$COPYRIGHT/;
	$html =~ s/\%COPYRIGHT2\%/$COPYRIGHT2/g;
	$html =~ s/\%COPYRIGHT3\%/$COPYRIGHT3/g;
	$html =~ s/\%COPYRIGHT4\%/$COPYRIGHT4/g;
	$html =~ s/\%CGI_URL\%/$CGI_URL/g;
	$html =~ s/\%IMAGE_URL\%/$CONF{IMAGE_URL}/g;
	return $html;
}

sub CheckHoliday {
	my($year, $mon, $day) = @_;
	#指定日が存在する日かをチェックする
	my $time;
	eval {$time = timelocal(0, 0, 0, $day, $mon-1, $year);};
	if($@) {return -1;}
	#当日を特定する。
	my @list = localtime($time);
	my $today = sprintf("%02d", $mon).sprintf("%02d", $day);
	my $youbi = $list[6];
	my $order = int(($day-1) / 7) + 1;
	#当日が日曜日かどうかをチェック
	#日曜日であれば、無条件で1を返す。
	if($youbi == 0) {return 1;}
	#1970年より前であれば終了
	if($year < 1970) { return 0; }
	#「国民の祝日に関する法律」が施工された1948年7月20日より前であれば終了
	#if( $year < 1948 || ( $year == 1948 && $today lt "0720") ) { return 0; }
	#----------------------------------------------------------------
	#国民の祝日（国民の祝日に関する法律 第二条）
	#----------------------------------------------------------------
	#日付が決まっている祝日を定義
	my @fix_horidays = (
		'0101',		#元日
		'0429',		#1988年以前は「天皇誕生日」、1989年～2006年までは「みどりの日」、2007年～は「昭和の日」と改名
		'0503',		#憲法記念日
		'0505',		#こどもの日
		'1103',		#文化の日
		'1123'		#勤労感謝の日
	);
	#成人の日 - ～ 1999年は1月15日、2000年以降はハッピーマンデー適用のため1月の第2月曜日
	if($year <= 1999) {
		push(@fix_horidays, '0115');
	} elsif($year >= 2000) {
		my($y, $m, $d) = Date::Pcalc::Nth_Weekday_of_Month_Year($year, 1, 1, 2);
		$m = sprintf("%02d", $m);
		$d = sprintf("%02d", $d);
		push(@fix_horidays, "${m}${d}");
	}
	#建国記念日 - 1967年（昭和42年）～
	if($year >= 1967) {
		push(@fix_horidays, '0211');
	}
	#昭和天皇の大喪の礼 2月24日 - 1989年（平成元年）のみ
	if($year == 1989) {
		push(@fix_horidays, '0224');
	}
	#皇太子明仁親王の結婚の儀 4月10日 - 1959年（昭和34年）のみ
	if($year == 1959) {
		push(@fix_horidays, '0410');
	}
	#みどりの日 - 2007年～
	if($year >= 2007) {
		push(@fix_horidays, '0504');
	}
	#皇太子徳仁親王の結婚の儀 6月9日 - 1993年（平成5年）のみ
	if($year == 1993) {
		push(@fix_horidays, '0609');
	}
	#海の日 - 1996年～2002年は7月20日、2003年以降はハッピーマンデー適用のため7月の第3月曜日
	if($year >= 1996 && $year <= 2002) {
		push(@fix_horidays, '0720');
	} elsif($year >= 2003) {
		my($y, $m, $d) = Date::Pcalc::Nth_Weekday_of_Month_Year($year, 7, 1, 3);
		$m = sprintf("%02d", $m);
		$d = sprintf("%02d", $d);
		push(@fix_horidays, "${m}${d}");
	}
	#敬老の日 - 1966年（昭和42年）～ 2002年は9月15日、2003年以降はハッピーマンデー適用のため9月の第3月曜日
	if($year >= 1966 && $year <= 2002) {
		push(@fix_horidays, '0915');
	} elsif($year >= 2003) {
		my($y, $m, $d) = Date::Pcalc::Nth_Weekday_of_Month_Year($year, 9, 1, 3);
		$m = sprintf("%02d", $m);
		$d = sprintf("%02d", $d);
		push(@fix_horidays, "${m}${d}");
	}
	#体育の日 - 1966年（昭和42年）～ 1999年は10月10日、2000年以降はハッピーマンデー適用のため10月の第2月曜日
	if($year >= 1966 && $year <= 1999) {
		push(@fix_horidays, '1010');
	} elsif($year >= 2000) {
		my($y, $m, $d) = Date::Pcalc::Nth_Weekday_of_Month_Year($year, 10, 1, 2);
		$m = sprintf("%02d", $m);
		$d = sprintf("%02d", $d);
		push(@fix_horidays, "${m}${d}");
	}
	#即位礼正殿の儀 11月12日 - 1990年（平成2年）のみ
	if($year == 1990) {
		push(@fix_horidays, '1112');
	}
	#今上天皇誕生日（平成） - 1989年～
	if($year >= 1989) {
		push(@fix_horidays, '1223');
	}
	#春分の日と秋分の日を挿入する。
	if($year % 4 == 0) {
		#春分の日
		if($year <= 1956) {
			push(@fix_horidays, '0321');
		} elsif($year <= 2088) {
			push(@fix_horidays, '0320');
		} else {
			push(@fix_horidays, '0319');
		}
		#秋分の日
		if($year <= 2008) {
			push(@fix_horidays, '0923');
		} else {
			push(@fix_horidays, '0922');
		}
	} elsif($year % 4 == 1) {
		#春分の日
		if($year <= 1989) {
			push(@fix_horidays, '0321');
		} else {
			push(@fix_horidays, '0320');
		}
		#秋分の日
		if($year <= 1917) {
			push(@fix_horidays, '0924');
		} elsif($year <= 2041) {
			push(@fix_horidays, '0923');
		} else {
			push(@fix_horidays, '0922');
		}
	} elsif($year % 4 == 2) {
		#春分の日
		if($year <= 2022) {
			push(@fix_horidays, '0321');
		} else {
			push(@fix_horidays, '0320');
		}
		#秋分の日
		if($year <= 1946) {
			push(@fix_horidays, '0924');
		} elsif($year <= 2074) {
			push(@fix_horidays, '0923');
		} else {
			push(@fix_horidays, '0922');
		}
	} elsif($year % 4 == 3) {
		#春分の日
		if($year <= 1923) {
			push(@fix_horidays, '0322');
		} elsif($year <= 2055) {
			push(@fix_horidays, '0321');
		} else {
			push(@fix_horidays, '0320');
		}
		#秋分の日
		if($year <= 1979) {
			push(@fix_horidays, '0924');
		} else {
			push(@fix_horidays, '0923');
		}
	}
	#当日が「国民の祝日」であれば、1を返す。
	if(grep(/^$today$/, @fix_horidays)) {return 1;}
	#----------------------------------------------------------------
	#振替休日（国民の祝日に関する法律 第三条２）
	#1973年に、祝日が日曜日の場合はその翌日を休日とする振替休日を制定。
	#初適用日は同年4月30日
	#2007年に規定が変更され、「国民の祝日」が日曜日に当たるときは、
	#その日後においてその日に最も近い「国民の祝日」でない日を休日とす
	#ることとなった。
	#----------------------------------------------------------------
	if($year >= 1973 && $year <= 2006) {
		my($s, $m, $h, $D, $M, $Y, $w) = localtime($time-86400);
		my $yesterday = sprintf("%02d", $M+1).sprintf("%02d", $D);
		#前日が「国民の祝日」で、かつ日曜日の場合には、当日は休日
		if( grep(/^${yesterday}$/, @fix_horidays) && $w == 0 ) {return 1;}
	} elsif($year >= 2007) {
		for(my $i=1; $i<=7; $i++) {
			my($s, $m, $h, $D, $M, $Y, $w) = localtime( $time - $i*86400 );
			my $md = sprintf("%02d", $M+1).sprintf("%02d", $D);
			my $hflag = grep(/^${md}$/, @fix_horidays);
			if($hflag) {
				if($w == 0) {
					return 1;
				}
			} else {
				last;
			}
		}
	}
	#----------------------------------------------------------------
	#国民の休日（国民の祝日に関する法律 第三条３）
	#1985年に、二つの祝日に挟まれた平日を休日とする国民の休日を制定。
	#初適用日は1988年5月4日
	#----------------------------------------------------------------
	if($year >= 1988) {
		#前日を特定する
		my @ysd = localtime($time-86400);
		my $yesterday = sprintf("%02d", $ysd[4]+1).sprintf("%02d", $ysd[3]);
		#翌日を特定する
		my @tmr = localtime($time+86400);
		my $tomorrow = sprintf("%02d", $tmr[4]+1).sprintf("%02d", $tmr[3]);
		#昨日と明日がともに「国民の祝日」の場合には、当日は休日。
		if( grep(/^$yesterday$/, @fix_horidays) && grep(/^$tomorrow$/, @fix_horidays) ) {return 1;}
	}
	#以上のチェックにひっかからなかったら、休日でない。
	return 0;
}

sub SpecifyLogFileName {
	my $filename = $q->param('LOG');
	if($filename) {
		if($filename =~ /[^a-zA-Z0-9\_\.\-]/) {
			&ErrorPrint('不正な値が送信されました。');
		}
	} else {
		my $date_str = &GetToday;
		my $mon_str = substr($date_str, 0, 6) . '00';
		if($CONF{'LOTATION'} == 2) {	#日ごとのローテーション
			$filename = "$PRE_LOGNAME\.$date_str\.cgi";
		} elsif($CONF{'LOTATION'} == 3) {	#月ごとのローテーション
			$filename = "$PRE_LOGNAME\.$mon_str\.cgi";
		} elsif($CONF{'LOTATION'} == 4) {	#週ごとのローテーション
			my $t = time + $CONF{'TIMEDIFF'}*60*60;
			my @date_array = localtime($t);
			my $wday = $date_array[6];
			my $epoc_time = $t;
			$epoc_time -= $wday*60*60*24;
			@date_array = localtime($epoc_time);
			my $day = $date_array[3];
			if($day < 10) {$day = "0$day";}
			my $mon = $date_array[4];
			$mon ++;	if($mon < 10) {$mon = "0$mon";}
			my $year = $date_array[5];
			$year += 1900;
			$filename = "$PRE_LOGNAME\.$year$mon$day\.cgi";
		} else {
			$filename = "$PRE_LOGNAME\.cgi";
		}
	}
	unless(-e "${LOGDIR}/${filename}") {
		opendir(DIR, "${LOGDIR}");
		my @files = readdir(DIR);
		closedir(DIR);
		my $mtime = 0;
		for my $f (@files) {
			if($f !~ /^access_log/) { next; }
			my $m = (stat("${LOGDIR}/${f}"))[9];
			if($m >= $mtime) {
				$filename = $f;
				$mtime = $m;
			}
		}
	}
	return $filename
}

sub RedirectPage {
	my($url) = @_;
	print "Content-Type: text/html; charset=utf-8\n";
	print "\n";
	print "<html><head><title>$COPYRIGHT</title>";
	print "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0; URL=$url\">";
	print "</head><body>\n";
	print "自動的に転送します。転送しない場合には<a href=\"$url\">こちら</a>をクリックして下さい。\n";
	print "</body></html>\n";
	exit;
}

sub GetDomainByHostname {
	my($host) = @_;
	my %tld_fix = (
		'com' =>'2', 'net'=>'2', 'org'=>'2', 'biz'=>'2', 'info'=>'2', 'name'=>'3',
		'aero'=>'2', 'coop'=>'2', 'museum'=>'2', 'pro'=>'3', 'edu'=>'2', 'gov'=>'2',
		'mil'=>'2', 'int'=>'2', 'arpa'=>'2', 'nato'=>'2', 
		'hk'=>'3', 'sg'=>'3', 'kr'=>'3', 'uk'=>'3', 'au'=>'3', 'mx'=>'3', 'th'=>'3', 'br'=>'3', 'pe'=>'3', 'nz'=>'3'
	);
	my %sld_fix = (
		#日本
		'ac.jp'=>'3', 'ad.jp'=>'3', 'co.jp'=>'3', 'ed.jp'=>'3', 'go.jp'=>'3',
		'gr.jp'=>'3', 'lg.jp'=>'3', 'ne.jp'=>'3', 'or.jp'=>'3',
		'hokkaido.jp'=>'3', 'aomori.jp'=>'3', 'iwate.jp'=>'3', 'miyagi.jp'=>'3',
		'akita.jp'=>'3', 'yamagata.jp'=>'3', 'fukushima.jp'=>'3', 'ibaraki.jp'=>'3',
		'tochigi.jp'=>'3', 'gunma.jp'=>'3', 'saitama.jp'=>'3', 'chiba.jp'=>'3',
		'tokyo.jp'=>'3', 'kanagawa.jp'=>'3', 'niigata.jp'=>'3', 'toyama.jp'=>'3',
		'ishikawa.jp'=>'3', 'fukui.jp'=>'3', 'yamanashi.jp'=>'3', 'nagano.jp'=>'3',
		'gifu.jp'=>'3', 'shizuoka.jp'=>'3', 'aichi.jp'=>'3', 'mie.jp'=>'3',
		'shiga.jp'=>'3', 'kyoto.jp'=>'3', 'osaka.jp'=>'3', 'hyogo.jp'=>'3',
		'nara.jp'=>'3', 'wakayama.jp'=>'3', 'tottori.jp'=>'3', 'shimane.jp'=>'3',
		'okayama.jp'=>'3', 'hiroshima.jp'=>'3', 'yamaguchi.jp'=>'3', 'tokushima.jp'=>'3',
		'kagawa.jp'=>'3', 'ehime.jp'=>'3', 'kochi.jp'=>'3', 'fukuoka.jp'=>'3',
		'saga.jp'=>'3', 'nagasaki.jp'=>'3', 'kumamoto.jp'=>'3', 'oita.jp'=>'3',
		'miyazaki.jp'=>'3', 'kagoshima.jp'=>'3', 'okinawa.jp'=>'3', 'sapporo.jp'=>'3',
		'sendai.jp'=>'3', 'chiba.jp'=>'3', 'yokohama.jp'=>'3', 'kawasaki.jp'=>'3',
		'nagoya.jp'=>'3', 'kyoto.jp'=>'3', 'osaka.jp'=>'3', 'kobe.jp'=>'3',
		'hiroshima.jp'=>'3', 'fukuoka.jp'=>'3', 'kitakyushu.jp'=>'3',
		#台湾
		'com.tw'=>'3', 'net.tw'=>'3', 'org.tw'=>'3', 'idv.tw'=>'3', 'game.tw'=>'3',
		'ebiz.tw'=>'3', 'club.tw'=>'3', 'edu.tw'=>'3',
		#中国
		'com.cn'=>'3', 'net.cn'=>'3', 'org.cn'=>'3', 'gov.cn'=>'3', 'ac.cn'=>'3',
		'edu.cn'=>'3'
	);
	my($level3, $level2, $level1) = $host =~ /([^\.]+)\.([^\.]+)\.([^\.]+)$/;
	my $org_domain;
	if(my $dom_level = $tld_fix{$level1}) {
		if($dom_level eq '2') {
			$org_domain = "${level2}.${level1}";
		} else {
			$org_domain = "${level3}.${level2}.${level1}";
		}
	} elsif($sld_fix{"${level2}.${level1}"}) {
		$org_domain = "${level3}.${level2}.${level1}";
	} else {
		$org_domain = "${level2}.${level1}";
	}
	return $org_domain;
}

sub CommaFormat {
	my($num) = @_;
	#数字とドット以外の文字が含まれていたら、引数をそのまま返す。
	if($num =~ /[^0-9\.]/) {return $num;}
	#整数部分と小数点を分離
	my($int, $decimal) = split(/\./, $num);
	#整数部分の桁数を調べる
	my $figure = length $int;
	my $commaformat;
	#整数部分にカンマを挿入
	for(my $i=1;$i<=$figure;$i++) {
		my $n = substr($int, $figure-$i, 1);
		if(($i-1) % 3 == 0 && $i != 1) {
			$commaformat = "$n,$commaformat";
		} else {
			$commaformat = "$n$commaformat";
		}
	}
	#小数点があれば、それを加える
	if($decimal) {
		$commaformat .= "\.$decimal";
	}
	#結果を返す
	return $commaformat;
}

sub RoundOff {
	my($num, $place) = @_;
	#$placeが指定されていない場合には、0をセットする
	if($place eq '' || $place eq '-0') {$place = 0;}
	if($place >= 0) {
		#小数点1位以下を四捨五入する場合
		$num = sprintf("%.${place}f", $num);
		return $num;
	} else {
		#1の位以上を四捨五入する場合
		#四捨五入するポイントまで桁を下げる
		$num = $num * (10 ** $place);
		#小数点第1位を四捨五入
		$num = sprintf("%.0f", $num);
		#桁を元に戻す
		$num = $num * (10 ** abs($place));
		#結果を返す
		return $num;
	}
}

sub User_Agent {
	my($user_agent, $remote_host) = @_;
	my($platform, @agentPart, $browser, $browser_v);
	my($platform_v, @agentPart2, $user_agent2, @buff, @buff2, @buff3);
	my($flag, $key, @version_buff);
	if($user_agent =~ /Trend Micro/) {
		return;
	}
	if($user_agent =~ /DoCoMo/i) {
		$platform = 'DoCoMo';
		@agentPart = split(/\//, $user_agent);
		$browser = 'DoCoMo';
		$browser_v = $agentPart[1];
		$platform_v = $agentPart[2];
		if($platform_v eq '') {
			if($user_agent =~ /DoCoMo\/([0-9\.]+)\s+([0-9a-zA-Z]+)/) {
				$browser_v = $1;
				$platform_v = $2;
			}
		}
	} elsif($user_agent =~ /NetPositive/i) {
		$browser = 'NetPositive';
		if($user_agent =~ /NetPositive\/([0-9\.\-]+)/) {
			$browser_v = $1;
		}
		$platform = 'BeOS';
		$platform_v = '';
	} elsif($user_agent =~ /OmniWeb/) {
		$browser = 'OmniWeb';
		if($user_agent =~ /Mac_PowerPC/i) {
			$platform = 'MacOS';
			$platform_v = '';
		} else {
			$platform = '';
			$platform_v = '';
		}
		if($user_agent =~ /OmniWeb\/([0-9\.]+)/) {
			$browser_v = $1;
		} else {
			$browser_v = '';
		}
	} elsif($user_agent =~ /Cuam/i) {
		$browser = 'Cuam';
		$platform = 'Windows';
		$browser_v = '';
		$platform_v = '';
		if($user_agent =~ /Cuam Ver([0-9\.]+)/i) {
			$platform_v = '';
			$browser_v = $1;
		} else {
			if($user_agent =~ /Windows\s+([^\;\)]+)/) {
				$platform_v = $1;
			}
			if($user_agent =~ /Cuam\s+(0-9a-z\.)/) {
				$browser_v = $1;
			}
		}
	} elsif($user_agent =~ /^JustView\/([0-9\.]+)/) {
		$platform = 'Windows';
		$platform_v = '';
		$browser = 'JustView';
		$browser_v = $1;
	} elsif($user_agent =~ /^sharp pda browser\/([0-9\.]+).*\((.+)\//) {
		$platform = 'ZAURUS';
		$platform_v = $2;
		$browser = 'sharp_pda_browser';
		$browser_v = $1;
	} elsif($user_agent =~ /DreamPassport\/([0-9\.]+)/) {
		$platform = 'Dreamcast';
		$platform_v = '';
		$browser = 'DreamPassport';
		$browser_v = $1;
	} elsif($user_agent =~ /\(PLAYSTATION\s*(\d+)\;\s*([\d\.]+)\)/) {
		$platform = 'PlayStation';
		$platform_v = "PlayStation $1";
		$browser = $platform_v;
		$browser_v = $2;
	} elsif($user_agent =~ /\(PS\d+\s*\(PlayStation\s*(\d+)\)\;\s*([\d\.]+)\)/) {
		$platform = 'PlayStation';
		$platform_v = "PlayStation $1";
		$browser = $platform_v;
		$browser_v = $2;
	} elsif($user_agent =~ /\(PlayStation Portable\)\;\s*([\d\.]+)/) {
		$platform = 'PlayStation';
		$platform_v = "PlayStation Portable";
		$browser = $platform_v;
		$browser_v = $1;
	} elsif($user_agent =~ /^Sonybrowser2 \(.+\/PlayStation2 .+\)/) {
		$platform = 'PlayStation';
		$platform_v = 'PlayStation 2';
		$browser = 'Sonybrowser2';
		$browser_v = '';
	} elsif($user_agent =~ /Opera\/([\d\.]+)\s*\(Nintendo Wii/i) {
		$platform = 'Nintendo';
		$platform_v = 'Wii';
		$browser = 'Opera';
		$browser_v = $1;

	} elsif($user_agent =~ /Nitro/ && $user_agent =~ /Opera\s+([\d\.]+)/) {
		$platform = 'Nintendo';
		$platform_v = 'DS';
		$browser = 'Opera';
		$browser_v = $1;
	} elsif($user_agent =~ /(CBBoard|CBBstandard)\-[0-9\.]+/) {
		$platform = 'DoCoMo';
		$platform_v = 'ColorBrowserBorad';
		$browser = 'DoCoMo';
		$browser_v = 'ColorBrowserBorad';
	} elsif($user_agent =~ /^PDXGW/) {
		$platform = 'DDI POCKET';
		$platform_v = 'H"';
		$browser = 'DDI POCKET';
		$browser_v = 'H"';
	} elsif($user_agent =~ /^Sleipnir Version ([0-9\.]+)/) {
		$browser = 'Sleipnir';
		$browser_v = $1;
		$platform = 'Windows';
		$platform_v = '';
	} elsif($user_agent =~ /AppleWebKit/ && $user_agent =~ / Safari/) {
		if($user_agent =~ /Mobile\/([a-zA-Z0-9\-\.]+)/) {
			$platform_v = $1;
			if($user_agent =~ /\(iPod\;/) {
				$platform = 'iPod touch';
			} elsif($user_agent =~ /\(iPhone[\s\;]/) {
				$platform = 'iPhone';
			}
			$browser = 'Safari Mobile';
			($browser_v) = $user_agent =~ /Version\/(\d+\.\d+)/;
		} else {
			my($build) = $user_agent =~ /Safari\/([\d\.]+)/;
			$platform = 'MacOS';
			$browser = 'Safari';
			if($build eq "523.12") {
				$browser_v = '3.0以上';
				$platform_v = '10.4';
			} elsif($build >= 523) {
				$browser_v = '3.0以上';
				$platform_v = '10.5';
			} elsif($build >= 412) {
				$browser_v = '2.0';
				$platform_v = '10.4';
			} elsif($build >= 312) {
				$browser_v = '1.3';
				$platform_v = '10.3';
			} elsif($build >= 125) {
				$browser_v = '1.2';
				$platform_v = '10.3';
			} elsif($build >= 100) {
				$browser_v = '1.1';
				$platform_v = '10.3';
			} elsif($build >= 85) {
				$browser_v = '1.0';
				$platform_v = '10.2';
			}
			if($user_agent =~ /Windows\s+([^\;]+)/i) {
				$platform = "Windows";
				$platform_v = $1;
				if($platform_v eq 'NT 6.0') {
					$platform_v = 'Vista';
				} elsif($platform_v eq 'NT 5.0') {
					$platform_v = '2000';
				} elsif($platform_v eq 'NT 5.1') {
					$platform_v = 'XP';
				}
			}
		}
	} elsif($user_agent =~ /(DIPOCKET|WILLCOM)\;\w+\/([\w\d\-]+)/i) {
		$platform_v = $2;
		$platform = 'WILLCOM';
		if($user_agent =~ /(Opera|NetFront|CNF)[\/\s]([\d\.]+)/) {
			$browser = $1;
			$browser_v = $2;
		}
	} elsif($user_agent =~ /(KYOCERA|SHARP)\/([AW][\w\d\-]+)/) {
		$platform_v = $2;
		$platform = 'WILLCOM';
		if($user_agent =~ /(Opera|NetFront|CNF)[\/\s]([\d\.]+)/) {
			$browser = $1;
			$browser_v = $2;
		}
	} elsif($user_agent =~ /KYOCERAAH\-([\w\d\-]+)/) {
		$platform_v = 'AH-' . $1;
		$platform = 'WILLCOM';
		if($user_agent =~ /(Opera|NetFront|CNF)[\/\s]([\d\.]+)/) {
			$browser = $1;
			$browser_v = $2;
		}
	} elsif($user_agent =~ /SHARP ([AW][\w\d\-]+)\//) {
		$platform_v = $1;
		$platform = 'WILLCOM';
		if($user_agent =~ /(Opera|NetFront|CNF)[\/\s]([\d\.]+)/) {
			$browser = $1;
			$browser_v = $2;
		}
	} elsif($user_agent =~ /^(J\-PHONE|Vodafone|Softbank)/i) {
		$browser = $1;
		$platform = 'SoftBank';
		my @parts = split(/\//, $user_agent);
		$browser_v = $parts[1];
		$platform_v = $parts[2];
		if($user_agent =~ /Browser\/([^\/]+)\/([\d\.]+)/) {
			$browser = $1;
			$browser_v = $2;
		}
	} elsif($user_agent =~ /UP\.\s*Browser/i) {
		$user_agent =~ s/UP\.\s*Browser/UP\.Browser/;
		$browser = 'UP.Browser';
		@agentPart = split(/ /, $user_agent);
		if($agentPart[0] =~ /KDDI/i) {
			my @tmp = split(/\-/, $agentPart[0]);
			$platform_v = $tmp[1];
			my @tmp2 = split(/\//, $agentPart[1]);
			$browser_v = $tmp2[1];
		} else {
			@agentPart2 = split(/\//, $agentPart[0]);
			($browser_v, $platform_v) = split(/\-/, $agentPart2[1]);
		}
		my %devid_list = (
			#http://www.au.kddi.com/ezfactory/tec/spec/4_4.html
			#■CDMA 1X WIN (W03H、W02H、W01を除く)
			'SA3E' => 'au,W64SA',
			'SH35' => 'au,W62SH',
			'TS3K' => 'au,Sportio',
			'KC3G' => 'au,W62K',
			'SN3E' => 'au,W62S',
			'ST34' => 'au,W62SA',
			'CA3A' => 'au,W61CA',
			'SH34' => 'au,W61SH',
			'CA3B' => 'au,W62CA',
			'SN3F' => 'au,re',
			'TS3J' => 'au,W62T',
			'SN3D' => 'au,W61S',
			'TS3I' => 'au,W61T',
			'PT33' => 'au,W61PT',
			'KC3D' => 'au,W61K',
			'SN3C' => 'au,W54S',
			'HI3D' => 'au,W62H',
			'KC3H' => 'au,W63K',
			'SA3D' => 'au,W63SA',
			'SA3C' => 'au,W61SA',
			'HI3C' => 'au,W61H',
			'MA33' => 'au,W61P',
			'SA3B' => 'au,W54SA',
			'TS3H' => 'au,W56T',
			'TS3G' => 'au,W55T',
			'HI3B' => 'au,W53H',
			'KC3B' => 'au,W53K',
			'ST33' => 'au,INFOBAR 2',
			'KC3E' => 'au,W44K IIカメラなしモデル',
			'SN3B' => 'au,W53S',
			'CA39' => 'au,W53CA',
			'ST32' => 'au,W53SA',
			'CA38' => 'au,W52CA',
			'TS3D' => 'au,W53T',
			'KC3A' => 'au,MEDIA SKIN',
			'TS3C' => 'au,W52T',
			'HI39' => 'au,W51H',
			'KC39' => 'au,W51K',
			'SN38' => 'au,W44S',
			'TS38' => 'au,W45T',
			'SN37' => 'au,W43S',
			'SH31' => 'au,W41SH',
			'TS37' => 'au,W44T/T II/T III',
			'SN36' => 'au,W42S',
			'SA36' => 'au,W41SA',
			'CA33' => 'au,W41CA',
			'SA35' => 'au,W33SA/SA II',
			'KC34' => 'au,W32K',
			'CA32' => 'au,W31CA',
			'KC33' => 'au,W31K/K II',
			'HI33' => 'au,W22H',
			'SA31' => 'au,W21SA',
			'HI32' => 'au,W21H',
			'CA36' => 'au,E03CA',
			'TS3E' => 'au,W54T',
			'MA32' => 'au,W52P',
			'SA3A' => 'au,W52SA',
			'SH32' => 'au,W51SH',
			'TS3B' => 'au,W51T',
			'CA37' => 'au,W51CA',
			'TS39' => 'au,DRAPE',
			'KC38' => 'au,W44K/K II',
			'CA35' => 'au,W43CA',
			'KC37' => 'au,W43K',
			'CA34' => 'au,W42CA',
			'TS35' => 'au,neon',
			'KC36' => 'au,W42K',
			'TS34' => 'au,W41T',
			'SN34' => 'au,W41S',
			'TS33' => 'au,W32T',
			'HI35' => 'au,W32H',
			'TS32' => 'au,W31T',
			'SA33' => 'au,W31SA/SA II',
			'CA31' => 'au,W21CA/CA II',
			'SN31' => 'au,W21S',
			'KC31' => 'au,W11K',
			'SA37' => 'au,E02SA',
			'SH33' => 'au,W52SH',
			'SN3A' => 'au,W52S',
			'HI3A' => 'au,W52H',
			'SN39' => 'au,W51S',
			'SA39' => 'au,W51SA',
			'MA31' => 'au,W51P',
			'TS3A' => 'au,W47T',
			'SA38' => 'au,W43SA',
			'HI38' => 'au,W43H/H II',
			'ST31' => 'au,W42SA',
			'HI37' => 'au,W42H',
			'TS36' => 'au,W43T',
			'KC35' => 'au,W41K',
			'HI36' => 'au,W41H',
			'HI34' => 'au,PENCK',
			'SA34' => 'au,W32SA',
			'SN33' => 'au,W32S',
			'SN35' => 'au,W32S',
			'SN32' => 'au,W31S',
			'SA32' => 'au,W22SA',
			'TS31' => 'au,W21T',
			'KC32' => 'au,W21K',
			'HI31' => 'au,W11H',
			#■CDMA 1X
			'ST2C' => 'au,Sweets cute',
			'ST26' => 'au,Sweets',
			'SA2A' => 'au,A5527SA',
			'TS2D' => 'au,A5523T',
			'ST2A' => 'au,A5520SA/SA II',
			'TS2B' => 'au,A5516T',
			'CA27' => 'au,A5512CA',
			'ST24' => 'au,A5507SA',
			'TS27' => 'au,A5504T',
			'TS26' => 'au,A5501T',
			'ST23' => 'au,A5405SA',
			'SN24' => 'au,A5402S',
			'ST21' => 'au,A5306ST',
			'HI24' => 'au,A5303H II',
			'TS23' => 'au,A5301T',
			'PT21' => 'au,A1405PT',
			'SN27' => 'au,A1402S II',
			'KC23' => 'au,A1401K',
			'TS25' => 'au,A1304T',
			'SA24' => 'au,A1302SA',
			'KC15' => 'au,A1013K',
			'ST29' => 'au,Sweets pure',
			'ST25' => 'au,talby',
			'KC29' => 'au,A5526K',
			'SA29' => 'au,A5522SA',
			'ST28' => 'au,A5518SA',
			'KC27' => 'au,A5515K',
			'TS2A' => 'au,A5511T',
			'TS28' => 'au,A5506T',
			'SA26' => 'au,A5503SA',
			'CA26' => 'au,A5407CA',
			'SN25' => 'au,A5404S',
			'CA23' => 'au,A5401CA II',
			'KC22' => 'au,A5305K',
			'HI23' => 'au,A5303H',
			'SA22' => 'au,A3015SA',
			'SN29' => 'au,A1404S/S II',
			'SN28' => 'au,A1402S II カメラ無し',
			'SA28' => 'au,A1305SA',
			'SN23' => 'au,A1301S',
			'KC26' => 'au,B01K',
			'CA28' => 'au,G\'zOne TYPE-R',
			'ST22' => 'au,INFOBAR',
			'ST2D' => 'au,A5525SA',
			'KC28' => 'au,A5521K',
			'TS2C' => 'au,A5517T',
			'ST27' => 'au,A5514SA',
			'TS29' => 'au,A5509T',
			'SA27' => 'au,A5505SA',
			'KC24' => 'au,A5502K',
			'KC25' => 'au,A5502K',
			'CA25' => 'au,A5406CA',
			'CA24' => 'au,A5403CA',
			'CA23' => 'au,A5401CA',
			'TS24' => 'au,A5304T',
			'CA22' => 'au,A5302CA',
			'PT22' => 'au,A1406PT',
			'KC26' => 'au,A1403K',
			'SN26' => 'au,A1402S',
			'SA25' => 'au,A1303SA',
			'ST14' => 'au,A1014ST',
			#■cdmaOne
			'SN21' => 'au,A3014S',
			'SA21' => 'au,A3011SA',
			'KC14' => 'au,A1012K',
			'MA21' => 'au,C3003P',
			'SN17' => 'au,C1002S',
			'HI14' => 'au,C451H',
			'KC13' => 'au,C414K',
			'ST12' => 'au,C411ST',
			'MA13' => 'au,C408P',
			'SY13' => 'au,C405SA',
			'DN11' => 'au,C402DE',
			'TS22' => 'au,A3013T',
			'SN22' => 'au,A1101S',
			'ST13' => 'au,A1011ST',
			'KC21' => 'au,C3002K',
			'SY15' => 'au,C1001SA',
			'TS14' => 'au,C415T',
			'SN15' => 'au,C413S',
			'SN16' => 'au,C413S',
			'TS13' => 'au,C410T',
			'HI13' => 'au,C407H',
			'SN12' => 'au,C404S',
			'SN14' => 'au,C404S',
			'SY12' => 'au,C401SA',
			'CA21' => 'au,A3012CA',
			'KC14' => 'au,A1012K II',
			'TS21' => 'au,C5001T',
			'HI21' => 'au,C3001H',
			'CA14' => 'au,C452CA',
			'KC13' => 'au,C414K II',
			'SY14' => 'au,C412SA',
			'CA13' => 'au,C409CA',
			'SN13' => 'au,C406S',
			'ST11' => 'au,C403ST',
			#■TU-KA
			'KCTE' => 'TU-KA,TK51',
			'SYT5' => 'TU-KA,TS41',
			'TST7' => 'TU-KA,TT31',
			'KCTB' => 'TU-KA,TK23',
			'KCT9' => 'TU-KA,TK21',
			'KCT8' => 'TU-KA,TK12',
			'MIT1' => 'TU-KA,TD11',
			'TST3' => 'TU-KA,TT03',
			'SYT2' => 'TU-KA,TS02',
			'KCT3' => 'TU-KA,TK0K',
			'TST1' => 'TU-KA,TT01',
			'TST9' => 'TU-KA,TT51',
			'KCTD' => 'TU-KA,TK40',
			'KCTC' => 'TU-KA,TK31',
			'KCTA' => 'TU-KA,TK22',
			'TST5' => 'TU-KA,TT21',
			'SYT3' => 'TU-KA,TS11',
			'MAT3' => 'TU-KA,TP11',
			'KCT5' => 'TU-KA,TK04',
			'MAT1' => 'TU-KA,TP01',
			'MAT2' => 'TU-KA,TP01',
			'KCT2' => 'TU-KA,TK02',
			'SYT1' => 'TU-KA,TS01',
			'KCU1' => 'TU-KA,TK41',
			'TST8' => 'TU-KA,TT32',
			'SYT4' => 'TU-KA,TS31',
			'TST6' => 'TU-KA,TT22',
			'TST4' => 'TU-KA,TT11',
			'KCT7' => 'TU-KA,TK11',
			'KCT6' => 'TU-KA,TK05',
			'KCT4' => 'TU-KA,TK03',
			'TST2' => 'TU-KA,TT02',
			'KCT1' => 'TU-KA,TK01',
			#その他
			'NT95'=>'UP.SDK',
			'UPG'=>'UP.SDK',
			'P-PAT'=>'DoCoMo,P-PAT',
			'D2'=>'DoCoMo,D2'
		);
		if($devid_list{$platform_v} eq '') {
			$platform = '';
			$platform_v = '';
		} else {
			($platform, $platform_v) = split(/,/, $devid_list{$platform_v});
		}
	} elsif($user_agent =~ /^ASTEL\/(.+)\/(.+)\/(.+)\//) {
		$platform = 'ASTEL';
		$browser = 'ASTEL';
		$browser_v = '';
		$platform_v = substr($2, 0, 5);
	} elsif($user_agent =~ /^Mozilla\/.+ AVE-Front\/(.+) \(.+\;Product=(.+)\;.+\)/) {
		$browser = 'NetFront';
		$browser_v = $1;
		$platform = $2;
		$platform_v = '';
	} elsif($user_agent =~ /^Mozilla\/.+ Foliage-iBrowser\/([0-9\.]+) \(WinCE\)/) {
		$platform = 'Windows';
		$platform_v = 'CE';
		$browser = 'Foliage-iBrowser';
		$browser_v = $1;		
	} elsif($user_agent =~ /^Mozilla\/.+\(compatible\; MSPIE ([0-9\.]+)\; Windows CE/) {
		$platform = 'Windows';
		$platform_v = 'CE';
		$browser = 'PocketIE';
		$browser_v = $1;
	} elsif($user_agent =~ /Opera/) {
		$browser = "Opera";
		if($user_agent =~ /^Opera\/([0-9\.]+)/) {
			$browser_v = $1;
		} elsif($user_agent =~ /Opera\s+([0-9\.]+)/) {
			$browser_v = $1;
		} else {
			$browser_v = '';
		}
		if($user_agent =~ /Windows\s+([^\;]+)(\;|\))/i) {
			$platform = "Windows";
			$platform_v = $1;
			if($platform_v eq 'NT 6.0') {
				$platform_v = 'Vista';
			} elsif($platform_v eq 'NT 5.0') {
				$platform_v = '2000';
			} elsif($platform_v eq 'NT 5.1') {
				$platform_v = 'XP';
			} elsif($platform_v eq 'NT 5.2') {
				$platform_v = '2003';
			} elsif($platform_v eq 'ME') {
				$platform_v = 'Me';
			}
		} elsif($user_agent =~ /Macintosh/) {
			if($user_agent =~ /Mac OS X/) {
				$platform = "MacOS";
				$platform_v = '';
			}
		} elsif($user_agent =~ /Mac_PowerPC/i) {
			$platform = 'MacOS';
			$platform_v = '';
		} elsif($user_agent =~ /Linux\s+([a-zA-Z0-9\.\-]+)/) {
			$platform = "Linux";
			$platform_v = $1;
		} elsif($user_agent =~ /BeOS ([A-Z0-9\.\-]+)(\;|\))/) {
			$platform = 'BeOS';
			$platform_v = $1;
		} else {
			$platform = '';
			$platform_v = '';
		}
	} elsif($user_agent =~ /^Mozilla\/[^\(]+\(compatible\; MSIE .+\)/) {
		if($user_agent =~ /NetCaptor ([0-9\.]+)/) {
			$browser = 'NetCaptor';
			$browser_v = $1;
		} elsif($user_agent =~ /Sleipnir\/([\d\.]+)/) {
			$browser = 'Sleipnir';
			$browser_v = $1;
		} elsif($user_agent =~ /Lunascape\s+([\d\.]+)/) {
			$browser = 'Lunascape';
			$browser_v = $1;
		} else {
			$browser = 'Internet Explorer';
			$user_agent2 = $user_agent;
			$user_agent2 =~ s/ //g;
			@buff = split(/\;/, $user_agent2);
			@version_buff = grep(/MSIE/i, @buff);
			$browser_v = $version_buff[0];
			$browser_v =~ s/MSIE//g;
			if($browser_v =~ /^([0-9]+)\.([0-9]+)/) {
        			$browser_v = "$1\.$2";
			}
		}

		if($user_agent =~ /Windows 3\.1/i) {
			$platform = 'Windows';
			$platform_v = '3.1';
		} elsif($user_agent =~ /Win32/i) {
			$platform = 'Windows';
			$platform_v = '32';
		} elsif($user_agent =~ /Windows 95/i) {
			$platform = 'Windows';
			$platform_v = '95';
		} elsif($user_agent =~ /Windows 98/i) {
			$platform = 'Windows';
			if($user_agent =~ /Win 9x 4\.90/) {
				$platform_v = 'Me';
			} else {
				$platform_v = '98';
			}
		} elsif($user_agent =~ /Windows NT 6\.0/i) {
			$platform = 'Windows';
			$platform_v = 'Vista';
		} elsif($user_agent =~ /Windows NT 5\.0/i) {
			$platform = 'Windows';
			$platform_v = '2000';
		} elsif($user_agent =~ /Windows NT 5\.1/i) {
			$platform = 'Windows';
			$platform_v = 'XP';
		} elsif($user_agent =~ /Windows NT 5\.2/i) {
			$platform = 'Windows';
			$platform_v = '2003';
		} elsif($user_agent =~ /Windows NT/i 
				&& $user_agent !~ /Windows NT 5\.0/i) {
			$platform = 'Windows';
			$platform_v = 'NT';
		} elsif($user_agent =~ /Windows 2000/) {
			$platform = 'Windows';
			$platform_v = '2000';
		} elsif($user_agent =~ /Windows ME/i) {
			$platform = 'Windows';
			$platform_v = 'Me';
		} elsif($user_agent =~ /Windows XP/i) {
			$platform = 'Windows';
			$platform_v = 'XP';
		} elsif($user_agent =~ /Windows CE/i) {
			$platform = 'Windows';
			$platform_v = 'CE';
		} elsif($user_agent =~ /Mac/i) {
			$platform = 'MacOS';
			if($browser_v >= 5.22) {
				$platform_v = '10.x';
			} else {
				$platform_v = '9以下';
			}
		} elsif($user_agent =~ /WebTV/i) {
			$platform = 'WebTV';
			@buff2 = split(/ /, $user_agent);
			@buff3 = split(/\//, $buff2[1]);
			$platform_v = $buff3[1];
		} else {
			$platform = '';
			$platform_v = '';
		}
	} elsif($user_agent =~ /^Mozilla\/([0-9\.]+)/) {
		$browser = 'Netscape';
		$browser_v = $1;
		if($user_agent =~ /Gecko/) {
			if($user_agent =~ /Netscape[0-9]*\/([0-9a-zA-Z\.]+)/) {
				$browser_v = $1;
			} elsif($user_agent =~ /Firefox\/[\d\.]+\s+Navigator\/([\d\.]+)/) {
				$browser_v = $1;
			} elsif($user_agent =~ /(Phoenix|Chimera|Firefox|Camino|Konqueror)\/([0-9a-zA-Z\.]+)/) {
				$browser = $1;
				$browser_v = $2;
			} else {
				$browser = 'Mozilla';
				if($user_agent =~ /rv:([0-9\.]+)/) {
					$browser_v = $1;
				} else {
					$browser_v = '';
				}
			}
		}
		if($user_agent =~ /Win95/) {
			$platform = 'Windows';
			$platform_v = '95';
		} elsif($user_agent =~ /Windows 95/) {
			$platform = 'Windows';
			$platform_v = '95';
		} elsif($user_agent =~ /Win 9x 4\.90/i) {
			$platform = 'Windows';
			$platform_v = 'Me';
		} elsif($user_agent =~ /Windows Me/i) {
			$platform = 'Windows';
			$platform_v = 'Me';
		} elsif($user_agent =~ /Win98/i) {
			$platform = 'Windows';
			$platform_v = '98';
		} elsif($user_agent =~ /WinNT/i) {
			$platform = 'Windows';
			$platform_v = 'NT';
		} elsif($user_agent =~ /Windows NT 6\.0/i) {
			$platform = 'Windows';
			$platform_v = 'Vista';
		} elsif($user_agent =~ /Windows NT 5\.0/i) {
			$platform = 'Windows';
			$platform_v = '2000';
		} elsif($user_agent =~ /Windows NT 5\.1/i) {
			$platform = 'Windows';
			$platform_v = 'XP';
		} elsif($user_agent =~ /Windows NT 5\.2/i) {
			$platform = 'Windows';
			$platform_v = '2003';
		} elsif($user_agent =~ /Windows 2000/i) {
			$platform = 'Windows';
			$platform_v = '2000';
		} elsif($user_agent =~ /Windows XP/i) {
			$platform = 'Windows';
			$platform_v = 'XP';
		} elsif($user_agent =~ /Macintosh/i) {
			$platform = 'MacOS';
			if($user_agent =~ /Mac OS X/i) {
				if($user_agent =~ /Mac OS X ([\d\.]+)\;/) {
					$platform_v = $1;
				} else {
					$platform_v = '10.x';
				}
			} else {
				$platform_v = '';
			}
		} elsif($user_agent =~ /SunOS/i) {
			$platform = 'Solaris';
			if($user_agent =~ /SunOS\s+([0-9\-\.]+)/i) {
				$platform_v = $1;
			} else {
				$platform_v = '';
			}
		} elsif($user_agent =~ /Linux/i) {
			$platform = 'Linux';
			if($user_agent =~ /Fedora/) {
				$platform_v = 'Fedora Core';
				if($user_agent =~ /\.fc([\d+\.]+)\s+/) {
					$platform_v .= " $1";
				}
			} elsif($user_agent =~ /SUSE/) {
				$platform_v = 'SUSE';
				if($user_agent =~ /SUSE\/([\d\.\-]+)/) {
					$platform_v .= " $1";
				}
			} elsif($user_agent =~ /Vine/) {
				$platform_v = 'Vine';
				if($user_agent =~ /Vine\/([\d\.\-\w]+)/) {
					$platform_v .= " $1";
				}
			} elsif($user_agent =~ /VineLinux/) {
				$platform_v = 'Vine';
				if($user_agent =~ /VineLinux\/([\d\.\-\w]+)/) {
					$platform_v .= " $1";
				}
			} elsif($user_agent =~ /Mandriva/) {
				$platform_v = 'Mandriva';
				if($user_agent =~ /Mandriva\/([\d\.\-\w]+)/) {
					$platform_v .= " $1";
				}
			} elsif($user_agent =~ /Red Hat/) {
				$platform_v = 'Red Hat';
				if($user_agent =~ /Red Hat\/([\d\.\-\w]+)/) {
					$platform_v .= " $1";
				}
			} elsif($user_agent =~ /Debian/) {
				$platform_v = 'Debian';
				if($user_agent =~ /Debian\-([\d\.\-\w\+]+)/) {
					$platform_v .= " $1";
				}
			} elsif($user_agent =~ /Ubuntu/) {
				$platform_v = "Ubuntu";
				if($user_agent =~ /Ubuntu(\/|\-)([\d\.\-\w\+]+)/) {
					$platform_v .= " $2";
				}
			} elsif($user_agent =~ /CentOS/) {
				$platform_v = "CentOS";
				if($user_agent =~ /CentOS\/([\d\.\-\w\+]+)/) {
					$platform_v .= " $1";
				}
			} elsif($user_agent =~ /Linux\s+([0-9\-\.]+)/) {
				$platform_v = $1;
			}
		} elsif($user_agent =~ /FreeBSD/i) {
			$platform = 'FreeBSD';
			if($user_agent =~ /FreeBSD\s+([a-zA-Z0-9\.\-\_]+)/i) {
				$platform_v = $1;
			} else {
				$platform_v = '';
			}
		} elsif($user_agent =~ /NetBSD/i) {
			$platform = 'NetBSD';
			$platform_v = '';
		} elsif($user_agent =~ /AIX/i) {
			$platform = 'AIX';
			if($user_agent =~ /AIX\s+([0-9\.]+)/) {
				$platform_v = $1;
			} else {
				$platform_v = '';
			}
		} elsif($user_agent =~ /IRIX/i) {
			$platform = 'IRIX';
			if($user_agent =~ /IRIX\s+([0-9\.]+)/i) {
				$platform_v = $1;
			} else {
				$platform_v = '';
			}
		} elsif($user_agent =~ /HP-UX/i) {
			$platform = 'HP-UX';
			if($user_agent =~ /HP-UX\s+([a-zA-Z0-9\.]+)/i) {
				$platform_v = $1;
			} else {
				$platform_v = '';
			}
		} elsif($user_agent =~ /OSF1/i) {
			$platform = 'OSF1';
			if($user_agent =~ /OSF1\s+([a-zA-Z0-9\.]+)/i) {
				$platform_v = $1;
			} else {
				$platform_v = '';
			}
		} elsif($user_agent =~ /BeOS/i) {
			$platform = 'BeOS';
			$platform_v = '';
		} else {
			$platform = '';
			$platform_v = '';
		}
	} else {
		$platform = '';
		$platform_v = '';
		$browser = '';
		$browser_v = '';
	}
	return ($platform, $platform_v, $browser, $browser_v);
}
