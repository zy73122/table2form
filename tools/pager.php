<?php
/**
 * 分页
 */
class pager
{
	/**
	 * 默认显示页面
	 *
	 * @var int
	 */
	public static $default_pages = 15;


	/**
	 * 获取页码列表
	 *
	 * @param int $total 记录总数
	 * @param int $start 从第几条记录开始
	 * @param int $page_rows 每页显示记录数
	 * @return array
	 */
	public static function get_page_number_list($total, $start, $page_rows)
	{
		if ($total < 1 || $start < 0 || $page_rows < 1) return false;

		// 非首页和尾页时，当前页两边最少显示的页数
		$least = (self::$default_pages - 5) / 2;

		if ($start < $page_rows) $start = 0;
		if ($start >= $total) $start = $total - 1;
		$page_num = ceil($total/$page_rows);

		$current_page = ceil($start/$page_rows);
		if ($start%$page_rows == 0) $current_page++;
		if ($current_page < 1) $current_page = 1;
		$output = array();
		// 如果小于显示默认显示页数
		if ($page_num <= self::$default_pages) {
			// 上一页
			$prev = ($current_page-2) * $page_rows;
			if ($prev < 0) $prev = 0;
			if ($start > 0) $output['prev'] = $prev;
			for ($i=0; $i<$page_num; ++$i) {
				$tmp = $i * $page_rows;
				$t = $i + 1;
				if ($t == $current_page) $output[$t] = -1;
				else $output[$t] = $tmp;
			}
			// 下一页
			$next = $current_page * $page_rows;
			if ($next < $total) $output['next'] = $next;
		} else {
			// 如果需要省略某些页
			if ($current_page - $least - 1 > 1 || $current_page + $least + 1 < $page_num) {
				// 上一页
				$prev = ($current_page - 2) * $page_rows;
				if ($prev < 0) $prev = 0;
				if ($start > 0) $output['prev'] = $prev;
				for ($i = 0; $i < $page_num; ++$i) {
					$tmp = $i * $page_rows;
					$t = $i + 1;
					if ($t < $current_page - $least && $page_num - self::$default_pages + 2 > $t && $t != 1) {
						$output['omitf'] = true;
						continue;
					}
					if ($t > $current_page + $least && $t > self::$default_pages - 1 && $t != $page_num) {
						$output['omita'] = true;
						continue;
					}
					if ($t == $current_page) $output[$t] = -1;
					else $output[$t] = $tmp;
				}
				// 下一页
				$next = $current_page * $page_rows;
				if ($next < $total) $output['next'] = $next;
			} else {
				// 上一页
				$prev = ($current_page-2) * $page_rows;
				if ($prev < 0) $prev = 0;
				if ($start > 0) $output['prev'] = $prev;
				for ($i=0; $i<$page_num; ++$i) {
					$tmp = $i * $page_rows;
					$t = $i + 1;
					if ($t == $current_page) $output[$t] = -1;
					else $output[$t] = $tmp;
				}
				// 下一页
				$next = $current_page * $page_rows;
				if ($next < $total) $output['next'] = $next;
			}
		}
		return $output;
	}

