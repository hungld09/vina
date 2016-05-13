<?php

class ReportController extends ApiController {

	public function actionDailyReport(){
		$today = date('Y-m-d 00:00:00', time());
		$result = $this->getServiceSubsciberReport($today);
		//rieng voi general_report thi du lieu cua today la yesterday
		$yesterday = date('Y-m-d 00:00:00', time()-86400);
		$result .= $this->getContent_tk_san_luong_dich_vu($yesterday, $yesterday);
		$result .= $this->getContent_tk_doanh_thu_new($yesterday, $yesterday);
		echo $result;
	}
	
	public function getServiceSubsciberReport($today){
		$result = "<html><head><meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\"> </head><body>";
		$result .= "1. Số lượng thuê bao lũy kế";
		$result .= "<br></br>";
		$result .= "<table class=\"table table-striped table-bordered table-condensed table-hover\"; border = 1; cellpadding=0; cellspacing=1; width=70%; text-align:center>";
		$result .="<thead>";
            	$result .="<tr>";
    				$result .="<th style=\"text-align: center\">Ngày</th>"; 
    				$result .="<th style=\"text-align: center\">PHIM7</th>"; 
   				$result .="</tr>";
	        $result .="</thead>";
		$result .="<tbody id=\"data.body\">";
		//data here
		$subscriberUseService = ServiceSubscriberReport::model()->getReport_tb_luy_ke($today);
		$totalPhim = 0;
		if(count($subscriberUseService) > 0){
			$date = date('d/m/Y', strtotime($today));
			$result .= "<tr>";	
			$result .= "<td style=\"text-align: center\">".$date."</td>
			<td style=\"text-align: center\">".number_format($subscriberUseService['phim7'])."</td>";
			$result .= "</tr>";
		}
		$result .="</tbody>";
		$result .= "</table>";
		$result .= "<br></br>";
		$result .= "</body></html>";
		return $result;
	}
	
	private function getContent_tk_san_luong_dich_vu($from_date, $to_date) {
		$result = "<html><head><meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\"> </head><body>";
		$result .= "1. Sản lượng dịch vụ";
		$result .= "<br></br>";
		$result .= "<table class=\"table table-striped table-bordered table-condensed table-hover\"; border = 1; cellpadding=0; cellspacing=1; width=70%; text-align:center>";
		$result .="<thead>";
            	$result .="<tr>";
    				$result .="<th rowspan=\"3\" style=\"text-align: center;vertical-align:middle;\"  width=\"10%\">Ngày</th>";
    				$result .="<th colspan=\"8\" style=\"text-align: center\">PHIM7</th>"; 
    				$result .="<th rowspan=\"2\" colspan=\"2\" style=\"text-align: center; vertical-align:middle;\">Xem phim tính phí</th>"; 
    				$result .="<th rowspan=\"3\" style=\"text-align: center;vertical-align:middle;\"  width=\"10%\">Số thuê bao phát sinh cước</th>";
   				$result .="</tr>";
				$result .="<tr>";
    			$result .="<th colspan=\"2\" style=\"text-align: center\" width=\"6%\">Đăng kí</th>";
    			$result .="<th colspan=\"2\" style=\"text-align: center\" width=\"6%\">Gia hạn</th>";
    			 $result .="<th colspan=\"2\" style=\"text-align: center\" width=\"6%\">Hủy</th>";
    			 $result .="<th colspan=\"2\" style=\"text-align: center\" width=\"6%\">Truy Thu</th>";
    			$result .="</tr>";
				$result .="<tr>";
    				$result .="<th style=\"text-align: center\" width=\"6%\">Thành công</th>";
	    			 $result .="<th style=\"text-align: center\" width=\"6%\">Thất bại</th>";
	    			 $result .="<th style=\"text-align: center\" width=\"6%\">Thành công</th>";
	    			 $result .="<th style=\"text-align: center\" width=\"6%\">Thất bại</th>";
	    			 $result .="<th style=\"text-align: center\" width=\"6%\">Bị hủy</th>";
	    			 $result .="<th style=\"text-align: center\" width=\"6%\">Chủ động hủy</th>";
	    			 $result .="<th style=\"text-align: center\" width=\"6%\">Thành công</th>";
	    			 $result .="<th style=\"text-align: center\" width=\"6%\">Thất bại</th>";
	    			 $result .="<th style=\"text-align: center\" width=\"6%\">Thành công</th>";
	    			 $result .="<th style=\"text-align: center\" width=\"6%\">Thất bại</th>";
			 	$result .="</tr>";            
	        $result .="</thead>";
		$result .="<tbody id=\"data.body\">";
		//data here
		$generalReport = GeneralReport::model()->getReport_tk_san_luong_dich_vu($from_date, $to_date);
		$numberSubcriberCharging = 0;
		if(count($generalReport) > 0){
			for ($i = 0; $i < count($generalReport); $i++) {
				if($generalReport[$i]['service_id'] == SERVICE_2){
					$numberSubcriberCharging += $generalReport[$i]['register_success_count'] + $generalReport[$i]['extend_success_count'] + $generalReport[$i]['retry_extend_success_count'];
					$totalSubscriberCharging += $numberSubcriberCharging;
					$date = date('d/m/Y', strtotime($generalReport[$i]['report_date']));
					$result .= "<tr>";	
					$result .= "<td style=\"text-align: center\">".$date."</td>
					<td style=\"text-align: center\">".number_format($generalReport[$i]['register_success_count'])."</td>
					<td style=\"text-align: center\">".number_format($generalReport[$i]['register_fail_count'])."</td>
					<td style=\"text-align: center\">".number_format($generalReport[$i]['extend_success_count'])."</td>
					<td style=\"text-align: center\">".number_format($generalReport[$i]['extend_fail_count'])."</td>
					<td style=\"text-align: center\">".number_format($generalReport[$i]['auto_cancel_count'])."</td>
					<td style=\"text-align: center\">".number_format($generalReport[$i]['manual_cancel_count'])."</td>
					<td style=\"text-align: center\">".number_format($generalReport[$i]['retry_extend_success_count'])."</td>
					<td style=\"text-align: center\">".number_format($generalReport[$i]['retry_extend_failed_count'])."</td>";
				}
				if($generalReport[$i]['using_type'] == USING_TYPE_WATCH) {
					$numberSubcriberCharging += $generalReport[$i]['pay_per_video_success_count'];
					$totalSubscriberCharging += $generalReport[$i]['pay_per_video_success_count'];
					$totalPayPerSuccess += $generalReport[$i]['pay_per_video_success_count'];
					$totalPayPerFailed += $generalReport[$i]['pay_per_video_fail_count'];
					$result .= "<td style=\"text-align: center\">".number_format($generalReport[$i]['pay_per_video_success_count'])."</td>
						<td style=\"text-align: center\">".number_format($generalReport[$i]['pay_per_video_fail_count'])."</td>
						<td style=\"text-align: center\">".number_format($numberSubcriberCharging)."</td>";
					$result .= "</tr>";
					$numberSubcriberCharging = 0;
				}
			}
		}
		$result .="</tbody>";
		$result .= "</table>";
		$result .= "<br></br>";
		$result .= "</body></html>";
		return $result;
	}
	
