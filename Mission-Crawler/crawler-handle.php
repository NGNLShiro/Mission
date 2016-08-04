<?php
	
	/*
		Cơ chế hoạt động :
		
			B1 : Lấy keyword từ trang democrawler.php
			B2 : gọi hàm get_all để lấy toàn bộ dữ liệu từ Amazon
			B3 : gọi hàm get_main_HTML để lấy dữ liệu cần lấy
			B4 : gọi hàm get_nav_main_data để lấy dữ liệu về navigation.
				 sau khi lấy được dữ liệu sẽ trả về hàm get_nav_link để sửa lại link.
	*/
	
	
	//Get all from Amazon
	function get_all ($data){
		
		$keyword = str_replace(" ","+",$data);
		//if data has " " -> replace " " to "+"
		$link = "https://www.amazon.com/s/ref=nb_sb_noss_1?url=search-alias%3Daps&field-keywords=".$keyword."&rh=i%3Aaps%2Ck%3A".$keyword;
		//link connect and request Amazon.com
		
		$HTML = file_get_contents($link);
		//HTML contents
		
		return $HTML;
	}
	
	//Get main contents
	function get_main_HTML ($data){
		
		$maindata = substr($data, strpos($data, "<div id=\"resultsCol"), (strpos($data, "<div style=\"clear:both\">") - strpos($data, "<div id=\"resultsCol")) + 66);
		//Main contents which can be seen in the result page
		
		return $maindata;
	}
	
	//Get navigation bar
	function get_nav_main_data ($data, $keyword){
		
		$nav = substr($data, strpos($data, "<div id=\"pagn"), (strpos($data, "<br clear=\"all") - strpos($data, "<div id=\"pagn")) + 54);
		//Navigation bar
		
		return get_nav_link ($nav, $keyword);
	}
	
	//Replace navigation link
	function get_nav_link ($data_HTML, $keyword){
		
		$data = $data_HTML;
		
		//condition : if it can find "page=" in $data -> $stt + 1
		$con = strpos($data_HTML, "page=");
		$stt = 0;
		while($con != null){
			$stt = $stt + 1;
			//cut unnecessary
			$data_HTML = substr($data_HTML, $con + 5);
			//loop condition
			$con = strpos($data_HTML, "page=");
		}
		
		//get default link from amazon.com
		$default_link = substr($data, strpos($data, "s/ref"), (strpos($data, "2</a></span>") - 3) - strpos($data, "s/ref"));
		
		$page_num_position = array();
		$keyword_position = array();
		
		//var1, var2 are temporary variables
		//get position of some link elements 
		$var1 = strpos($default_link, "sr_pg_") + 6;
		$var2 = strpos($default_link, "Ck%3A") + 5;
		array_push($page_num_position, $var1);
		array_push($keyword_position, $var2);
		
		$var1 = strpos($default_link, "page=") + 5;
		$var2 = strpos($default_link, "keywords=") + 9;
		array_push($page_num_position, $var1);
		array_push($keyword_position, $var2);
		
		for($i = 2; $i < $stt + 1; $i++){
		
			$link_element1 = substr($default_link, 0, $page_num_position[0]);
			$link_element2 = substr($default_link, $page_num_position[0] + strlen($i), $keyword_position[0] - ($page_num_position[0] + strlen($i)));
			$link_element3 = substr($default_link, $keyword_position[0] + strlen($keyword), $page_num_position[1] - ($keyword_position[0] + strlen($keyword)));
			$link_element4 = substr($default_link, $page_num_position[1] + strlen($i), $keyword_position[1] - ($page_num_position[1] + strlen($i)));
			$link_element5 = substr($default_link, $keyword_position[1] + strlen($keyword));
			
			//like default link but auto fix all link from 2 to the last number
			$fixed_link = "/".$link_element1.$i.$link_element2.$keyword.$link_element3.$i.$link_element4.$keyword.$link_element5;
			//link replace
			$replace_link = "http://www.amazon.com/".$fixed_link;
			
			//data was replaced
			$data = str_replace($fixed_link, $replace_link, $data);
		}
		
		return $data;
	}
	
	
	
	
	if(!isset($_GET['para'])){
		die("No keyword !!!");
	}
	
	$keyword = $_GET['para'];
	//get all HTML data
	$all = get_all ($keyword);
	//Choose necessary data from all
	$maindata = get_main_HTML ($all);
	//choose navigation data from all
	$navdata = get_nav_main_data ($all, $keyword);
	
	
?>

