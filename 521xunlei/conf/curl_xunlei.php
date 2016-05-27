<?php  
/**
* 抓取521xunlei.com 最新分享迅雷账号密码
* 521xunlei.com编码为gbk, 注意抓取后转码
*/
class Thunder
{
	// 主页
	private $origin_url = 'http://521xunlei.com/portal.php';
	private $user_agent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36';

	// 目标url前缀
	private $pos_url = 'http://521xunlei.com/';

	private $result;

	function __construct()
	{
	}

	// 获取指定url的内容
	public function get_url_content($url)
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_ENCODING, "");
		curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
		curl_setopt($ch, CURLOPT_URL, $url);

		$content = curl_exec($ch);
		curl_close($ch);
		return $content;
	}

	// 获取最新分享的url
	public function get_new_html($content)
	{
		preg_match_all('/thread-[0-9]+-1-1.html/', $content, $result);
	
		$arr = array_unique($result[0]);
		$final_html = $arr[0];
		$final_len = strlen($final_html);
		foreach ($arr as $row) 
		{
			$tmp_len = strlen($row);
			if($tmp_len > $final_len)
			{
				$final_html = $row;
				$final_len = $tmp_len;
			}
			else if($tmp_len == $final_len)
			{
				if(!strcasecmp($row, $final_html))
					$final_html = $row;
			}
		}
		return $this->pos_url . $final_html;
	}

	// 获取账号
	public function get_account()
	{
		$content = $this->get_url_content($this->origin_url);
		$content = $this->get_url_content($this->get_new_html($content));

		$pre = '/[0-9]+密码[0-9a-zA-Z.]+/';
		$pre = iconv('utf-8', 'gbk', $pre);
		preg_match_all($pre, $content, $result);
		$arr = array_unique($result[0]);
		
		$pre_account = '/^[0-9]+/';
		$pre_passwd = '/[0-9a-zA-Z.]+$/';

		$pre_account = iconv('utf-8', 'gbk', $pre_account);
		$pre_passwd = iconv('utf-8', 'gbk', $pre_passwd);
		$result = [];

		foreach ($arr as $row) 
		{
			preg_match($pre_account, $row, $account);
			preg_match($pre_passwd, $row, $passwd);
			$account = iconv('gbk', 'utf-8', $account[0]);
			$passwd = iconv('gbk', 'utf-8', $passwd[0]);
			// echo $account . '&nbsp;' . $passwd . '<br/>';
			$result['result'][] = array(
				'account' => $account,
				'passwd' => $passwd
				);
		}
		$this->result = $result;
		return $result;
	}

	public function show_account()
	{
		foreach ($this->result['result'] as $row) 
		{
			echo '账号：' . $row['account'] . '&nbsp;密码：' . $row['passwd'] . '<br/>';
		}
	}
}
?>