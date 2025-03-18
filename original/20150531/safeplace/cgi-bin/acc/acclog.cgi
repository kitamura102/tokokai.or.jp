#!/usr/bin/perl
################################################################################
# 高機能アクセス解析CGI Proffesional版（アクセスログ ロギング用）
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
	eval "use Net::Netmask; 1";
	eval "use DB_File; 1";
}
use Time::Local;
use CGI;
use CGI::Carp qw(fatalsToBrowser);
my $q = new CGI;
$| = 1;

#このCGIの設定
my $CONF_DATA = './data/config.cgi';
my $JPEG_FILE = './acclogo.jpg';
my $GIF_FILE = './acclogo.gif';
my $PNG_FILE = './acclogo.png';
my $RESTRICT_COOKIE_NAME = 'accrestrict';
my $LOG_DIR_PATH = './logs';
#自アクセス制限対象の場合、ロギングせずに終了
my %cookie = &GetCookie;
if($cookie{$RESTRICT_COOKIE_NAME}) {
	&PrintImage;
	exit;
}
#設定を読み取る
my %CONF = &GetConf($CONF_DATA);
my $LOTATION = $CONF{'LOTATION'};
my @REJECT_HOSTS = split(/,/, $CONF{'REJECT_HOSTS'});
my $USECOOKIE = $CONF{'USECOOKIE'};
my $LOTATION_SIZE = $CONF{'LOTATION_SIZE'};
my $EXPIREDAYS = $CONF{'EXPIREDAYS'};
my $TIMEDIFF = $CONF{'TIMEDIFF'};
my $LOTATION_SAVE = $CONF{'LOTATION_SAVE'};
my $LOTATION_SAVE_NUM = $CONF{'LOTATION_SAVE_NUM'};
my $LOCK_FLAG = $CONF{'LOCK_FLAG'};
my $IMAGE_TYPE = $CONF{'IMAGE_TYPE'};
# Remote host
my $remote_host = &GetRemoteHost;
# 除外IPアドレス
{
	my @deny_blk_list = (
		'150.70.0.0/16',	# trendmicro
		'66.180.80.0/20',	# trendmicro
		'66.35.255.0/24'	# trendmicro
	);
	for my $blk (@deny_blk_list) {
		my $hit = 0;
		eval {
			if( Net::Netmask->new($blk)->match($ENV{REMOTE_ADDR}) ) {
				$hit = 1;
			}
		};
		if($hit) {
			&PrintImage;
			exit;
		}
	}
}
# 指定ホストからのアクセスを除外する
if(scalar @REJECT_HOSTS) {
	my $Reject;
	my $RejectFlag = 0;
	for $Reject (@REJECT_HOSTS) {
		if($Reject =~ /^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\/\d{1,2}$/) {
			eval {
				if( Net::Netmask->new($Reject)->match($ENV{REMOTE_ADDR}) ) {
					$RejectFlag = 1;
				}
			};
		} elsif($Reject =~ /[^0-9\.]/) {	# ホスト名指定の場合
			if($remote_host =~ /$Reject$/) {
				$RejectFlag = 1;
			}
		} else {	# IPアドレス指定の場合
			if($ENV{REMOTE_ADDR} =~ /^$Reject/) {
				$RejectFlag = 1;
			}
		}
		if($RejectFlag) {
			&PrintImage;
			exit;
		}
	}
}
#ログファイル名を決定する。
my $LOG = './logs/access_log';
my $Time = time + $TIMEDIFF*60*60;
my $DateStr = &TimeStamp($Time);
if($LOTATION == 2) {	#日ごとのローテーション
	my $DayStr = substr($DateStr, 0, 8);
	$LOG .= "\.$DayStr\.cgi";
} elsif($LOTATION == 3) {	#月ごとのローテーション
	my $MonStr = substr($DateStr, 0, 6);
	$LOG .= "\.$MonStr";
	$LOG .= '00.cgi';
} elsif($LOTATION == 4) {	#週ごとのローテーション
	my @date_array = localtime($Time);
	my $wday = $date_array[6];
	my $epoc_time = $Time;
	$epoc_time -= $wday*60*60*24;
	@date_array = localtime($epoc_time);
	my $day = $date_array[3];
	if($day < 10) {$day = "0$day";}
	my $mon = $date_array[4];
	$mon ++;	if($mon < 10) {$mon = "0$mon";}
	my $year = $date_array[5];
	$year += 1900;
	$LOG .= "\.$year$mon$day\.cgi";
} else {
	$LOG .= "\.cgi";
}
# Access Log Lotation
if($LOTATION) {
	&LogLotation;
}
# User Tracking
my %CookieList = &GetCookie;
my $TrackingData = $CookieList{'futomiacc'};
unless($TrackingData) {
	my $now = time;
	if($ENV{HTTP_X_DCMGUID} =~ /^[a-zA-Z0-9]+$/) {
		my $guid = $ENV{HTTP_X_DCMGUID};
		my %dcmguids;
		eval {
			tie %dcmguids, "DB_File", "./logs/dcmguid.db";
			if( my $epoch = $dcmguids{$guid} ) {
				if( $epoch < $now - (86400 * $EXPIREDAYS) ) {
					$dcmguids{$guid} = $now;
					$TrackingData = 'DCMGUID.' . $ENV{HTTP_X_DCMGUID} . '.' . $now;
				} else {
					$TrackingData = 'DCMGUID.' . $guid . '.' . $epoch;
				}
			} else {
				$dcmguids{$guid} = $now;
				$TrackingData = 'DCMGUID.' . $ENV{HTTP_X_DCMGUID} . '.' . $now;
			}
			untie %dcmguids;
		};
		if($@) {
			$TrackingData = 'DCMGUID.' . $guid . '.' . $now;
		}
	} else {
		$TrackingData = $ENV{REMOTE_ADDR} . '.' . $now;
	}
}
# Remote user
my $remote_user = &GetRemoteUser;
# Requested URI
my $request = &GetRequest;
# HTTP_REFERER
my $referrer = &GetReferrer;
# Make Log String
my $LogString = &GetLogString;
# Loging
&Loging($LogString);
# Print Image to the Client
&PrintImage;
exit;

