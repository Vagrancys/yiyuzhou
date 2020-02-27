<?php
namespace app\index\controller;
use think\Cache;
use think\Controller;
use think\Cookie;
use think\Db;
use think\Session;
use think\Image;
class Sitemap extends Comment
{
    public function sitemap2()
    {

        $mange=Db::table('manga')->where('manga_static',1)->select();
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
        $xml .=" <urlset>";
        $xmltpl = "<url>";
        $xmltpl .="<loc>{loc}</loc>";
        $xmltpl .=" <lastmod>{lastmod}</lastmod>";
        $xmltpl .=" <changefreq>daily</changefreq>";
        $xmltpl .=" <priority>{priority}</priority>";
        $xmltpl .="</url>";
        $patterns = array(
            '/{loc}/','/{lastmod}/','/{priority}/'
        );
        foreach($mange as $key=>$val){
            $replacements=array('http://www.yiyuzhou.com/main/'.$val['manga_id'],date("Y-m-d",$val['manga_time']),'0.9');
            $xml .=preg_replace($patterns,$replacements,$xmltpl);
        }
        $xml .="</urlset>";
        $file = fopen("sitemap2.xml","w");
        fwrite($file,$xml);
        fclose($file);
    }

    public function sitemap1()
    {
        $mange=Db::table('manga_part')->where('part_static',1)->select();
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
        $xml .=" <urlset>";
        $xmltpl = "<url>";
        $xmltpl .="<loc>{loc}</loc>";
        $xmltpl .=" <lastmod>{lastmod}</lastmod>";
        $xmltpl .=" <changefreq>daily</changefreq>";
        $xmltpl .=" <priority>{priority}</priority>";
        $xmltpl .="</url>";
        $patterns = array(
            '/{loc}/','/{lastmod}/','/{priority}/'
        );
        foreach($mange as $key=>$val){
            $replacements=array('http://www.yiyuzhou.com/play/'.$val['manga_part_id'],date("Y-m-d",$val['part_time']),'0.9');
            $xml .=preg_replace($patterns,$replacements,$xmltpl);
        }
        $xml .="</urlset>";
        $file = fopen("sitemap1.xml","w");
        fwrite($file,$xml);
        fclose($file);
    }


    public function sitemap()
    {
        $mange=array(
            '0'=>array('url'=>'sitemap1.xml','time'=>date("Y-m-d",time())),
            '1'=>array('url'=>'sitemap2.xml','time'=>date("Y-m-d",time())),
        );
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
        $xml .=" <sitemapindex>";
        $xmltpl = "<sitemap>";
        $xmltpl .="<loc>{loc}</loc>";
        $xmltpl .=" <lastmod>{lastmod}</lastmod>";
        $xmltpl .="</sitemap>";
        $patterns = array(
            '/{loc}/','/{lastmod}/'
        );
        foreach($mange as $key=>$val){
            $replacements=array('http://www.yiyuzhou.com/'.$val['url'],$val['time']);
            $xml .=preg_replace($patterns,$replacements,$xmltpl);
        }
        $xml .="</sitemapindex>";
        $file = fopen("sitemap.xml","w");
        fwrite($file,$xml);
        fclose($file);
    }
}