	/**
	 *
	 * .tpl文件代码
	 * 建议这样写在模板文件里:
			<!--pager--> 
			<!--页面链接不可用时用class="t1" 活动状态是用class="this"-->
			{{if $pages}}
			{{assign var=page_url value=$pages.page_url}}
			{{assign var=total value=$pages.total}}
			{{assign var=current value=$pages.current}}
			{{assign var=pagenum value=$pages.pagenum}}
			{{assign var=pagesize value=$pages.pagesize}}
			{{assign var=min value=$pages.min}}
			{{assign var=max value=$pages.max}}
			{{assign var=first value=$pages.first}}
			{{assign var=prev value=$pages.prev}}
			{{assign var=next value=$pages.next}}
			{{assign var=last value=$pages.last}}
			<!--当前：{{$current}}/{{$pagenum}}-->
			<a href="{{url avg="$page_url&page=$first"}}" {{if $current==$first}}class="t1"{{/if}}><small>« 首页</small></a>
			<a href="{{url avg="$page_url&page=$prev"}}" {{if $current==$first}}class="t1"{{/if}}><small>‹ 上一页</small></a>
			{{if $min>0}}
			<a href="{{url avg="$page_url&page=$first"}}">{{$first+1}}</a>...
			{{/if}}
			{{section name=i loop=$pages.pageno start=$min max=$max-$min+1}}
			{{assign var=one value=$pages.pageno[i]}}
			<a href="{{url avg="$page_url&page=$one"}}" {{if $current==$one}}class="this"{{/if}}>{{$one+1}}</a>
			{{/section}}
			{{if $max<$pagenum-1}}
			...<a href="{{url avg="$page_url&page=$last"}}">{{$last+1}}</a>
			{{/if}}
			<a href="{{url avg="$page_url&page=$next"}}" {{if $current==$last}}class="t1"{{/if}}><small>下一页 ›</small></a>			
			<a href="{{url avg="$page_url&page=$last"}}" {{if $current==$last}}class="t1"{{/if}}><small>尾页 »</small></a>
			{{/if}} 
			<!--pager end-->

	 * 其他方式:
	 * style1:
												<a href="{{$page_url}}&page={{$pages.prev}}">&lt;上一页</a>																						
													{{assign var=remainder value=$pages.current%5}}
													{{assign var=min value=$pages.current-$remainder}}
													{{assign var=max value=$min+4}}
													{{if $max>$pages.pagenum}}
													{{assign var=max value=$pages.pagenum}}												
													{{/if}}
													
													{{if $min>0}}
														<a href="{{$page_url}}&page={{$pages.first}}">1</a>			
														...							
													{{/if}}
													{{foreach from=$pages.pageno item=pageno name=pager}}
													{{assign var=pageurl value="$page_url&page=$pageno"}}
													{{if $smarty.foreach.pager.index>=$min && $smarty.foreach.pager.index<=$max}}
														{{if $smarty.foreach.pager.index!=$pages.current}}
														<a href="{{url avg=$pageurl}}">{{$smarty.foreach.pager.index+1}}</a>
														{{else}}
														<strong>{{$pages.current+1}}</strong>
														{{/if}}
													{{/if}}
													{{/foreach}}
													{{if $max<$pages.pagenum}}
														...
														<a href="{{$page_url}}&page={{$pages.last}}">{{$pages.last+1}}</a>										
													{{/if}}
												<a href="{{$page_url}}&page={{$pages.next}}">下一页&gt;</a>';
	 * 
	 * style2:
	 											{{if $pages}}
												页次：{{$pages.current+1}}/{{$pages.pagenum}}&nbsp;每页{{$pages.pagesize}}&nbsp;总数{{$pages.total}}&nbsp;&nbsp;&nbsp;&nbsp;<a href="{{$page_url}}&page={{$pages.first}}">首页</a>&nbsp;&nbsp;<a href="{{$page_url}}&page={{$pages.prev}}">上一页</a>&nbsp;&nbsp;<a href="{{$page_url}}&page={{$pages.next}}">下一页</a>&nbsp;&nbsp;<a href="{{$page_url}}&page={{$pages.last}}">尾页</a>
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;转到:
												<SELECT onchange=self.location.href=this.options[this.selectedIndex].value 
				  name=select>
				  								{{foreach from=$pages.pageno item=pageno name=pager}}
												{{assign var=pageurl value="$page_url&page=$pageno"}}
													<OPTION value={{url avg=$pageurl}} {{if $pageno==$pages.current}}selected{{/if}}>第 {{$smarty.foreach.pager.index+1}} 页</OPTION>
				  								{{/foreach}}
												</SELECT>
												{{/if}}
	 */
	public static function get_page_info($page_url, $total, $current_page, $page_size)
	{
		if (strpos($page_url, '?')) {
			$spl = '&';	
		} else {
			$spl = '?';	
		}	
		
		//页面总数
		$page_num = ceil($total/$page_size);
		
		//当前页面
		if ($current_page < 0) $current_page = 0;
		if ($current_page > $page_num) $current_page = $page_num;

		$output = array();		
		$output['page_url'] = $page_url;
		$output['total'] = $total;
		$output['pagenum'] = $page_num;
		$output['pagesize'] = $page_size;
		$output['current'] = $current_page;
		$output['first'] = 0;
		$output['prev'] = ($current_page-1>0) ? ($current_page-1) : 0;
		$output['next'] = ($current_page+1<($page_num-1)) ? $current_page+1 : $page_num-1;
		$output['last'] = $page_num-1;
		
		for ($i=0; $i<=$output['last']; $i++)
		{
			$output['pageno'][] = $i;
		}
		

//		$remainder = $current_page%5;
//		$min = $current_page-$remainder;
//		$max = $min+4;
//		if ($max > $page_num)
//		$max = $page_num;
//		if ($current_page==$max)
//		{
//			$min += 5;
//			$max += 5;
//		}
		
		$min = $current_page-2>0 ? $current_page-2 : 0;
		$max = $current_page+2<$page_num-1 ? $current_page+2 : $page_num-1;
		$output['min'] = $min;
		$output['max'] = $max;
		//分页样式1
		$output['htmstyle1'] = "<a>共".$page_num."页: </a>".'<a href="'.url($page_url.$spl.'page='.$output['prev']).'">&lt;上一页</a>
		';
		if ($min > 0)					
		$output['htmstyle1'] .= '<a href="'.url($page_url.$spl.'page='.$output['first']).'">1</a>...';
		for ($i=$min; $i<=$max; $i++)		
		{
				if ($i != $current_page)
				$output['htmstyle1'] .= '<a href="'.url($page_url.$spl.'page='.$i).'">'.($i+1).'</a>';
				else
				$output['htmstyle1'] .= '<strong>'.($current_page+1).'</strong>';
		}
		if ($max < $page_num-1)					
		$output['htmstyle1'] .= '...<a href="'.url($page_url.$spl.'page='.$output['last']).'">'.($output['last']+1).'</a>';			
		$output['htmstyle1'] .= '<a href="'.url($page_url.$spl.'page='.$output['next']).'">下一页&gt;</a>';
		
		//分页样式2
		$output['htmstyle2'] = "页次：".($current_page+1)."/$page_num &nbsp;每页$page_size &nbsp;总数$total &nbsp;&nbsp;&nbsp;&nbsp;
		<a href=".url($page_url.$spl.'page=0').">首页</a>&nbsp;&nbsp;
		<a href=".url($page_url.$spl.'page='.$output['prev']).">上一页</a>&nbsp;&nbsp;
		<a href=".url($page_url.$spl.'page='.$output['next']).">下一页</a>&nbsp;&nbsp;
		<a href=".url($page_url.$spl.'page='.$output['last']).">尾页</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		转到:<SELECT onchange=self.location.href=this.options[this.selectedIndex].value name=select>";
				  
		for ($i=0; $i<$page_num; $i++)
		{
			$output['htmstyle2'] .= "<OPTION value='".url($page_url.$spl.'page='.$i)."'";
			if ($current_page == $i)
			$output['htmstyle2'] .= "selected";
			$output['htmstyle2'] .= ">第 ".($i+1)." 页</OPTION>";
		}
		$output['htmstyle2'] .= "</SELECT>";
		
/*		//分页样式3
		$output['htmstyle3'] = "<ul>
				<li><a>共".$page_num."页: </a></li>
				<li><a href=".url($page_url.$spl.'page='.$output['prev']).">上一页</a></li>";
				
		for ($i=0; $i<$page_num; $i++)
		{
			$classstr = "";
			if ($current_page == $i) $classstr = "class='thisclass'";
			$output['htmstyle3'] .= "<li ".$classstr."><a href='".url($page_url.$spl.'page='.$i)."'>".($i+1)."</a></li>";
		}
		$output['htmstyle3'] .= "
				<li><a href=".url($page_url.$spl.'page='.$output['next']).">下一页</a></li>
			  </ul>";*/
			  
		//分页样式3
		$output['htmstyle3'] = "<ul>
				<li><a>共".$page_num."页: </a></li>
				<li><a href=".url($page_url.$spl.'page='.$output['prev']).">上一页</a></li>";
		if ($min > 0)					
		$output['htmstyle3'] .= '<li><a href="'.url($page_url.$spl.'page='.$output['first']).'">1</a>...</li>';
		for ($i=$min; $i<=$max; $i++)	
		{
			$classstr = "";
			if ($current_page == $i) $classstr = "class='thisclass'";
			$output['htmstyle3'] .= "<li ".$classstr."><a href='".url($page_url.$spl.'page='.$i)."'>".($i+1)."</a></li>";
		}
		if ($max < $page_num-1)					
		$output['htmstyle3'] .= '<li>...<a href="'.url($page_url.$spl.'page='.$output['last']).'">'.($output['last']+1).'</a></li>';
		$output['htmstyle3'] .= "
				<li><a href=".url($page_url.$spl.'page='.$output['next']).">下一页</a></li>
			  </ul>";
			  
		return $output;
	}
	
}
?>