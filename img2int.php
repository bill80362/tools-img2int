<?php
/**
 * 圖片轉成數字方式範例
 */

//引入curl
include "../include/class_curl.php";

//第一次curl取得要抓取的圖片位置
$curl = new CurlRequest();

//url找一個有圖片數字網站的範例
$params = array('url' => 'http://www.jlfc.com.cn/View/KS_WinInfo.aspx',
    'host' => "134.qq3196998.com",
    'header' => '',
    'method' => 'GET', // 'POST','HEAD'
    'referer' => '',
    'cookie' => '',
    'post_fields' => '', // 'var1=value&var2=value
    'timeout' => 20
);
$curl->init($params);
$result = $curl->exec();
$output = $result['body'];
$pattern = "<img src='(.*)' />";
preg_match_all($pattern, $output, $matches);

for($i=1;$i<count($matches);$i++){
    //去除雜質
    $matches[$i] = preg_replace('#imgsrc=\'#', "", $matches[$i]);
}

$img_src = 'http://www.jlfc.com.cn/View/';

for($i=0;$i<10;$i++){
    echo "get_image_url:".$img_src.$matches[1][$i]."<BR>";
    //下載圖片到tmp.png
    $params = array('url' => $img_src.$matches[1][$i],
        'host' => "134.qq3196998.com",
        'header' => '',
        'method' => 'GET', // 'POST','HEAD'
        'referer' => '',
        'cookie' => '',
        'post_fields' => '', // 'var1=value&var2=value
        'timeout' => 20
    );
    $curl->init($params);
    $result = $curl->exec();
    $output = $result['body'];
    $photoname = 'tmp/tmp.png';
    $fp = fopen($photoname, "w");
    fwrite($fp, $result['body']);
    fclose($fp);
    $res = imagecreatefrompng($photoname); //要注意圖片的檔案類型 這次是png可以改gif...等等
    $vailcode = DeimageCC2($res);
    echo "number:".$vailcode."<BR>";
}