	private function getContent_tk_doanh_thu_new($from_date, $to_date) {
		$result = "<html><head><meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\"> </head><body>";
		$result .= "3. Doanh thu";
		$result .= "<br></br>";
		$result .= "<table class=\"table table-striped table-bordered table-condensed table-hover\"; border = 1; cellpadding=0; cellspacing=1; width=70%; text-align:center>";
		$result .="<thead>";
            	$result .="<tr>";
    				$result .="<th rowspan=\"3\" style=\"text-align: center;vertical-align:middle;\"  width=\"6%\">Ngày</th>";
    				$result .="<th colspan=\"3\" style=\"text-align: center\">Doanh thu gói cước</th>"; 
    				$result .="<th rowspan=\"1\" style=\"text-align: center;vertical-align:middle;\"  width=\"12%\">Doanh thu bán lẻ</th>";
    				$result .="<th rowspan=\"3\" style=\"text-align: center;vertical-align:middle;\"  width=\"6%\">Tổng doanh thu</th>";
   				$result .="</tr>";
				$result .="<tr>";
    				$result .="<th colspan=\"3\" style=\"text-align: center\" width=\"6%\">PHIM7</th>";
    				$result .="<th rowspan=\"2\" style=\"text-align: center;vertical-align:middle;\" width=\"6%\">xem</th>";
    		$result .="</tr>";
				$result .="<tr>";
    				$result .="<th style=\"text-align: center\" width=\"6%\">ĐK mới</th>";
    				 $result .="<th style=\"text-align: center\" width=\"6%\">Gia hạn</th>";
    				 $result .="<th style=\"text-align: center\" width=\"6%\">Truy thu</th>";
			 	$result .="</tr>";            
	        $result .="</thead>";
	        $result .="<tbody id=\"data.body\">";
		$resultReport = Revenue::model()->report($from_date, $to_date);
		$lstRevenue= $resultReport['data'];
		$totalRegister = 0;
		$totalExtend = 0;
		$totalRetryExtend = 0;
		$totalAll = 0;
		$data = array();
		$content = array();
		$totalView = 0;
		for ($i = 0; $i < count($lstRevenue); $i++) {
				$date = date('d-m-Y', strtotime($lstRevenue[$i]['create_date']));
				if($lstRevenue[$i]['service_id'] == SERVICE_2) {
					$totalRegister += $lstRevenue[$i]['register'];
					$totalExtend += $lstRevenue[$i]['extend'];
					$totalRetryExtend += $lstRevenue[$i]['retry_extend'];
					$totalAll += $lstRevenue[$i]['register'] + $lstRevenue[$i]['extend'] + $lstRevenue[$i]['retry_extend'];
// 					echo $totalAll;
					$data = (array($date=>array($lstRevenue[$i]['register'], $lstRevenue[$i]['extend'], $lstRevenue[$i]['retry_extend'])));
					$content = array_merge_recursive($content,$data);
				}
				if($lstRevenue[$i]['service_id'] == null){
					$totalView += $lstRevenue[$i]['view'];
					$totalAll += $lstRevenue[$i]['view']; 
// 					echo $totalAll;
					$data = (array($date=>array($lstRevenue[$i]['view'])));
					$content = array_merge_recursive($content,$data);
				} 
				
		}
		foreach ($content as $key=>$value){
			$totalRevenue = $value[0] + $value[1] + $value[2] + $value[3];
			$result .= "<tr>";
			$result .= "<td style=\"text-align: center\">".$key."</td><td style=\"text-align: center\">".number_format($value[0]).
								"</td><td style=\"text-align: center\">".number_format($value[1]).
								"</td><td style=\"text-align: center\">".number_format($value[2]).
								"</td>"."<td style=\"text-align: center\">".number_format($value[3]).
								"</td>"."<td style=\"text-align: center\">".number_format($totalRevenue)."</td>";
			$result .= "</tr>";
		}
		$result .= "</table>";
		$result .= "</body></html>";
		return $result;
	}
}
?>