######################################################################
#  Subroutine
######################################################################

# Print Image to the Client
sub PrintImage {
	my($mime_type, $image_file);
	if($IMAGE_TYPE eq '2') {
		$mime_type = 'image/jpeg';
		$image_file = $JPEG_FILE;
	} elsif($IMAGE_TYPE eq '3') {
		$mime_type = 'image/png';
		$image_file = $PNG_FILE;
	} elsif($IMAGE_TYPE eq '1') {
		$mime_type = 'image/gif';
		$image_file = $GIF_FILE;
	} else {
		my $ua = $ENV{'HTTP_USER_AGENT'};
		if($ua =~ /^(J\-PHONE|Vodafone|Softbank)/i) {
			$mime_type = 'image/png';
			$image_file = $PNG_FILE;
		} elsif($ua =~ /UP\.Browser/) {
			$mime_type = 'image/jpeg';
			$image_file = $JPEG_FILE;
		} else {
			$mime_type = 'image/gif';
			$image_file = $GIF_FILE;
		}
	}
	open(IMAGE, "<$image_file");
	my $logo_size = -s "$image_file";
	$logo_size = -s "$image_file";
	my $data;
	read IMAGE, $data, $logo_size;
	close IMAGE;
	print "Pragma: no-cache\n";
	print "Cache-Control: no-cache\n";
	print "P3P: CP=\"NOI ADMa\"\n";
	if($USECOOKIE) {
		my $SetCookieString = &SetCookie('futomiacc', $TrackingData, $EXPIREDAYS);
		print "$SetCookieString\n";
	}
	print "Content-Type: $mime_type\n\n";
	print $data;
}

