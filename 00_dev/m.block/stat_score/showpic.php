<?php
include("class/pDraw.class.php");
include("class/pImage.class.php");
include("class/pData.class.php");
include("class/pPie.class.php");

$myData = new pData();

$myData->addPoints(array($_GET["a0"],$_GET["a1"],$_GET["a2"],$_GET["a3"],$_GET["a4"]));
$myData->addPoints(array("表現優異(".$_GET["a0"].")","表現良好(".$_GET["a1"].")", "已經做到(".$_GET["a2"].")", "還要加油(".$_GET["a3"].")", "努力改進(".$_GET["a4"].")"), "等第");
$myData->setAbscissa("等第");
$myPicture = new pImage(500,500,$myData);
$myPicture->setFontProperties(array("FontName"=>"fonts/NotoSansCJKtc-Regular.otf","FontSize"=>12));
$myPicture->drawRectangle(0,0,499,499);
$RectangleSettings = array("R"=>0,"G"=>0,"B"=>0);
$myPicture->drawFilledRectangle(0,0,500,25,$RectangleSettings);
$myPicture->drawText(200,22,"成績分布圖",array("R"=>255,"G"=>255,"B"=>255));
$PieChart = new pPie($myPicture,$myData);
$settings = array(
    "Radius"=>120, // 圓餅圖半徑
    "Border" => TRUE, // 區塊的框線
    "DrawLabels"=>TRUE, // 畫出標籤
    "WriteValues" => true, // 標示數值
    "ValuePosition" => PIE_VALUE_INSIDE, // 數值的位置
    );
$PieChart->draw2DPie(250,250, $settings);
#$myPicture->render('example_04.png');
header('Content-Type: image/png');
$myPicture->autoOutput();

?>
