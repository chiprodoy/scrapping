<?php
namespace App\Lib\Scrapper;

require "vendor/autoload.php";


class ElementType{
    const TEXT='text';
    const IMAGE='image';
    const LINK='link';

}

class ElementData{
    public $label;
    public $elementNode;
    public $elementType;

    public function __construct($label,$elementNode,$elementType=ElementType::TEXT)
    {   
        $this->label=$label;
        $this->elementNode=$elementNode;
        $this->elementType=$elementType;
        
    }
}

use Exception;
use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\DomCrawler\Crawler;
class Scrapper{

    private $url;
    private $data=array();
    private $client;
    private $waitFor;
    private $crawler ;
    private $result=array();
    private $resultPath='result';

    public function __construct($url,$port=9999,$waitFor='')
    {
        $this->waitFor=$waitFor;
        $_SERVER['PANTHER_NO_HEADLESS'] = true;
        $_SERVER['PANTHER_NO_SANDBOX'] = true;
        $_SERVER['PANTHER_DEVTOOLS'] = false;
        $this->client=Client::createChromeClient("drivers/chromedriver", null, ["port" => $port]);
        $this->crawler  = $this->client->request('GET', $url);
        $this->client->waitForVisibility($this->waitFor);  
        
        return $this;
    }


    public function setData(ElementData $elmData){
       array_push($this->data,$elmData);
        return $this;
    }

    public function setResultPath($resultPath){
        $this->resultPath=$resultPath;
        if(!is_dir($this->resultPath)){
            mkdir($this->resultPath);
        }
        return $this;
    }


    private function download(){

    }
    public function scrap(){
        $result=array();
        $data=$this->data;
        $this->crawler->filter('div.shop-search-result-view__item.col-xs-2-4')
        ->each(function(Crawler $parentCrawler, $i) use($data,&$result){
           
            foreach($data as $k => $v){
                   // echo $v->label.",".$v->elementNode.",".strpos($v->elementNode,'.');
                    try{
                        if($v->elementType==ElementType::IMAGE){
                          // echo "yy".$v->label.",".$v->elementNode.",".$parentCrawler->filter($v->elementNode)->getAttribute('src')."\r\t\n";

//                            echo $i.":".$v->elementNode."=".$parentCrawler->filter($v->elementNode)->getAttribute('src')."\r\t\n";
                           $result[$i][$v->label]=$parentCrawler->filter($v->elementNode)->getAttribute('src');
                           if(strlen($result[$i][$v->label]) > 0){
                                $fileName=basename($result[$i][$v->label]);
                                file_put_contents($this->resultPath."/".$fileName.".jpg", file_get_contents($result[$i][$v->label]));
                            }
                        }elseif($v->elementType==ElementType::LINK){
                            $result[$i][$v->label]=$parentCrawler->filter($v->elementNode)->getAttribute('href');
                        }elseif($v->elementType==ElementType::TEXT){
                            $result[$i][$v->label]=$parentCrawler->filter($v->elementNode)->text();
                        }  

                    }catch(Exception $e){
                        echo $v->elementNode."=".$e->getMessage();
                        $result[$i][$v->label]='';
                    }

            }
        });
        
        $this->result=$result;
        return $this;
        /*
        foreach($this->data as $k => $v){
            if(strpos($v->elementNode,'.')===0){
                echo $v->label.",".$v->elementNode.",".strpos($v->elementNode,'.');

                $this->crawler->filter($v->elementNode)->each(function(Crawler $parentCrawler, $i) use($v,&$result){
                    
                    $result[$i][$v->label]=$parentCrawler->text();
                   
                });
            }else{
                $result[][$v->label]=  $this->crawler->filter($v->elementNode)->text();
            }
 
           // $a=$this->crawler->filter($v->elementNode)->text();

        }        
        */                               // wait for the element with this css class until appear in DOM
       // print_r($result);
    }
    public function toCSV(){

        $row=array();
        $fp = fopen($this->resultPath.'/import_result.csv', 'a');

        foreach($this->result as $i =>$k){
            if($i==0)  fputcsv($fp, array_keys($k),";");
            fputcsv($fp,$k,";");

        }
       // fputcsv($fp,$row,";");
       // print_r($row);

    }
    
    private function cleanText($str){
        $keyword=[','];
        $replace=['.'];
        return str_replace($keyword,$replace,$str);
    }
    public function __destruct()
    {
        echo "Finished processing";
        $this->client->quit();
    }
 }


//print_r($s);
?>