sub SetCookie {
	my($CookieName, $CookieValue, $ExpireDays, $Domain, $Path) = @_;
	# URLエンコード
	$CookieValue =~ s/([^\w\=\& ])/'%' . unpack("H2", $1)/eg;
	$CookieValue =~ tr/ /+/;
	my $CookieHeaderString = "Set-Cookie: $CookieName=$CookieValue\;";
	if($ExpireDays) {
		my @MonthString = ('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
					'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
		my @WeekString = ('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
		my $time = time + $ExpireDays*24*60*60;
		my($sec, $min, $hour, $monthday, $month, $year, $weekday) = gmtime($time);
		$year += 1900;
		$month = $MonthString[$month];
		if($monthday < 10) {$monthday = '0'.$monthday;}
		if($sec < 10) {$sec = '0'.$sec;}
		if($min < 10) {$min = '0'.$min;}
		if($hour < 10) {$hour = '0'.$hour;}
		my $GmtString = "$WeekString[$weekday], $monthday-$month-$year $hour:$min:$sec GMT";
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

sub Loging {
	my($String) = @_;
	open(LOGFILE, ">>$LOG") || &ErrorPrint("ログファイルをオープンできませんでした。ディレクトリ「logs」のパーミッションを確認して下さい。パーミッションを変更したら、ディレクトリ「logs」内にあるファイルをすべて削除してから、再度ブラウザーで acclog.cgi にアクセスしてみて下さい。: $!");
	if($LOCK_FLAG) {
		my $lock_result = &Lock(*LOGFILE);
		if($lock_result) {
			&ErrorPrint("ログファイルのロック処理に失敗しました。: $lock_result");
		}
	}
	print LOGFILE "$String\n";
	close(LOGFILE);
}

sub GetLogString {
	my $logfile = "$DateStr $remote_host $TrackingData $remote_user $request $referrer \"$ENV{'HTTP_USER_AGENT'}\"";
	if($ENV{'HTTP_ACCEPT_LANGUAGE'} eq '') {
		if($ENV{HTTP_USER_AGENT} =~ /DoCoMo/i) {
			$logfile .= " \"ja-jp\"";
		} else {
			$logfile .= " \"-\"";
		}
	} else {
		$logfile .= " \"$ENV{'HTTP_ACCEPT_LANGUAGE'}\"";
	}
	my $ScreenWidth = $q->param('width');
	my $ScreenHeight = $q->param('height');
	my $ColorDepth = $q->param('color');
	if($ScreenWidth && $ScreenHeight && $ColorDepth) {
		$logfile .= " \"$ScreenWidth $ScreenHeight $ColorDepth\"";
	} elsif($ENV{'HTTP_X_JPHONE_DISPLAY'} && $ENV{'HTTP_X_JPHONE_COLOR'}) {
		my($width, $height) = split(/\*/, $ENV{'HTTP_X_JPHONE_DISPLAY'});
		my $color = $ENV{'HTTP_X_JPHONE_COLOR'};
		$color =~ s/^[^0-9]+//;
		my $depth = log($color) / log(2);
		$logfile .= " \"$width $height $depth\"";
	} elsif($ENV{'HTTP_X_UP_DEVCAP_SCREENDEPTH'} && $ENV{'HTTP_X_UP_DEVCAP_SCREENPIXELS'}) {
		my($width, $height) = split(/,/, $ENV{'HTTP_X_UP_DEVCAP_SCREENPIXELS'});
		my($depth) = split(/,/, $ENV{'HTTP_X_UP_DEVCAP_SCREENDEPTH'});
		$logfile .= " \"$width $height $depth\"";
	} else {
		$logfile .= ' "-"';
	}
	$logfile =~ s/\x0D//g;
	$logfile =~ s/\x0A//g;
	return $logfile;
}

sub GetReferrer {
	my @query_parts = split(/&/, $ENV{'QUERY_STRING'});
	my $referrer;
	my $part;
	my $flag = 0;
	for $part (@query_parts) {
		if($part =~ /^(width|height|color)=/i) {
			$flag = 0;
		}
		if($part =~ /^referrer=/i) {
			$flag = 1;
		}
		if($flag) {
			$part =~ s/^referrer=//;
			$referrer .= "$part&";
		}
	}
	$referrer =~ s/&$//;
	if($referrer eq '') {
		$referrer = '-';
	}
	$referrer =~ s/\%7e/\~/ig;
	return $referrer;
}

sub URL_encode {
	my($str) = @_;
	$str =~ s/([^\w\=\&\# ])/'%' . unpack("H2", $1)/eg;
	$str =~ tr/ /+/;
	$str =~ s/(\&)(\#)/'%' . unpack("H2", $1) . '%' . unpack("H2", $2)/eg;
	$str =~ s/(\#)/'%' . unpack("H2", $1)/eg;
	return $str;
}

sub TimeStamp {
	my($time) = @_;
	my($sec, $min, $hour, $mday, $mon, $year) = localtime($time);
	$year += 1900;
	$mon += 1;
	$mon = "0$mon" if($mon < 10);
	$mday = "0$mday" if($mday < 10);
	$hour = "0$hour" if($hour < 10);
	$min = "0$min" if($min < 10);
	$sec = "0$sec" if($sec < 10);
	my $stamp = $year.$mon.$mday.$hour.$min.$sec;
	return $stamp;
}

sub Lock {
	local(*FILE) = @_;
	eval{flock(FILE, 2)};
	if($@) {
		return $!;
	} else {
		return '';
	}
}

sub GetRequest {
	my $request = $q->param('url');
	unless($request) {
		if($ENV{'HTTP_REFERER'} eq '') {
			$request = '-';
		} else {
			$request = $ENV{'HTTP_REFERER'};
		}
	}
	$request =~ s/\%7e/\~/ig;
	return $request;
}

sub GetRemoteUser {
	my $remote_user;
	if($ENV{'REMOTE_USER'} eq '') {
		$remote_user = '-';
	} else {
		$remote_user = $ENV{'REMOTE_USER'};
	}
	return $remote_user;
}

sub GetRemoteHost {
	my $remote_host;
	if($ENV{'REMOTE_HOST'} =~ /[^0-9\.]/) {
		$remote_host = $ENV{'REMOTE_HOST'};
	} else {
		my @addr = split(/\./, $ENV{'REMOTE_ADDR'});
		my $packed_addr = pack("C4", $addr[0], $addr[1], $addr[2], $addr[3]);
		my($aliases, $addrtype, $length, @addrs);
		($remote_host, $aliases, $addrtype, $length, @addrs) = gethostbyaddr($packed_addr, 2);
		unless($remote_host) {
			$remote_host = $ENV{'REMOTE_ADDR'};
		}
	}
	return $remote_host;
}

sub LogLotation {
	my $DateStr = &TimeStamp($Time);
	$DateStr = substr($DateStr, 0, 8);
	my $log_size = -s "$LOG";
	if($LOTATION == 1) {
		if($log_size > $LOTATION_SIZE) {
			if($LOTATION_SAVE == 1) {
				my $newlogname = $LOG;
				$newlogname =~ s/\.cgi$/\.$DateStr\.cgi/;
				rename("$LOG", "$newlogname");
			} elsif($LOTATION_SAVE == 2) {
				&PastLogDelete($LOTATION_SAVE_NUM, $LOG);
			} else {
				unlink("$LOG");
			}
		}
	} elsif($LOTATION == 2 || $LOTATION == 3 || $LOTATION == 4) {
		if($LOTATION_SAVE == 0) {
			my @parts = split(/\//, $LOG);
			my $logname = pop @parts;
			my($logname_key) = split(/\./, $logname);
			my $logdir = join('/', @parts);
			if(opendir(DIR, "$logdir")) {
				my @files = readdir(DIR);
				closedir(DIR);
				my $file;
				for $file (@files) {
					if($file eq $logname) {
						next;
					}
					if($file =~ /^$logname_key/) {
						unlink("$logdir/$file");
					}
				}
			}
		} elsif($LOTATION_SAVE == 2) {
			&PastLogDelete($LOTATION_SAVE_NUM, $LOG);
		}
	}
}

sub PastLogDelete {
	my($save_num, $current_log_file_path) = @_;
	my @parts = split(/\//, $current_log_file_path);
	my $current_log_file_name = pop @parts;
	opendir(DIR, "$LOG_DIR_PATH");
	my @files = readdir(DIR);
	closedir(DIR);
	my %ts;
	for my $file (@files) {
		if($file eq $current_log_file_name) { next; }
		if($file =~ /^access_log.(\d{8})\.cgi/) {
			my $t = $1;
			$ts{$file} = $t;
		}
	}
	my @past_logs = sort { $ts{$b} <=> $ts{$a} } keys %ts;
	if(@past_logs > $save_num) {
		splice(@past_logs, 0, $save_num);
		for my $f (@past_logs) {
			unlink "${LOG_DIR_PATH}/${f}";
		}
	}
}

sub GetCookie {
	my(@CookieList) = split(/\; /, $ENV{'HTTP_COOKIE'});
	my %Cookie = ();
	my $key;
	for $key (@CookieList) {
		my($CookieName, $CookieValue) = split(/=/, $key);
		$CookieValue =~ s/\+/ /g;
		$CookieValue =~ s/%([0-9a-fA-F][0-9a-fA-F])/pack("C",hex($1))/eg;
		$Cookie{$CookieName} = $CookieValue;
	}
	return %Cookie;
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
