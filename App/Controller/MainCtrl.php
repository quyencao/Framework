<?php
/**
 * Created by PhpStorm.
 * User: LaptopTCC
 * Date: 7/26/2017
 * Time: 9:16 AM
 */

namespace App\Controller;

use App\Model\ArticleMapper;
use Lib\Controller;
use App\View\Layout\TwoColsLayout;
use App\Model\ArticleEntity;

class MainCtrl extends Controller
{
    protected $twoColsLayout;

    protected $articleMapper;

    function init()
    {
        $this->twoColsLayout = new TwoColsLayout($this->context);
        $viewDir = dirname(__DIR__) . '/View';
        $this->twoColsLayout->setTemplatesDirectory($viewDir);
        $this->articleMapper = new ArticleMapper();
    }

    public function index() {

        $rawData = array(
            'pk' => 1,
            'title' => 'title',
            'content' => 'content',
            'stt' => 1
        );

        $article = new ArticleEntity($rawData);

        $this->twoColsLayout->render('Main/index.phtml', array(
            'page_name' => 'Sample Page',
            'id' => $article->getUrl()
        ));
    }

    public function all() {
        $articles = $this->articleMapper->makeInstance()
                ->getAll();

        var_dump($articles);
    }



    public function test() {
        $articles = $this->articleMapper->makeInstance()
                ->countArticleInCategory();

        var_dump($articles);
//        $updateArticle = array(
//          'stt' => 0
//        );
//
//        $this->articleMapper->makeInstance()
//                    ->update(2, $updateArticle);

//        var_dump($articles);
    }
}