function DeimageCC2($_img){
    //定義鎖定文字的像素 要注意上面第一排和左邊第一排都是文字開始點 會影響後續評分導致判斷文字
    $num[1] = array(
        array(1,1,1,1,0,0,0,0),
        array(1,1,1,0,0,0,0,0),
        array(0,0,1,0,0,0,0,0),
        array(0,0,1,0,0,0,0,0),
        array(0,0,1,0,0,0,0,0),
        array(0,0,1,0,0,0,0,0),
        array(0,0,1,0,0,0,0,0),
        array(1,1,1,1,1,0,0,0),
        array(1,1,1,1,1,0,0,0),
        array(0,0,0,0,0,0,0,0)
    );
    $num[2] = array(
        array(0,1,1,1,0,0,0,0),
        array(1,1,1,1,1,0,0,0),
        array(0,0,0,0,1,0,0,0),
        array(0,0,0,0,1,0,0,0),
        array(0,0,0,1,1,0,0,0),
        array(0,0,1,1,0,0,0,0),
        array(0,1,0,0,0,0,0,0),
        array(1,1,1,1,1,0,0,0),
        array(1,1,1,1,1,0,0,0),
        array(0,0,0,0,0,0,0,0)
    );
    $num[3] = array(
        array(0,1,1,1,0,0,0,0),
        array(1,1,1,1,1,0,0,0),
        array(0,0,0,0,1,0,0,0),
        array(0,1,1,1,0,0,0,0),
        array(0,1,1,1,1,0,0,0),
        array(0,0,0,0,1,0,0,0),
        array(0,0,0,0,1,0,0,0),
        array(1,1,1,1,1,0,0,0),
        array(1,1,1,1,0,0,0,0),
        array(0,0,0,0,0,0,0,0)
    );
    $num[4] = array(
        array(0,0,0,0,1,1,0,0),
        array(0,0,0,1,1,1,0,0),
        array(0,0,0,1,1,1,0,0),
        array(0,0,1,0,1,1,0,0),
        array(0,1,0,0,1,1,0,0),
        array(0,1,0,0,1,1,0,0),
        array(1,1,1,1,1,1,1,0),
        array(0,0,0,0,1,1,0,0),
        array(0,0,0,0,1,1,0,0),
        array(0,0,0,0,0,0,0,0)
    );
    $num[5] = array(
        array(1,1,1,1,1,0,0,0),
        array(1,1,1,1,1,0,0,0),
        array(1,1,0,0,0,0,0,0),
        array(1,1,1,1,0,0,0,0),
        array(1,1,1,1,1,0,0,0),
        array(0,0,0,0,1,0,0,0),
        array(0,0,0,0,1,0,0,0),
        array(1,1,1,1,1,0,0,0),
        array(1,1,1,1,0,0,0,0),
        array(0,0,0,0,0,0,0,0)
    );

    $num[6] = array(
        array(0,0,1,1,1,0,0),
        array(0,1,1,1,1,0,0),
        array(1,1,0,0,0,0,0),
        array(1,0,1,1,1,0,0),
        array(1,1,1,1,1,0,0),
        array(1,0,0,0,1,1,0),
        array(1,0,0,0,1,1,0),
        array(1,1,1,1,1,0,0),
        array(0,1,1,1,0,0,0),
        array(0,0,0,0,0,0,0)
    );

    //取的圖片寬跟高
    $imagex = imagesx($_img);
    $imagey = imagesy($_img);
    //exit();
    //
    for($j=0; $j <$imagey; ++$j){
        for($i=0; $i <$imagex; ++$i){
            $rgb = imagecolorat($_img,$i,$j);
            $rgbarray = imagecolorsforindex($_img, $rgb);
            //調整$rgbarray的三原色，讓背景與文字分離
            if($rgbarray['red'] >230  &&( $rgbarray['green']>230 || $rgbarray['blue']  >230) ){
                $data[$i][$j] = 1;
            }else{
                $data[$i][$j] = "";
            }
        }
    }

    //第一次先開啟這邊，調整$rgbarray的三原色，抓出$num[?]的排序方式放入二維陣列
    echo "<table>";
    for($j=0; $j <$imagey; ++$j){
        echo "<tr>";
        for($i=0; $i <$imagex; ++$i){
            echo "<td>".$data[$i][$j]."</td>";
        }
        echo "</tr>";
    }
    echo "</table>";

    $numstart = 0;
    $c = 0;
    for($i=0;$i<$imagex; ++$i){
        $column = 0;
        for($j=0; $j <$imagey; ++$j){
            if($data[$i][$j]==1) $column = 1;
        }
        if($column==1 && !$numstart){
            $numstart=1;
            $numarr[$c]['Xstart'] = $i;
        }

        if($column==0 && $numstart){
            $numstart=0;
            $numarr[$c]['Xend'] = $i-1;
            $c++;
        }
    }
    //這邊跑完
    for($numc=0; $numc <count($numarr); ++$numc){
        $numstart = 0;
        for($j=0; $j <$imagey; ++$j){
            $column = 0;
            for($i=$numarr[$numc]['Xstart']; $i <=$numarr[$numc]['Xend']; ++$i){
                if($data[$i][$j]==1) $column = 1;
            }
            if($column==1 && !$numstart){
                $numstart=1;
                $numarr[$numc]['Ystart'] = $j;
            }

            if($column==0 && $numstart){
                $numstart=0;
                $numarr[$numc]['Yend'] = $j-1;
            }
        }
    }
    //echo "Total字數:".$numc;//

    //開始比較 累計從每個字 Xstart->Xend(Ystart->Yend) 比對陣列的積分
    for($numc=0; $numc <count($numarr); ++$numc){
        for($i=0; $i <10; ++$i){
            for($j=0; $j <10; ++$j){
                $xvalue = $i+$numarr[$numc]['Xstart'];
                $yvalue = $j+$numarr[$numc]['Ystart'];
                if($data[$xvalue][$yvalue]==$num[0][$j][$i]) $compnumarr[$numc][0]++;
                if($data[$xvalue][$yvalue]==$num[1][$j][$i]) $compnumarr[$numc][1]++;
                if($data[$xvalue][$yvalue]==$num[2][$j][$i]) $compnumarr[$numc][2]++;
                if($data[$xvalue][$yvalue]==$num[3][$j][$i]) $compnumarr[$numc][3]++;
                if($data[$xvalue][$yvalue]==$num[4][$j][$i]) $compnumarr[$numc][4]++;
                if($data[$xvalue][$yvalue]==$num[5][$j][$i]) $compnumarr[$numc][5]++;
                if($data[$xvalue][$yvalue]==$num[6][$j][$i]) $compnumarr[$numc][6]++;
                if($data[$xvalue][$yvalue]==$num[7][$j][$i]) $compnumarr[$numc][7]++;
                if($data[$xvalue][$yvalue]==$num[8][$j][$i]) $compnumarr[$numc][8]++;
                if($data[$xvalue][$yvalue]==$num[9][$j][$i]) $compnumarr[$numc][9]++;

            }
        }
        //積分最高 確認該字
        $tmpmax=0;
        $tmpmaxvalue=0;
        for($count=1;$count<=10;$count++){
            if($compnumarr[$numc][$count]>$tmpmaxvalue){
                $tmpmax = $count;
                $tmpmaxvalue = $compnumarr[$numc][$count];
            }
        }
        $finalnum .= $tmpmax;
    }
    return $finalnum;
}