<html>
	<head>
		<link rel="stylesheet" href="https://images-na.ssl-images-amazon.com/images/G/01/AUIClients/AmazonUI-de8acf3eb250580d6759e6f8f5aa7179debc59d2._V2_.css#AUIClients/AmazonUI.rendering_engine-not-trident.secure.min">
		
		<link rel="stylesheet" href="https://images-na.ssl-images-amazon.com/images/I/71H2xHINdUL._RC|01lxpkIhxkL.css,21tX6kCG1IL.css,21-IrxM6-jL.css,21HnNaydNYL.css_.css#AUIClients/NavDesktopMetaAsset">
		
		<link rel="stylesheet" href="https://images-na.ssl-images-amazon.com/images/G/01/AUIClients/RetailSearchAssets-c61962f9bb2b87af3c2fda248e5a2e50727bf7f3._V2_.css#AUIClients/RetailSearchAssets.us.renderskin-pc.search-results-aui.secure.min">
		
		<style>
			ul.s-result-list{margin:0 0 0 4px;padding:0;word-spacing:-4px;letter-spacing:-4px}ul.s-result-list li.s-result-item{display:inline-block;vertical-align:top;overflow:hidden;word-spacing:normal;letter-spacing:normal;padding:0;margin:0;*display:inline;zoom:1}ul.s-result-list li.s-result-item .s-item-container{padding:7px}ul.s-item-container-height-auto .s-item-container{height:auto!important}ul.s-result-list.s-list-mode li.s-result-item{width:100%!important}.a-ws ul.s-result-list.s-col-ws-1 li.s-result-item,ul.s-result-list.s-col-1 li.s-result-item{width:100%;*width:99.94792%}.a-ws ul.s-result-list.s-col-ws-2 li.s-result-item,ul.s-result-list.s-col-2 li.s-result-item{width:50%;*width:49.94792%}.a-ws ul.s-result-list.s-col-ws-2 li.s-result-item.s-col-span-2,ul.s-result-list.s-col-2 li.s-result-item.s-col-span-2{width:100%;*width:99.94792%}.a-ws ul.s-result-list.s-col-ws-3 li.s-result-item,ul.s-result-list.s-col-3 li.s-result-item{width:33.33333%;*width:33.28125%}.a-ws ul.s-result-list.s-col-ws-3 li.s-result-item.s-col-span-2,ul.s-result-list.s-col-3 li.s-result-item.s-col-span-2{width:66.66667%;*width:66.61458%}.a-ws ul.s-result-list.s-col-ws-4 li.s-result-item,ul.s-result-list.s-col-4 li.s-result-item{width:25%;*width:24.94792%}.a-ws ul.s-result-list.s-col-ws-4 li.s-result-item.s-col-span-2,ul.s-result-list.s-col-4 li.s-result-item.s-col-span-2{width:50%;*width:49.94792%}.a-ws ul.s-result-list.s-col-ws-5 li.s-result-item,ul.s-result-list.s-col-5 li.s-result-item{width:20%;*width:19.94792%}.a-ws ul.s-result-list.s-col-ws-5 li.s-result-item.s-col-span-2,ul.s-result-list.s-col-5 li.s-result-item.s-col-span-2{width:40%;*width:39.94792%}.a-ws ul.s-result-list.s-col-ws-6 li.s-result-item,ul.s-result-list.s-col-6 li.s-result-item{width:16.66667%;*width:16.61458%}.a-ws ul.s-result-list.s-col-ws-6 li.s-result-item.s-col-span-2,ul.s-result-list.s-col-6 li.s-result-item.s-col-span-2{width:33.33333%;*width:33.28125%}.a-ws ul.s-result-list.s-col-ws-7 li.s-result-item,ul.s-result-list.s-col-7 li.s-result-item{width:14.28571%;*width:14.23363%}.a-ws ul.s-result-list.s-col-ws-7 li.s-result-item.s-col-span-2,ul.s-result-list.s-col-7 li.s-result-item.s-col-span-2{width:28.57143%;*width:28.51935%}.a-ws ul.s-result-list.s-col-ws-8 li.s-result-item,ul.s-result-list.s-col-8 li.s-result-item{width:12.5%;*width:12.44792%}.a-ws ul.s-result-list.s-col-ws-8 li.s-result-item.s-col-span-2,ul.s-result-list.s-col-8 li.s-result-item.s-col-span-2{width:25%;*width:24.94792%}.a-ws ul.s-result-list.s-col-ws-9 li.s-result-item,ul.s-result-list.s-col-9 li.s-result-item{width:11.11111%;*width:11.05903%}.a-ws ul.s-result-list.s-col-ws-9 li.s-result-item.s-col-span-2,ul.s-result-list.s-col-9 li.s-result-item.s-col-span-2{width:22.22222%;*width:22.17014%}.a-ws ul.s-result-list.s-col-ws-10 li.s-result-item,ul.s-result-list.s-col-10 li.s-result-item{width:10%;*width:9.94792%}.a-ws ul.s-result-list.s-col-ws-10 li.s-result-item.s-col-span-2,ul.s-result-list.s-col-10 li.s-result-item.s-col-span-2{width:20%;*width:19.94792%}.a-ws ul.s-result-list.s-col-ws-11 li.s-result-item,ul.s-result-list.s-col-11 li.s-result-item{width:9.09091%;*width:9.03883%}.a-ws ul.s-result-list.s-col-ws-11 li.s-result-item.s-col-span-2,ul.s-result-list.s-col-11 li.s-result-item.s-col-span-2{width:18.18182%;*width:18.12973%}.a-ws ul.s-result-list.s-col-ws-12 li.s-result-item,ul.s-result-list.s-col-12 li.s-result-item{width:8.33333%;*width:8.28125%}.a-ws ul.s-result-list.s-col-ws-12 li.s-result-item.s-col-span-2,ul.s-result-list.s-col-12 li.s-result-item.s-col-span-2{width:16.66667%;*width:16.61458%}.s-result-list-hgrid.s-col-1 li:nth-child(1n+2) .s-item-container,.s-result-list-hgrid.s-col-10 li:nth-child(1n+11) .s-item-container,.s-result-list-hgrid.s-col-11 li:nth-child(1n+12) .s-item-container,.s-result-list-hgrid.s-col-12 li:nth-child(1n+13) .s-item-container,.s-result-list-hgrid.s-col-2 li:nth-child(1n+3) .s-item-container,.s-result-list-hgrid.s-col-3 li:nth-child(1n+4) .s-item-container,.s-result-list-hgrid.s-col-4 li:nth-child(1n+5) .s-item-container,.s-result-list-hgrid.s-col-5 li:nth-child(1n+6) .s-item-container,.s-result-list-hgrid.s-col-6 li:nth-child(1n+7) .s-item-container,.s-result-list-hgrid.s-col-7 li:nth-child(1n+8) .s-item-container,.s-result-list-hgrid.s-col-8 li:nth-child(1n+9) .s-item-container,.s-result-list-hgrid.s-col-9 li:nth-child(1n+10) .s-item-container{border-top:1px solid #DDD}.a-ws ul.s-result-list-hgrid.s-col-ws-1 .s-result-item .s-item-container,.a-ws ul.s-result-list-hgrid.s-col-ws-10 .s-result-item .s-item-container,.a-ws ul.s-result-list-hgrid.s-col-ws-11 .s-result-item .s-item-container,.a-ws ul.s-result-list-hgrid.s-col-ws-12 .s-result-item .s-item-container,.a-ws ul.s-result-list-hgrid.s-col-ws-2 .s-result-item .s-item-container,.a-ws ul.s-result-list-hgrid.s-col-ws-3 .s-result-item .s-item-container,.a-ws ul.s-result-list-hgrid.s-col-ws-4 .s-result-item .s-item-container,.a-ws ul.s-result-list-hgrid.s-col-ws-5 .s-result-item .s-item-container,.a-ws ul.s-result-list-hgrid.s-col-ws-6 .s-result-item .s-item-container,.a-ws ul.s-result-list-hgrid.s-col-ws-7 .s-result-item .s-item-container,.a-ws ul.s-result-list-hgrid.s-col-ws-8 .s-result-item .s-item-container,.a-ws ul.s-result-list-hgrid.s-col-ws-9 .s-result-item .s-item-container{border-top-width:0}.a-ws .s-result-list-hgrid.s-col-ws-1 li:nth-child(1n+2) .s-item-container,.a-ws .s-result-list-hgrid.s-col-ws-10 li:nth-child(1n+11) .s-item-container,.a-ws .s-result-list-hgrid.s-col-ws-11 li:nth-child(1n+12) .s-item-container,.a-ws .s-result-list-hgrid.s-col-ws-12 li:nth-child(1n+13) .s-item-container,.a-ws .s-result-list-hgrid.s-col-ws-2 li:nth-child(1n+3) .s-item-container,.a-ws .s-result-list-hgrid.s-col-ws-3 li:nth-child(1n+4) .s-item-container,.a-ws .s-result-list-hgrid.s-col-ws-4 li:nth-child(1n+5) .s-item-container,.a-ws .s-result-list-hgrid.s-col-ws-5 li:nth-child(1n+6) .s-item-container,.a-ws .s-result-list-hgrid.s-col-ws-6 li:nth-child(1n+7) .s-item-container,.a-ws .s-result-list-hgrid.s-col-ws-7 li:nth-child(1n+8) .s-item-container,.a-ws .s-result-list-hgrid.s-col-ws-8 li:nth-child(1n+9) .s-item-container,.a-ws .s-result-list-hgrid.s-col-ws-9 li:nth-child(1n+10) .s-item-container{border-top:1px solid #DDD}.s-result-list-vgrid .s-item-container{border-left:1px solid #DDD}.s-col-1 .s-result-list-vgrid:nth-child(1n+1) .s-item-container,.s-col-10 .s-result-list-vgrid:nth-child(10n+1) .s-item-container,.s-col-11 .s-result-list-vgrid:nth-child(11n+1) .s-item-container,.s-col-12 .s-result-list-vgrid:nth-child(12n+1) .s-item-container,.s-col-2 .s-result-list-vgrid:nth-child(2n+1) .s-item-container,.s-col-3 .s-result-list-vgrid:nth-child(3n+1) .s-item-container,.s-col-4 .s-result-list-vgrid:nth-child(4n+1) .s-item-container,.s-col-5 .s-result-list-vgrid:nth-child(5n+1) .s-item-container,.s-col-6 .s-result-list-vgrid:nth-child(6n+1) .s-item-container,.s-col-7 .s-result-list-vgrid:nth-child(7n+1) .s-item-container,.s-col-8 .s-result-list-vgrid:nth-child(8n+1) .s-item-container,.s-col-9 .s-result-list-vgrid:nth-child(9n+1) .s-item-container{border-left-width:0}.a-ws ul.s-col-ws-1 li.s-result-list-vgrid div.s-item-container,.a-ws ul.s-col-ws-10 li.s-result-list-vgrid div.s-item-container,.a-ws ul.s-col-ws-11 li.s-result-list-vgrid div.s-item-container,.a-ws ul.s-col-ws-12 li.s-result-list-vgrid div.s-item-container,.a-ws ul.s-col-ws-2 li.s-result-list-vgrid div.s-item-container,.a-ws ul.s-col-ws-3 li.s-result-list-vgrid div.s-item-container,.a-ws ul.s-col-ws-4 li.s-result-list-vgrid div.s-item-container,.a-ws ul.s-col-ws-5 li.s-result-list-vgrid div.s-item-container,.a-ws ul.s-col-ws-6 li.s-result-list-vgrid div.s-item-container,.a-ws ul.s-col-ws-7 li.s-result-list-vgrid div.s-item-container,.a-ws ul.s-col-ws-8 li.s-result-list-vgrid div.s-item-container,.a-ws ul.s-col-ws-9 li.s-result-list-vgrid div.s-item-container{border-left:1px solid #DDD}.a-ws .s-col-ws-1 .s-result-list-vgrid:nth-child(1n+1) .s-item-container,.a-ws .s-col-ws-10 .s-result-list-vgrid:nth-child(10n+1) .s-item-container,.a-ws .s-col-ws-11 .s-result-list-vgrid:nth-child(11n+1) .s-item-container,.a-ws .s-col-ws-12 .s-result-list-vgrid:nth-child(12n+1) .s-item-container,.a-ws .s-col-ws-2 .s-result-list-vgrid:nth-child(2n+1) .s-item-container,.a-ws .s-col-ws-3 .s-result-list-vgrid:nth-child(3n+1) .s-item-container,.a-ws .s-col-ws-4 .s-result-list-vgrid:nth-child(4n+1) .s-item-container,.a-ws .s-col-ws-5 .s-result-list-vgrid:nth-child(5n+1) .s-item-container,.a-ws .s-col-ws-6 .s-result-list-vgrid:nth-child(6n+1) .s-item-container,.a-ws .s-col-ws-7 .s-result-list-vgrid:nth-child(7n+1) .s-item-container,.a-ws .s-col-ws-8 .s-result-list-vgrid:nth-child(8n+1) .s-item-container,.a-ws .s-col-ws-9 .s-result-list-vgrid:nth-child(9n+1) .s-item-container{border-left-width:0}li.s-result-item.s-item-placeholder.s-no-left .s-item-container{border-left:0!important}
	</style>
		
		
	</head>
	<body>
		<?php
			
			echo $maindata;
			echo "<br/>";
			echo $navdata;
			
		?>
	</body>
</html>





























