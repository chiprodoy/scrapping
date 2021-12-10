<?php
include "scrapper.php";
use App\Lib\Scrapper\ElementData;
use App\Lib\Scrapper\ElementType;
use App\Lib\Scrapper\Scrapper;

$e=new ElementData('nama_produk','._10Wbs-');
$e2=new ElementData('harga','.zp9xm9');
$e3=new ElementData('harga_diskon','._1d9_77');
$e4=new ElementData('diskon','._3yCxz- > span.percent');
$e5=new ElementData('terjual','._2VIlt8');
//$e6=new ElementData('gambar','div._25_r8I.ggJllv > img',ElementType::IMAGE);
$e6=new ElementData('gambar','._3-N5L6',ElementType::IMAGE);

//$disc=new ElementData('diskon',)

$s= new Scrapper('https://shopee.co.id/shop/267130746/search?page=8&sortBy=pop',9999,'.shopee-page-controller');
$s->setResultPath('result/shopee_267130746')
->setData($e)
->setData($e2)
->setData($e3)
->setData($e4)
->setData($e5)
->setData($e6)
->scrap()
->toCSV();
?>